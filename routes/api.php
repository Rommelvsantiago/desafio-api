<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\Api\AuthController;

// Rotas de autenticação
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::get('auth/me', [AuthController::class, 'me']);

// Grupo de rotas protegidas por autenticação JWT
Route::middleware('auth:api')->group(function () {
    // Notes
    Route::get('notes', [NotesController::class, 'index']);
    Route::post('notes', [NotesController::class, 'store']);
    Route::put('notes/{id}', [NotesController::class, 'update']);
    Route::delete('notes/{id}', [NotesController::class, 'destroy']);

    // Categories
    Route::get('categories', [CategoriesController::class, 'index']);
    Route::post('categories', [CategoriesController::class, 'store']);
    Route::put('categories/{id}', [CategoriesController::class, 'update']);
    Route::delete('categories/{id}', [CategoriesController::class, 'destroy']);
});
