<?php

namespace App\Http\Controllers\API;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $books = Book::search($search)->paginate($perPage);
        $books->load(['authors', 'categories']);

        return $this->successResponse('Successful request', new BookCollection($books));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookStoreRequest $request)
    {
        $this->authorize('create', Book::class);

        $validatedData = $request->validated();
        $book = Book::create($validatedData);

        $book->authors()->sync($validatedData['authors']);

        return $this->successResponse('Book created successfully', null, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->load(['authors', 'categories']);
            return $this->successResponse('Success request', ['book' => new BookResource($book)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Book not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookUpdateRequest $request, string $id)
    {
        try {
            $book = Book::findOrFail($id);
            $this->authorize('update', $book);

            $book->update($request->validated());
            if ($request->input('authors')) {
                $book->authors()->sync($request->validated()['authors']);
            }

            if ($request->input('categories')) {
                $book->categories()->sync($request->validated()['categories']);
            }

            $book->load(['authors', 'categories']);
            return $this->successResponse('Book updated successfully', ['book' => new BookResource($book)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Book not found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $book = Book::findOrFail($id);
            $this->authorize('delete', Book::class);
            $book->delete();

            return $this->successResponse('Book has been removed', []);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Book not found', 404);
        }
    }
}
