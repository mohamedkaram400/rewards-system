<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Product;
use App\DTOs\Product\ProductDTO;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\CursorPaginator;
use App\Exceptions\NotFoundProductsException;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Retrieve all products with pagination and optional filters (offer pool, category, search).
     * Caches the result for 2 hours using a tag-based cache.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\CursorPaginator
     *
     * @throws \App\Exceptions\NotFoundException If no products are found.
     */
    public function getAllProducts($request): CursorPaginator 
    {
        $filters = [
            'perPage' => $request->input('perPage', 10),
            'offerPoolOnly' => $request->boolean('offerPool'),
            'categoryId' => $request->input('categoryId'),
            'searchTerm' => $request->input('searchTerm'),
        ];

        // Generate unique cache key based on all parameters
        $cacheKey = Helper::generateProductsCacheKey('product', $filters);

        return Cache::tags('products')->remember($cacheKey, now()->addHours(2), function () use ($filters) {

            $products = $this->productRepository->getAllProducts($filters);
           
            if ($products->count() === 0) {
                throw new NotFoundException('Not found products');
            }

            return $products;
        });
    }

    /**
     * Retrieve a single product by its ID.
     *
     * @param int $id
     * @return \App\Models\Product
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the product is not found.
     */
    public function getProductById(int $id): Product
    {
        return $this->productRepository->getProductById($id);
    }

    /**
     * Create a new product and flush the cache.
     *
     * @param ProductDTO $dto Product dto to be stored.
     * @return \App\Models\Product
     */
    public function createProduct($dto): Product
    {
        $product = $this->productRepository->createProduct($dto);

        if ($product) {
            Cache::tags('products')->flush();
        }

        return $product;
    }

    /**
     * Update an existing product, refresh its relations, and flush the cache.
     *
     * @param \App\Models\Product $product
     * @param ProductDTO $dto Updated product dto.
     * @return \App\Models\Product
     */
    public function updateProduct($product, $dto): Product
    {
        $this->productRepository->updateProduct($product, $dto);

        if ($product) {
            $product->fresh()->load('category');
            Cache::tags('products')->flush();
        }

        return $product;
    }

    /**
     * Toggle the "offer_pool" status of the given product and flush the cache.
     *
     * @param \App\Models\Product $product
     * @return \App\Models\Product
     */
    public function toggleOfferPool(Product $product): Product
    {
        $this->productRepository->toggleOfferPool($product);
        
        Cache::tags('products')->flush();

        return $product;
    }

    /**
     * Delete the given product and flush the cache.
     *
     * @param \App\Models\Product $product
     * @return void
     */
    public function deleteProduct(Product $product): void
    {
        $this->productRepository->deleteProduct($product);
        
        Cache::tags('products')->flush();
    }

    /**
     * Retrieve only redemptionable products with pagination.
     *
     * @param int $perPage Number of items per page.
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProductsRedemptionable($perPage): CursorPaginator
    {
        $products = $this->productRepository->getProductsRedemptionable($perPage);

        if ($products->count() === 0) {
                throw new NotFoundProductsException();
            }

        return $products;
    }
}