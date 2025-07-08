<?php

namespace App\Providers;

use App\Services\ProductService;
use App\Services\CreditPackageService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CreditPackageService::class, function (): CreditPackageService {
            return new CreditPackageService();
        });
        
        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService();
        });
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('dynamic', function ($request) {
            $routeName = $request->route()?->getName();
        
            return match ($routeName) {
                'redeem.product' => Limit::perMinute(2)->by($request->user()?->id ?: $request->ip()),
                'purchase.credit' => Limit::perMinute(2)->by($request->user()?->id ?: $request->ip()),
                default => Limit::perMinute(20)->by($request->ip()),
            };
        });
    }
}
