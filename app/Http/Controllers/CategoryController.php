<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use \Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $result = null;
        if ($request->input('get_all') && $request->input('get_all') == 1) {
            $result = Category::all();
        } else {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');

            $categories = Category::search($search)
                ->orderBy('name', 'asc')
                ->paginate($perPage);
            $categories->load('books');
            $result = new CategoryCollection($categories);
        }

        return $this->successResponse('Successful request', $result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);


        $validatedData = $request->validated();
        $category = Category::create($validatedData);


        return $this->successResponse('Category created successfully', new CategoryResource($category), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->load('books');
            return $this->successResponse('Success request', ['category' => new CategoryResource($category)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Category not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryStoreRequest $request, string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $this->authorize('update', $category);

            $category->update($request->validated());

            return $this->successResponse('Category updated successfully', ['category' => new CategoryResource($category)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Category not found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $this->authorize('delete', Category::class);

            $category->delete();

            return $this->successResponse('Category has been removed', []);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Category not found', 404);
        }
    }
}
