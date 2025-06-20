<?php

namespace App\Services;

use App\Models\CreditPackage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreditPackageService
{
    /**
     * Get all credit packages with pagination
     */
    public function getAllPackages(int $perPage = 10): LengthAwarePaginator
    {
        return CreditPackage::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new credit package
     */
    public function createPackage(array $data): CreditPackage
    {
        return CreditPackage::create([
            'name' => $data['name'],
            'credit_amount' => $data['credit_amount'],
            'price' => $data['price'],
            'bonus_points' => $data['bonus_points'] ?? 0,
            'is_active' => $data['is_active'] ?? true
        ]);
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

        return $package->fresh();
    }

    /**
     * Delete a credit package
     */
    public function deletePackage(CreditPackage $package): void
    {
        $package->delete();
    }
}