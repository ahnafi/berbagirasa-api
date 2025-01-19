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
     * @return UserResource
     */
    public function index() : UserResource {
       $users = User::all();

       return new UserResource('success', 'Data fetched successfully', $users);
    }

    /**
     * create
     *
     * @param  mixed $request
     * @return UserResource
     */
    public function store(Request $request) : UserResource {
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

    /**
     * show
     *
     * @param  mixed $id
     * @return UserResource
     */
    public function show($id) : UserResource {
        $user = User::find($id);

        if (!$user) {
            return new UserResource('error', 'User not found', null);
        }

        return new UserResource('success', 'User fetched successfully', $user);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return UserResource
     */
    /* public function update(Request $request, $id) : UserResource
    {
        $user = User::find($id);

        if (!$user) {
            return new UserResource('error', 'User not found', null);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|unique:users,phone,' . $id . '|min:10|max:15',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return new UserResource('error', $validator->errors(), null);
        }

        $request->merge([
            'password' => bcrypt($request->password)
        ]);
        $user->update($request->all());

        return new UserResource('success', 'User updated successfully', $user);
    } */

    /**
     * destroy
     *
     * @param  mixed $id
     * @return UserResource
     */
    public function destroy($id) : UserResource
    {
        $user = User::find($id);

        if (!$user) {
            return new UserResource('error', 'User not found', null);
        }

        $user->delete();
        return new UserResource('success', 'User deleted successfully', null);
    }
}
