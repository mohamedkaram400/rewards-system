<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\AiRedemptionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CreditPackageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login',    'login');
});



Route::middleware(['auth:sanctum'])->group(function() {
    // Admin routes 
    Route::prefix('admin')->group(function() {
    Route::apiResource('credit-packages', CreditPackageController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::patch('products/{product}/toggle-offer', [ProductController::class, 'toggleOffer']);
    });

    // Public (Auth) routes
    Route::get('redemptionable-products/', [ProductController::class, 'getProductsRedemptionable']);

    Route::post('/purchases', PurchaseController::class); // Without strategy pattern 
    
    Route::post('redemptions/', RedemptionController::class);
    Route::post('redemption-recommendation-with-ai/', AiRedemptionController::class);
    
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

