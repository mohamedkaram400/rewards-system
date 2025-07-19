<?php
namespace App\DTOs\Product;

class ProductDTO
{
    public function __construct(
        public ?string $name,
        public ?string $description,
        public ?float $price,
        public ?int $categoryId,
        public ?int $isOfferPool,
        public ?int $pointCost
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            price: isset($data['price']) ? (float)$data['price'] : null,
            pointCost: isset($data['point_cost']) ? (int)$data['point_cost'] : null,
            isOfferPool: isset($data['is_offer_pool']) ? (int)$data['is_offer_pool'] : null,
            categoryId: isset($data['category_id']) ? (int)$data['category_id'] : null,
        );
    }
}