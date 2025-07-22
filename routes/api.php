<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Elastic\Elasticsearch\ClientBuilder;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AiRedemptionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\SanctumAuthController;
use App\Http\Controllers\PurchaseStrategyController;
use App\Http\Controllers\Admin\CreditPackageController;
use App\Http\Controllers\Auth\JWTAuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::controller(SanctumAuthController::class)->prefix('auth')->group(function () {
//     Route::post('/register', 'register');
//     Route::post('/login',    'login');
// });

Route::controller(JWTAuthController::class)->prefix('auth-jwt')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login',    'login');
    Route::post('/refresh',    'refresh');

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [JWTAuthController::class, 'me']);
    });
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

    Route::post('/purchases', [PurchaseStrategyController::class, 'purchase'])->middleware('throttle:dynamic')->name('purchase.credit');    ; // With strategy pattern 
    
    Route::get('transactions-history/{userId}', [TransactionController::class, 'transactionsHistory']);
    
    Route::post('redemptions/', [RedemptionController::class, 'redeem'])->middleware('throttle:dynamic')->name('redeem.product');;
    Route::post('redemption-recommendation-with-ai/', AiRedemptionController::class)->middleware('throttle:dynamic');
    
    Route::post('/auth/logout', [SanctumAuthController::class, 'logout']);

    Route::post('/auth-jwt/logout', [JWTAuthController::class, 'logout']);





    // Route::get('/search', function () {

    //     $client = ClientBuilder::create()->setHosts([config('elasticsearch.host')])->build();
    //     $client->ping();

    //     // $results = Product::search('Samsung')->get();
        
    //     // return $results;
    // });
});

Route::post('/payment/callback', [PurchaseStrategyController::class, 'callback']);

