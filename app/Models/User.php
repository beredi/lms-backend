<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Borrow;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'phone',
        'address',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Permissions for User
     */
    private static $permissons = [
        'VIEW_USER' => 'VIEW_USER',
        'VIEW_USERS' => 'VIEW_USERS',
        'CREATE_USER' => 'CREATE_USER',
        'EDIT_USER' => 'EDIT_USER',
        'DELETE_USER' => 'DELETE_USER'
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
     * Check if user is administrator
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Field for full-text search
     */
    #[SearchUsingPrefix(['id'])]
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'email' => $this->email,
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
     * Get only reserved books for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getReservedBooks()
    {
        return $this->borrows()
            ->whereNull('returned')
            ->whereNull('borrowed');
    }

    /**
     * Get only borrowed books for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getBorrowedBooks()
    {
        return $this->borrows()
            ->whereNull('returned')
            ->whereNotNull('borrowed');
    }

    /**
     * Get only returned books for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getReturnedBooks()
    {
        return $this->borrows()
            ->whereNotNull('returned');
    }


    /**
     * Get payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if the user has made a payment in the current year.
     *
     * @return bool
     */
    public function didUserPaid(): bool
    {
        $currentYear = Carbon::now()->year;

        // Check if there is any payment made by the user in the current year
        return $this->payments()->whereYear('payment_date', $currentYear)->exists();
    }
}
