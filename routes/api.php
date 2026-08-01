<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/create-user', [AdminController::class, 'create_user']);
Route::post('/login', [UserController::class, 'login']);

// Categories
Route::get('/view-category', [CategoryController::class, 'index']);
Route::get('/category-page', [ArticleController::class, 'categoryPage']);
Route::get('/category/{slug}', [ArticleController::class, 'categoryArticles']);

// Articles
Route::get('/articles', [ArticleController::class, 'articles']);
Route::get('/view-article', [ArticleController::class, 'index']);
Route::get('/article/{slug}', [ArticleController::class, 'singleArticle']);
Route::get('/trending-articles', [ArticleController::class, 'trending']);
Route::get('/latest-news', [ArticleController::class, 'latestNews']);
Route::get('/featured-news', [ArticleController::class, 'featuredNews']);


/*
|--------------------------------------------------------------------------
| Protected Routes (Admin Only)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/logout', [UserController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    Route::post('/create-category', [CategoryController::class, 'create']);
    Route::get('/edit-category/{id}', [CategoryController::class, 'edit']);
    Route::post('/update-category/{id}', [CategoryController::class, 'update']);
    Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Articles
    |--------------------------------------------------------------------------
    */

    Route::post('/create-article', [ArticleController::class, 'create']);
    Route::post('/upload-image', [ArticleController::class, 'uploadImage']);
    Route::get('/edit-article/{id}', [ArticleController::class, 'edit']);
    Route::post('/update-article/{id}', [ArticleController::class, 'update']);
    Route::delete('/delete-article/{id}', [ArticleController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Tags
    |--------------------------------------------------------------------------
    */

    Route::post('/create-tag', [TagController::class, 'create']);
    Route::get('/view-tag', [TagController::class, 'index']);
    Route::get('/edit-tag/{id}', [TagController::class, 'edit']);
    Route::post('/update-tag/{id}', [TagController::class, 'update']);
    Route::delete('/delete-tag/{id}', [TagController::class, 'destroy']);

});