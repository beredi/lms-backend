<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Models\Book;

class Author extends Model
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];


    /**
     * Permissions for User
     */
    private static $permissons = [
        'VIEW_AUTHOR' => 'VIEW_AUTHOR',
        'VIEW_AUTHORS' => 'VIEW_AUTHORS',
        'CREATE_AUTHOR' => 'CREATE_AUTHOR',
        'EDIT_AUTHOR' => 'EDIT_AUTHOR',
        'DELETE_AUTHOR' => 'DELETE_AUTHOR'
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
     * Author has many books
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'authors_books',  'author_id', 'book_id');
    }

    /**
     * Field for full-text search
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
