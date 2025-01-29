<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name("login");

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/current', [App\Http\Controllers\Api\UserController::class, 'current']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
});

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
//Route::apiResource('/users', App\Http\Controllers\Api\UserController::class);
Route::apiResource('/categories', App\Http\Controllers\Api\CategoryController::class);
Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);
Route::apiResource('/post-images', App\Http\Controllers\Api\PostImageController::class);
Route::apiResource('/feedback', App\Http\Controllers\Api\FeedbackController::class);
