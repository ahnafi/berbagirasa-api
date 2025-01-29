<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password'])
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ((new AuthResource($user, $token)))->response()->setStatusCode(201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return (new AuthResource('error', $validator->errors(), null))
                ->response()
                ->setStatusCode(400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return (new AuthResource('error', 'The provided credentials are incorrect.', null))
                ->response()
                ->setStatusCode(401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return (new AuthResource('success', 'User logged in successfully', [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]))
            ->response()
            ->setStatusCode(200);
    }

    public function profile(Request $request): JsonResponse
    {
        return (new AuthResource('success', 'User profile retrieved successfully', $request->user()))
            ->response()
            ->setStatusCode(200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return (new AuthResource('success', 'User logged out successfully', null))
            ->response()
            ->setStatusCode(200);
    }
}
