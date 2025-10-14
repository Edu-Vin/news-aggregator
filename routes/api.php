<?php

use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Source\SourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/authors', [ArticleController::class, 'getAuthors']);

Route::get('categories', [CategoryController::class, 'index']);
Route::get('sources', [SourceController::class, 'index']);
