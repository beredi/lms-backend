<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\Models\Book;

class Category extends Model
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
        'VIEW_CATEGORY' => 'VIEW_CATEGORY',
        'VIEW_CATEGORIES' => 'VIEW_CATEGORIES',
        'CREATE_CATEGORY' => 'CREATE_CATEGORY',
        'EDIT_CATEGORY' => 'EDIT_CATEGORY',
        'DELETE_CATEGORY' => 'DELETE_CATEGORY'
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
     * Category has many books
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'books_categories',  'category_id', 'book_id');
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
