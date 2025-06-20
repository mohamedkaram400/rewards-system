<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    /**
     * Get all products with pagination and optional filters
     */
    public function getAllProducts($request): LengthAwarePaginator 
    {
        $perPage = $request->input('per_page', 10);
        $offerPoolOnly = $request->boolean('offer_pool');
        $categoryId = $request->input('category_id');

        return Product::query()
            ->when($offerPoolOnly, fn($query) => $query->where('is_offer_pool', true))
            ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
            ->with('category')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get a single product by ID
     *
     * @throws ModelNotFoundException
     */
    public function getProductById(int $id): Product
    {
        return Product::with('category')->findOrFail($id);
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data): Product
    {
        $this->validateCategory($data['category_id'] ?? null);

        return Product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'point_cost' => $data['point_cost'] ?? 0,
            'is_offer_pool' => $data['is_offer_pool'] ?? false,
            'category_id' => $data['category_id'] ?? null
        ]);
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        if (isset($data['category_id'])) {
            $this->validateCategory($data['category_id']);
        }

        $product->update([
            'name' => $data['name'] ?? $product->name,
            'description' => $data['description'] ?? $product->description,
            'price' => $data['price'] ?? $product->price,
            'point_cost' => $data['point_cost'] ?? $product->point_cost,
            'is_offer_pool' => $data['is_offer_pool'] ?? $product->is_offer_pool,
            'category_id' => $data['category_id'] ?? $product->category_id
        ]);

        return $product->fresh()->load('category');
    }

    /**
     * Toggle product's offer pool status
     */
    public function toggleOfferPool(Product $product): Product
    {
        $product->update([
            'is_offer_pool' => !$product->is_offer_pool
        ]);

        return $product->fresh();
    }

    /**
     * Delete a product
     */
    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }

    /**
     * Validate category exists
     *
     * @throws ModelNotFoundException
     */
    private function validateCategory($categoryId): void
    {
        if ($categoryId && !Category::where('id', $categoryId)->exists()) {
            throw new ModelNotFoundException("Category not found");
        }
    }
}