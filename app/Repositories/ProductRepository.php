<?php
namespace App\Repositories;

use App\Models\Product;
use App\DTOs\Product\ProductDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use App\Exceptions\ProductDeletionException;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Retrieve all products with optional filters: search, offer pool, category, and pagination.
     *
     * @param array $filters [
     *     'searchTerm' => string|null,
     *     'offerPoolOnly' => bool|null,
     *     'categoryId' => int|null,
     *     'perPage' => int
     * ]
     * @return \Illuminate\Pagination\CursorPaginator
     */
    public function getAllProducts($filters): CursorPaginator 
    {
        $query = Product::query();

        // Apply search if term exists
        if ($filters['searchTerm']) {
            $this->applySearch($query, $filters['searchTerm']);
        }

        return $query->when($filters['offerPoolOnly'], fn($query) => $query->where('is_offer_pool', true))
            ->when($filters['categoryId'], fn($query) => $query->where('category_id', $filters['categoryId']))
            ->with('category:id,name')
            ->latest()
            ->cursorPaginate($filters['perPage']);
    }

    /**
     * Retrieve a product by its ID with its category relationship.
     *
     * @param int $id
     * @return \App\Models\Product
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProductById(int $id): Product
    {
        return Product::with('category')->findOrFail($id);
    }

    /**
     * Get a product from the offer pool by ID.
     *
     * @param int $productId
     * @return \App\Models\Product
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOfferProductById($productId): Product
    {
        return Product::where('id', $productId)
                    ->where('is_offer_pool', true)
                    ->firstOrFail();
    }

    /**
     * Create a new product using the given dto.
     *
     * @param ProductDTO $dto
     * @return \App\Models\Product
     */
    public function createProduct(ProductDTO $dto): Product
    {
        // dd($dto);

        return Product::create([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'point_cost' => $dto->pointCost ?? 0,
            'is_offer_pool' => $dto->isOfferPool ?? false,
            'category_id' => $dto->categoryId ?? null
        ]);
    }

    /**
     * Update the given product with new dto.
     *
     * @param \App\Models\Product $product
     * @param ProductDTO $dto
     * @return void
     */
    public function updateProduct(Product $product, ProductDTO $dto): void
    {
        $product->update([
            'name' => $dto->name ?? $product->name,
            'description' => $dto->description ?? $product->description,
            'price' => $dto->price ?? $product->price,
            'point_cost' => $dto->pointCost ?? $product->point_cost,
            'is_offer_pool' => $dto->isOfferPool ?? $product->is_offer_pool,
            'category_id' => $dto->categoryId ?? $product->category_id
        ]);
    }

    /**
     * Toggle the offer pool status of the product (on/off).
     *
     * @param \App\Models\Product $product
     * @return void
     */
    public function toggleOfferPool(Product $product): void
    {
        $product->update([
            'is_offer_pool' => !$product->is_offer_pool
        ]);

        $product->fresh();
    }

    /**
     * Delete the given product from the database.
     *
     * @param \App\Models\Product $product
     * @return void
     */
    public function deleteProduct(Product $product): void
    {
        $deleted = $product->delete();

        if (!$deleted) {
            throw new ProductDeletionException("Failed to delete the product.");
        }
    }

    /**
     * Retrieve all products that are eligible for redemption (offer pool = true).
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\CursorPaginator
     */
    public function getProductsRedemptionable($perPage): CursorPaginator
    {
        return Product::where('is_offer_pool', true)
            ->with('category')
            ->latest()
            ->cursorPaginate($perPage ?? 10);
    }

    /**
     * Apply a full-text search on product name/description and category name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchTerm
     * @return void
     */
    private function applySearch(Builder $query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            // Full-text search on products table
            $q->whereRaw('MATCH(name, description) AGAINST (? IN BOOLEAN MODE)', [$searchTerm])
    
            // Full-text search on related category name
            ->orWhereHas('category', function ($q) use ($searchTerm) {
                $q->whereRaw('MATCH(name) AGAINST (? IN BOOLEAN MODE)', [$searchTerm]);
            });
        });
    }
}