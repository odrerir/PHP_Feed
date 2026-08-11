<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\SearchController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/home', [PostController::class, 'feedWithSuggestions']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/users/{username}', [ProfileController::class, 'showByUsername']);

    Route::post('/users/{username}/follow', [FollowController::class, 'follow']);
    Route::delete('/users/{username}/follow', [FollowController::class, 'unfollow']);
    Route::get('/users/{username}/posts', [PostController::class, 'byUser']);
    
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    Route::post('/posts/{post}/like', [LikeController::class, 'store']);
    Route::delete('/posts/{post}/like', [LikeController::class, 'destroy']);

    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);

    Route::get('/search', [SearchController::class, 'index']);
});
