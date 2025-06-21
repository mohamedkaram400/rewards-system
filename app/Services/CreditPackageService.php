<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\CreditPackage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreditPackageService
{
    /**
     * Get all credit packages with pagination
     */
    public function getAllPackages(int $perPage = 10): LengthAwarePaginator
    {
        // Generate unique cache key based on all parameters
        $cacheKey = Helper::generateProductsCacheKey('product', [
            'per_page' => $perPage,
        ]);

        return Cache::tags('credit_packages')->remember($cacheKey, now()->addHours(2), function () use ($perPage) {
            return CreditPackage::where('is_active', true)
                ->latest()
                ->paginate($perPage);
    
        });
    }

    /** 
     * Create a new credit package
     */
    public function createPackage(array $data): CreditPackage
    {
        $creditPackage = CreditPackage::create([
            'name' => $data['name'],
            'credit_amount' => $data['credit_amount'],
            'price' => $data['price'],
            'bonus_points' => $data['bonus_points'] ?? 0,
            'is_active' => $data['is_active'] ?? true
        ]);

        Cache::tags('credit_packages')->flush();

        return $creditPackage;
    }

    /**
     * Get a single CreditPackage by ID
     *
     * @throws ModelNotFoundException
     */
    public function getCreditPackageById(int $id): CreditPackage
    {
        return CreditPackage::findOrFail($id);
    }

    /**
     * Update an existing credit package
     */
    public function updatePackage(CreditPackage $package, array $data): CreditPackage
    {
        $package->update([
            'name' => $data['name'] ?? $package->name,
            'credit_amount' => $data['credit_amount'] ?? $package->credit_amount,
            'price' => $data['price'] ?? $package->price,
            'bonus_points' => $data['bonus_points'] ?? $package->bonus_points,
            'is_active' => $data['is_active'] ?? $package->is_active
        ]);


        Cache::tags('credit_packages')->flush();

        return $package->fresh();
    }

    /**
     * Delete a credit package
     */
    public function deletePackage(CreditPackage $package): void
    {
        $package->delete();
        Cache::tags('credit_packages')->flush();
    }
}