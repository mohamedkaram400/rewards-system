<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
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
        $searchTerm = $request->input('search');

        // Generate unique cache key based on all parameters
        $cacheKey = Helper::generateProductsCacheKey('product', [
            'per_page' => $perPage,
            'offer_pool' => $offerPoolOnly,
            'category_id' => $categoryId,
            'search' => $searchTerm
        ]);

        return Cache::tags('products')->remember($cacheKey, now()->addHours(2), function () use ($perPage, $offerPoolOnly, $categoryId, $searchTerm) {

            $query = Product::query();

            // Apply search if term exists
            if ($searchTerm) {
                $this->applySearch($query, $searchTerm);
            }

            return $query->when($offerPoolOnly, fn($query) => $query->where('is_offer_pool', true))
                ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
                ->with('category')
                ->latest()
                ->paginate($perPage);
        });
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

        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'point_cost' => $data['point_cost'] ?? 0,
            'is_offer_pool' => $data['is_offer_pool'] ?? false,
            'category_id' => $data['category_id'] ?? null
        ]);

        if ($product) {
            Cache::tags('products')->flush();
        }

        return $product;

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

        if ($product) {
            $product->fresh()->load('category');
            Cache::tags('products')->flush();
        }

        return $product;
    }

    /**
     * Toggle product's offer pool status
     */
    public function toggleOfferPool(Product $product): Product
    {
        $product->update([
            'is_offer_pool' => !$product->is_offer_pool
        ]);

        $product->fresh();
        
        Cache::tags('products')->flush();

        return $product;
    }

    /**
     * Delete a product
     */
    public function deleteProduct(Product $product): void
    {
        $product->delete();
        
        Cache::tags('products')->flush();
    }

    /**
     * Return Redemptionable products only
     */
    public function getProductsRedemptionable($request)
    {
        return Product::where('is_offer_pool', true)
            ->with('category')
            ->latest()
            ->paginate($request->perPage ?? 10);
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

    /**
     * Apply search conditions to the query
     */
    private function applySearch(Builder $query, string $searchTerm): void
    {
        $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
            ->orWhere('description', 'like', "%{$searchTerm}%")
            ->orWhereHas('category', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%"); 
            });
        });
    }
}