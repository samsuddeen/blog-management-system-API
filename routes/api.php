<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\PublicPostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/public/posts', [PublicPostController::class, 'index']);
Route::get('/categories/export', [CategoryController::class, 'export']);
Route::get('/posts/export', [PostController::class, 'export']);


Route::middleware('custom.auth')->group(function () {
    Route::apiResource('posts', PostController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::post('/categories/import', [CategoryController::class, 'import']);

    //optional(extra)
    Route::delete('/category/force-delete/{id}', [CategoryController::class, 'forceDelete']);
    Route::post('/category/restore/{id}', [CategoryController::class, 'restore']);
});
