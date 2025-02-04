<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name("login");

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/current', [App\Http\Controllers\Api\UserController::class, 'current']);
    Route::patch('/users/current', [App\Http\Controllers\Api\UserController::class, 'update']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
});

Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);
Route::get("/category",\App\Http\Controllers\CategoryController::class);
//Route::apiResource('/post-images', App\Http\Controllers\Api\PostImageController::class);
//Route::apiResource('/feedback', App\Http\Controllers\Api\FeedbackController::class);
