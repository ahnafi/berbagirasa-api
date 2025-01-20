<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) : JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()) {
            return (new AuthResource('success', $validator->errors(), null))
                ->response()
                ->setStatusCode(400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return (new AuthResource('success', 'User registered successfully', [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]))
            ->response()
            ->setStatusCode(201);
    }

    public function login(Request $request) : JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()) {
            return (new AuthResource('error', $validator->errors(), null))
                ->response()
                ->setStatusCode(400);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
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

    public function profile(Request $request) : JsonResponse {
        return (new AuthResource('success', 'User profile retrieved successfully', $request->user()))
            ->response()
            ->setStatusCode(200);
    }

    public function logout(Request $request) : JsonResponse {
        $request->user()->currentAccessToken()->delete();

        return (new AuthResource('success', 'User logged out successfully', null))
            ->response()
            ->setStatusCode(200);
    }
}
