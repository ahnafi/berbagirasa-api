<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index() {
       $users = User::all();

       return new UserResource('success', 'Data fetched successfully', $users);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone|min:10|max:15',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return new UserResource('error', $validator->errors(), null);
        }

        $request->merge([
            'password' => bcrypt($request->password)
        ]);
        $user = User::create($request->all());

        return new UserResource('success', 'User created successfully', $user);
    }
}
