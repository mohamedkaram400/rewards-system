<?php
namespace App\Repositories\Interfaces;

use App\Models\Product;
use App\DTOs\Product\ProductDTO;
use Illuminate\Pagination\CursorPaginator;

interface ProductRepositoryInterface
{
    public function getAllProducts($filters): CursorPaginator;
    public function getProductById(int $id): Product;
    public function findOfferProductById(int $productId): Product;
    public function createProduct(ProductDTO $dto): Product;
    public function updateProduct(Product $product, ProductDTO $dto);
    public function deleteProduct(Product $product): void;
    public function toggleOfferPool(Product $product);
    public function getProductsRedemptionable(int $perPage);
}