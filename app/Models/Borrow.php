<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Book;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'reserved',
        'borrowed',
        'returned',
        'deadline',
    ];


    /**
     * Permissions for User
     */
    private static $permissons = [
        'VIEW_BORROW' => 'VIEW_BORROW',
        'VIEW_BORROWS' => 'VIEW_BORROWS',
        'CREATE_BORROW' => 'CREATE_BORROW',
        'EDIT_BORROW' => 'EDIT_BORROW',
        'DELETE_BORROW' => 'DELETE_BORROW'
    ];

    /**
     * Get permissions for model
     * @return array
     */
    public static function getModelPermissions(): array
    {
        return self::$permissons;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
