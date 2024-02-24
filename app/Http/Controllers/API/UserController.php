<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\BorrowCollection;
use App\Http\Resources\UserCollection;
use App\Notifications\RegisterUser;
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
        $search = $request->input('search');

        $search ? $users = User::search($search)->paginate($perPage) : $users = User::paginate($perPage);


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
     *
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = User::create([
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'password' => bcrypt($request->input('password')),
        ]);

        $roles = $request->input('roles', ['user']);
        $user->syncRoles($roles);

        $user->notify(new RegisterUser($user));
        return $this->successResponse(
            'User created successfully',
            $user,
            201
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $this->authorize('update', $user);
            if ($request->input('roles')) {
                $roles = $request->input('roles');
                $user->syncRoles($roles);
            }

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

    /**
     *
     */
    public function getBorrowsByUser(Request $request, $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            $perPage = $request->input('per_page', 10);
            $status = $request->input('status', 'returned');

            switch ($status) {
                case 'returned':
                    $borrows = $user->getReturnedBooks();
                    break;

                case 'borrowed':
                    $borrows = $user->getBorrowedBooks();
                    break;

                default:
                    $borrows = $user->getReservedBooks();
                    break;
            }


            return $this->successResponse('Successful request', new BorrowCollection($borrows->with(['user', 'book'])->orderBy('returned', 'desc')->paginate($perPage)));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->errorResponse('Borrow record not found', 404);
        }
    }
}
