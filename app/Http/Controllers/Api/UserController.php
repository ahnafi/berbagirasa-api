<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function current(Request $request): UserResource
    {
        $user = Auth::user();

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();
        $user = Auth::user();

        if ($request->hasFile("photo")) {
            $data["photo"] = $request->file("photo")->store("profiles", "public");

            if (!empty($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
        }

        if (!$request->filled("password")) unset($data["password"]);
        $user->update($data);
        return new UserResource($user);
    }

}
