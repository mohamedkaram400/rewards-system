<?php
namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Pagination\CursorPaginator;

interface ProductRepositoryInterface
{
    public function getAllProducts($filters): CursorPaginator;
    public function getProductById(int $id): Product;
    public function findOfferProductById(int $productId): Product;
    public function createProduct(array $data): Product;
    public function updateProduct(Product $product, array $data);
    public function deleteProduct(Product $product): void;
    public function toggleOfferPool(Product $product);
    public function getProductsRedemptionable(int $perPage);
}