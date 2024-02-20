<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Models\Author;
use App\Models\Borrow;
use Laravel\Scout\Attributes\SearchUsingPrefix;

class Book extends Model
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'book_id',
        'title',
        'description',
        'pages',
        'year'
    ];

    /**
     * Permissions for User
     */
    private static $permissons = [
        'VIEW_BOOK' => 'VIEW_BOOK',
        'VIEW_BOOKS' => 'VIEW_BOOKS',
        'CREATE_BOOK' => 'CREATE_BOOK',
        'EDIT_BOOK' => 'EDIT_BOOK',
        'DELETE_BOOK' => 'DELETE_BOOK'
    ];

    /**
     * Get permissions for model
     * @return array
     */
    public static function getModelPermissions(): array
    {
        return self::$permissons;
    }

    /**
     * Book has many authors
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'authors_books', 'book_id', 'author_id');
    }

    /**
     * Book has many categories
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'books_categories',  'book_id', 'category_id');
    }

    /**
     * Field for full-text search
     */
    #[SearchUsingPrefix(['book_id'])]
    public function toSearchableArray(): array
    {
        $this->load('authors'); // Ensure authors are loaded

        $authors = $this->authors->pluck('name')->toArray(); // Get author names

        return [
            'book_id' => $this->book_id,
            'title' => $this->title,
            // 'authors' => $authors, // Include authors in the searchable data
        ];
    }

    /**
     *
     */
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        $borrowed = $this->borrows()
            ->whereNull('returned')
            ->whereNotNull('borrowed')
            ->count();

        $reserved = $this->borrows()
            ->whereNull('returned')
            ->whereNull('borrowed')
            ->count();

        if ($borrowed > 0) {
            return 'Borrowed';
        } elseif ($reserved > 0) {
            return 'Reserved';
        } else {
            return 'Available';
        }
    }



    /**
     * Get only reserved re.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getReserved()
    {
        return $this->borrows()
            ->whereNull('returned')
            ->whereNull('borrowed');
    }



    /**
     * Get only borrowed records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getBorrowed()
    {
        return $this->borrows()
            ->whereNull('returned')
            ->whereNotNull('borrowed');
    }





    /**
     * Get only returned records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getReturned()
    {
        return $this->borrows()
            ->whereNotNull('returned');
    }
}
