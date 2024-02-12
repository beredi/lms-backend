<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use \Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $users = User::all();

        return $this->successResponse('Successful request', ['users' => new UserCollection($users)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $this->authorize('view', $user);

            return $this->successResponse('Success request', ['user' => new UserResource($user)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('User not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $this->authorize('update', $user);

            $user->update($request->validated());

            return $this->successResponse('User updated successfully', ['user' => new UserResource($user)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('User not found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $this->authorize('delete', User::class);

            $user->delete();

            return $this->successResponse('User has been removed', []);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('User not found', 404);
        }
    }
}
