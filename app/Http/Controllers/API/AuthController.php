<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use \Illuminate\Http\JsonResponse;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponseTrait;


    /**
     * Register new user
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'lastname' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'password' => bcrypt($request->input('password')),
        ]);

        $roles = $request->input('roles', ['user']);
        $user->syncRoles($roles);


        $token = $user->createToken('user' . $user->name . 'token')->plainTextToken;


        return $this->successResponse(
            'User created successfully',
            $this->customResponseData($user, $token),
            201
        );
    }

    /**
     * Login user
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('user' . $user->name . 'token')->plainTextToken;

            return $this->successResponse(
                'Logged in successfully',
                $this->customResponseData($user, $token),
                201
            );
        }
        return $this->errorResponse('The provided credentials are incorrect.', 401);
    }

    /**
     * Logout user
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return $this->successResponse('Logged out', []);
    }

    /**
     * Build data array for json response
     * @param User $user
     * @param $token
     * @return array
     */
    private function customResponseData(User $user, $token): array
    {
        return [
            'user' => new UserResource($user),
            'token' => $token
        ];
    }
}
