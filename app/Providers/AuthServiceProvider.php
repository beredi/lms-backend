<?php

namespace App\Providers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Category;
use App\Models\Payment;
use App\Models\User;
use App\Policies\AuthorPolicy;
use App\Policies\BookPolicy;
use App\Policies\BorrowPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Book::class => BookPolicy::class,
        Author::class => AuthorPolicy::class,
        User::class => UserPolicy::class,
        Category::class => CategoryPolicy::class,
        Borrow::class => BorrowPolicy::class,
        Payment::class => PaymentPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->isAdmin() ? true : null;
        });
    }
}
