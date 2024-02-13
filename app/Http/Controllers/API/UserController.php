<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use \Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $perPage = $request->input('per_page', 10);
        $users = User::paginate($perPage);


        return $this->successResponse('Successful request', new UserCollection($users));
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

    /**
     * Get logged in user
     */
    public function authUser(): JsonResponse
    {
        try {
            $user = auth()->user();
            $user->auth = true;

            return $this->successResponse('Successfull request', ['user' => new UserResource($user)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {

            return $this->errorResponse('User not found', 404);
        }
    }
}
