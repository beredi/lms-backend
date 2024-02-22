<?php

use App\Http\Controllers\API\BorrowController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('auth-user', [UserController::class, 'authUser']);
    Route::get('users/borrowed-by-user/{userId}', [UserController::class, 'getBorrowsByUser']);
    Route::get('users/export-borrows/{userId}', [BorrowController::class, 'exportForUser']);
    Route::post('users/pay', [PaymentController::class, 'payUser']);
    Route::get('user/payments/{userId}', [PaymentController::class, 'getPaymentsForUser']);
});
