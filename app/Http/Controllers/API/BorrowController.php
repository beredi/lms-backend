<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Borrow;
use App\Http\Resources\BorrowCollection;
use App\Http\Resources\BorrowResource;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BorrowController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Borrow::class);

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $borrows = Borrow::where('user_id', 'like', "%{$search}%")
            ->orWhere('book_id', 'like', "test%")
            // You can add more conditions based on your requirements

            ->paginate($perPage);

        $borrows->load(['user', 'book']); // Assuming you have relationships defined in the Borrow model

        return $this->successResponse('Successful request', new BorrowCollection($borrows));
    }

    /**
     *
     */
    public function reserveBook(Request $request): JsonResponse
    {
        $bookId = $request->input('book_id');
        $userId = $request->input('user_id');

        try {
            User::findOrFail($userId);
            Book::findOrFail($bookId);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User or book not found.', 404);
        }

        $existingBorrow = Borrow::where('book_id', $bookId)
            ->whereNull('returned')
            ->first();

        if ($existingBorrow) {
            return $this->errorResponse('This book is not available for reserve.', 400);
        }

        $reservation = Borrow::create([
            'user_id' => $userId,
            'book_id' => $bookId,
            'reserved' => now(),
        ]);

        return $this->successResponse('Book reserved successfully', new BorrowResource($reservation), 201);
    }

    /**
     *
     */
    public function borrowBook(Request $request): JsonResponse
    {
        $this->authorize('create', Borrow::class);

        $bookId = $request->input('book_id');
        $userId = $request->input('user_id');

        try {
            $user = User::findOrFail($userId);
            $book = Book::findOrFail($bookId);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User or book not found.', 404);
        }

        $existingBorrow = Borrow::where('book_id', $bookId)
            ->whereNull('returned')
            ->first();

        $deadline = now()->addMonth();

        if ($existingBorrow) {
            if ($existingBorrow->borrowed !== null) {
                return $this->errorResponse('This book is already borrowed and not returned.', 400);
            }
            if ($existingBorrow->reserved !== null && $existingBorrow->user_id !== $userId) {
                return $this->errorResponse('This book is reserved by another user.', 400);
            }

            $existingBorrow->borrowed = now();
            $existingBorrow->deadline = $deadline;
            $existingBorrow->save();
        } else {
            $existingBorrow = Borrow::create([
                'user_id' => $userId,
                'book_id' => $bookId,
                'borrowed' => now(),
                'deadline' => $deadline,
            ]);
        }

        return $this->successResponse('Book borrowed successfully', new BorrowResource($existingBorrow), 201);
    }

    public function returnBook(Request $request): JsonResponse
    {
        $this->authorize('update', Borrow::class);

        $bookId = $request->input('book_id');
        $userId = $request->input('user_id');

        try {
            $existingBorrow = Borrow::where('book_id', $bookId)
                ->where('user_id', $userId)
                ->whereNotNull('borrowed')
                ->whereNull('returned')
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Borrow record not found.', 404);
        }

        // Update the returned timestamp
        $existingBorrow->returned = now();
        $existingBorrow->save();

        return $this->successResponse('Book returned successfully', new BorrowResource($existingBorrow), 200);
    }

    public function returnById($borrowId): JsonResponse
    {
        try {
            $existingBorrow = Borrow::findOrFail($borrowId);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Borrow record not found.', 404);
        }

        if ($existingBorrow->returned !== null) {
            return $this->errorResponse('This book is already returned.', 400);
        }

        $existingBorrow->returned = now();
        $existingBorrow->save();

        return $this->successResponse('Book returned successfully', new BorrowResource($existingBorrow), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $borrowId)
    {
        try {
            $borrow = Borrow::findOrFail($borrowId);
            $this->authorize('delete', $borrow);

            if ($borrow->reserved !== null && $borrow->returned === null && $borrow->returned === null) {
                $borrow->delete();
            } else {
                return $this->errorResponse('Book was borrowed or returned', 400);
            }

            return $this->successResponse('Reservation was successfully canceled', []);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Borrow record not found', 404);
        }
    }
}
