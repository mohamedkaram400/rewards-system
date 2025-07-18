<?php
namespace App\DTOs\Product;

class ProductDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public float $price,
        public int $categoryId,
        public int $isOfferPool,
        public int $pointCost
    ) {}
}