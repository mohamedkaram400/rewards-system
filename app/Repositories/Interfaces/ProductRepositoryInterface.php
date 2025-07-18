<?php
namespace App\Repositories\Interfaces;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function findOfferProductById($productId): Product;
}