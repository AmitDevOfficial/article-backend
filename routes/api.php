<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TagController;

// Public Routes
Route::post('/create-user', [AdminController::class, 'create_user']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/view-category', [CategoryController::class, 'index']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/create-category', [CategoryController::class, 'create']);
    Route::get('/edit-category/{id}', [CategoryController::class, 'edit']);
    Route::post('/update-category/{id}', [CategoryController::class, 'update']);
    Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy']);

    /*---Articles---*/
    Route::get('/articles', [ArticleController::class, 'articles']);
    Route::get('/category-page', [ArticleController::class,'categoryPage']);
    Route::post('/create-article',[ArticleController::class,'create']);
    Route::post('/upload-image', [ArticleController::class, 'uploadImage']);
    Route::get('/view-article',[ArticleController::class,'index']);
    Route::get('/trending-articles', [ArticleController::class, 'trending']);
    Route::get('/latest-news', [ArticleController::class, 'latestNews']);
    Route::get('/featured-news', [ArticleController::class, 'featuredNews']);
    Route::get('/article/{slug}', [ArticleController::class,'singleArticle']);
    Route::get('/edit-article/{id}',[ArticleController::class,'edit']);
    Route::post('/update-article/{id}',[ArticleController::class,'update']);
    Route::delete('/delete-article/{id}', [ArticleController::class,'destroy']);
    Route::get('/category/{slug}', [ArticleController::class, 'categoryArticles']);

    // Tags
    Route::post('/create-tag', [TagController::class, 'create']);
    Route::get('/view-tag', [TagController::class, 'index']);
    Route::get('/edit-tag/{id}', [TagController::class, 'edit']);
    Route::post('/update-tag/{id}', [TagController::class, 'update']);
    Route::delete('/delete-tag/{id}', [TagController::class, 'destroy']);
});

