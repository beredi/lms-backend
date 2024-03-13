<?php

use App\Http\Controllers\API\AuthorController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\BorrowController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'index']);
    Route::get('/{id}', [BookController::class, 'show']);
    Route::get('/category/{categoryId}', [BookController::class, 'getByCategory']);
    Route::get('/author/{authorId}', [BookController::class, 'getByAuthor']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [BookController::class, 'store']);
        Route::put('/{id}', [BookController::class, 'update']);
        Route::delete('/{id}', [BookController::class, 'destroy']);
        Route::post('/reserve', [BorrowController::class, 'reserveBook']);
        Route::post('/borrow', [BorrowController::class, 'borrowBook']);
        Route::post('/return', [BorrowController::class, 'returnBook']);
        Route::get('/return-by-id/{borrowId}', [BorrowController::class, 'returnById']);
        Route::get('/borrows-by-book/{bookId}', [BookController::class, 'getBorrowsByBook']);
        Route::get('/cancel-reservation/{borrowId}', [BorrowController::class, 'destroy']);
        Route::get('export/all', [BookController::class, 'exportBooks']);
    });
});

Route::prefix('authors')->group(function () {
    Route::get('/', [AuthorController::class, 'index']);
    Route::get('/{id}', [AuthorController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [AuthorController::class, 'store']);
        Route::put('/{id}', [AuthorController::class, 'update']);
        Route::delete('/{id}', [AuthorController::class, 'destroy']);
    });
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });
});
