<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorStoreRequest;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;
use \Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $result = null;
        if ($request->input('get_all') && $request->input('get_all') == 1) {
            $result = Author::all();
        } else {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');

            $authors = Author::search($search)->orderBy('name', 'asc')->paginate($perPage);
            $authors->load('books');
            $result = new AuthorCollection($authors);
        }

        return $this->successResponse('Successful request', $result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthorStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Author::class);


        $validatedData = $request->validated();
        $author = Author::create($validatedData);


        return $this->successResponse('Author created successfully', new AuthorResource($author), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $author = Author::findOrFail($id);
            $author->load('books');
            return $this->successResponse('Success request', ['author' => new AuthorResource($author)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Author not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthorStoreRequest $request, string $id): JsonResponse
    {
        try {
            $author = Author::findOrFail($id);
            $this->authorize('update', $author);

            $author->update($request->validated());

            return $this->successResponse('Author updated successfully', ['author' => new AuthorResource($author)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Author not found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $author = Author::findOrFail($id);
            $this->authorize('delete', Author::class);

            $author->delete();

            return $this->successResponse('Author has been removed', []);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Author not found', 404);
        }
    }
}
