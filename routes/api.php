<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductCommentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VendorDashboardController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\UserProductReportController;
use App\Http\Controllers\Api\VendorProductReportController;
use App\Http\Controllers\Api\AdminProductReportController;
use App\Http\Controllers\Api\VendorMessageController;
use App\Http\Controllers\Api\OrderReportController;


// Public routes
Route::get('/admin-selected-categories', [HomeController::class, 'adminSelectedCategories']);
Route::get('/categories/{categoryId}/products', [HomeController::class, 'productsByCategory']);
Route::post('/user-signup', [AuthController::class, 'signup']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/vender-signup', [AuthController::class, 'Vender_register']);
Route::get('/categories-list', [CategoryController::class, 'index']);
Route::get('/popular-products', [HomeController::class, 'popularProducts']);
Route::get('/top-deals', [HomeController::class, 'topDeals']);
Route::get('/products/{id}', [HomeController::class, 'productDetail']);
Route::get('/products/{id}/related', [HomeController::class, 'relatedProducts']);
Route::get('/products/{id}/comments', [ProductCommentController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/verification/submit', [AuthController::class, 'submitVerification']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['role:user'])->group(function () {
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::patch('/cart/{id}', [CartController::class, 'update']);
        Route::delete('/cart/{id}', [CartController::class, 'destroy']);
        Route::delete('/cart', [CartController::class, 'clear']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'userOrders']);
        Route::post('/orders/{order}/report', [OrderReportController::class, 'store']);
        Route::get('/dashboard/summary', [UserDashboardController::class, 'summary']);
        Route::get('/dashboard/orders', [UserDashboardController::class, 'recentOrders']);
        Route::patch('/dashboard/billing-address', [UserDashboardController::class, 'updateBillingAddress']);
        Route::post('/products/{id}/comments', [ProductCommentController::class, 'store']);
        Route::post('/products/{id}/report', [UserProductReportController::class, 'store']);
        Route::get('/products/{id}/report-status', [UserProductReportController::class, 'status']);
         Route::get('/user/reports', [UserProductReportController::class, 'index']);
        Route::post('/user/reports/read/{id}', [UserProductReportController::class, 'markAsRead']);
    });
});

// Vender routes
Route::middleware(['auth:sanctum', 'role:vendor'])->group(function () {
   Route::post('/vender/add-product', [ProductController::class, 'saveProduct'])->middleware('verified.account');
    Route::get('/vender/products', [ProductController::class, 'showProduct']);
    Route::delete('/vender/products/{id}', [ProductController::class, 'destroy']);
    Route::put('/vender/products/{id}', [ProductController::class, 'update']);
    Route::get('/vender/products/{id}', [ProductController::class, 'show']);
    Route::post('/vender/products/{id}/discount', [ProductController::class, 'storeDiscount'])->middleware('verified.account');
    Route::get('/vender/orders', [OrderController::class, 'venderOrders']);
    Route::patch('/vender/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::get('/vender/dashboard/profile', [VendorDashboardController::class, 'profile']);
    Route::get('/vender/dashboard/stats', [VendorDashboardController::class, 'stats']);
    Route::get('/vender/dashboard/recent-orders', [VendorDashboardController::class, 'recentOrders']);
    Route::get('/vender/dashboard/products', [VendorDashboardController::class, 'products']);
    Route::get('/vender/dashboard/notifications', [VendorDashboardController::class, 'notifications']);
     Route::get('/vendor/reports', [VendorProductReportController::class, 'index']);
    Route::post('/vendor/reports/{id}/justify', [VendorProductReportController::class, 'justify']);
    Route::get('/vendor/messages', [VendorMessageController::class, 'index']);
    Route::post('/vendor/messages/read/{id}', [VendorMessageController::class, 'markAsRead']);
    Route::post('/vendor/messages/archive/{id}', [VendorMessageController::class, 'archive']);
    Route::delete('/vendor/messages/{id}', [VendorMessageController::class, 'destroy']);
    // backward-compatible typo paths
    Route::get('/vender/reports', [VendorProductReportController::class, 'index']);
    Route::post('/vender/reports/{id}/justify', [VendorProductReportController::class, 'justify']);

});

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::patch('categories/{id}/toggle-top', [CategoryController::class, 'toggleTop']);
    Route::get('/admin/users', fn () => app(AuthController::class)->listByRole('user'));
    Route::get('/admin/venders', fn () => app(AuthController::class)->listByRole('vender'));
    Route::patch('/admin/users/{user}/verification', [AuthController::class, 'updateVerificationStatus']);
    Route::patch('/admin/venders/{user}/verification', [AuthController::class, 'updateVerificationStatus']);
    Route::get('/admin/reports', [AdminProductReportController::class, 'index']);
    Route::put('/admin/reports/{id}', [AdminProductReportController::class, 'update']);
    Route::post('/admin/reports/warn/{vendor_id}', [AdminProductReportController::class, 'warnVendor']);
    Route::get('/admin/order-reports', [OrderReportController::class, 'index']);
});
