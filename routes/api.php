<?php

use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CreditPackageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login',    'login');
});



// Admin routes
Route::middleware(['auth:sanctum'])->group(function() {
    Route::prefix('admin')->group(function() {
    Route::apiResource('credit-packages', CreditPackageController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::patch('products/{product}/toggle-offer', [ProductController::class, 'toggleOffer']);
    });
    
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});