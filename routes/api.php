<?php

use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Source\SourceController;
use App\Http\Controllers\User\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('verify-api-key')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/authors', [ArticleController::class, 'getAuthors']);

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('sources', [SourceController::class, 'index']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::prefix('preferences')->group(function () {
            Route::controller(UserPreferenceController::class)->group(function () {
                Route::get('/', 'index');
                Route::put('/', 'update');
                Route::post('/add', 'addItem');
                Route::delete('/remove', 'removeItem');
            });
        });

        Route::get('/feed', [UserPreferenceController::class, 'feed']);
    });
});

