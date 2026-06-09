<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TeaController;
use App\Http\Controllers\Api\TeaTypeController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/tea-types', [TeaTypeController::class, 'index']);
Route::get('/tea-types/{id}', [TeaTypeController::class, 'show']);
Route::get('/teas', [TeaController::class, 'index']);
Route::get('/teas/{id}', [TeaController::class, 'show']);
Route::get('/teas/{id}/comments', [CommentController::class, 'indexByTea']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/by-slug/{slug}', [ArticleController::class, 'showBySlug']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/teas/{id}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::middleware('admin')->group(function () {
        Route::post('/teas', [TeaController::class, 'store']);
        Route::put('/teas/{id}', [TeaController::class, 'update']);
        // 兼容 multipart 表单上传在部分环境下无法使用 PUT 的情况
        Route::post('/teas/{id}', [TeaController::class, 'update']);
        Route::delete('/teas/{id}', [TeaController::class, 'destroy']);
    });
});
