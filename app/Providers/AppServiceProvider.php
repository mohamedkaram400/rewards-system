<?php

namespace App\Providers;

use App\Services\ProductService;
use App\Services\CreditPackageService;
use Illuminate\Support\ServiceProvider;

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
        //
    }
}
