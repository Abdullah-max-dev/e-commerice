<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\HomeController;
// Public routes
Route::post('/user-signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories-list', [CategoryController::class, 'index']);
Route::get('/popular-products', [HomeController::class, 'popularProducts']);
Route::post('/vender-signup', [AuthController::class, 'Vender_register']);



// Vender routes
Route::middleware(['auth:sanctum', 'vender'])->group(function () {
    Route::post('/vender/add-product', [ProductController::class, 'saveProduct']);
    Route::get('/vender/products', [ProductController::class, 'showProduct']);
    Route::delete('/vender/products/{id}', [ProductController::class, 'destroy']);
    Route::put('/vender/products/{id}', [ProductController::class, 'update']);
    Route::get('/vender/products/{id}', [ProductController::class, 'show']);
    Route::post('/vender/products/{id}/discount', [ProductController::class, 'storeDiscount']);
    Route::post('/logout', [AuthController::class, 'logout']);

});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::patch('categories/{id}/toggle-top', [CategoryController::class, 'toggleTop']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
