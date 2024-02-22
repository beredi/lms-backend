<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'value', 'payment_date'];

        /**
     * Permissions for User
     */
    private static $permissons = [
        'VIEW_PAYMENT' => 'VIEW_PAYMENT',
        'VIEW_PAYMENTS' => 'VIEW_PAYMENTS',
        'CREATE_PAYMENT' => 'CREATE_PAYMENT',
        'EDIT_PAYMENT' => 'EDIT_PAYMENT',
        'DELETE_PAYMENT' => 'DELETE_PAYMENT'
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
     * Payment has one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
