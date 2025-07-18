<?php
namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    
    /**
     * Get a valid product from the offer pool.
     *
     * @param int $productId
     * @return Product
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOfferProductById($productId): Product
    {
        return Product::where('id', $productId)
                    ->where('is_offer_pool', true)
                    ->firstOrFail();
    }
}