<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/user-signup', [AuthController::class, 'signup']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/vender-signup', [AuthController::class, 'Vender_register']);
Route::get('/categories-list', [CategoryController::class, 'index']);
Route::get('/popular-products', [HomeController::class, 'popularProducts']);
Route::get('/top-deals', [HomeController::class, 'topDeals']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/verification/submit', [AuthController::class, 'submitVerification']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Vender routes
Route::middleware(['auth:sanctum', 'vender'])->group(function () {
   Route::post('/vender/add-product', [ProductController::class, 'saveProduct'])->middleware('verified.account');
    Route::get('/vender/products', [ProductController::class, 'showProduct']);
    Route::delete('/vender/products/{id}', [ProductController::class, 'destroy']);
    Route::put('/vender/products/{id}', [ProductController::class, 'update']);
    Route::get('/vender/products/{id}', [ProductController::class, 'show']);
    Route::post('/vender/products/{id}/discount', [ProductController::class, 'storeDiscount'])->middleware('verified.account');

});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::patch('categories/{id}/toggle-top', [CategoryController::class, 'toggleTop']);
    Route::get('/admin/users', fn () => app(AuthController::class)->listByRole('user'));
    Route::get('/admin/venders', fn () => app(AuthController::class)->listByRole('vender'));
    Route::patch('/admin/users/{user}/verification', [AuthController::class, 'updateVerificationStatus']);
    Route::patch('/admin/venders/{user}/verification', [AuthController::class, 'updateVerificationStatus']);
});
