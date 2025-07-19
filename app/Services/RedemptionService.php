<?php
namespace App\Services;

use App\Models\Product;
use App\Enums\TransactionTypeEnum;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\DB;
use App\Enums\TransactionSourceEnum;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\InsufficientPointsException;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class RedemptionService
{
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    /**
     * Redeem a product using user's points.
     *
     * @param int $productId
     * @return int New balance
     *
     * @throws InsufficientPointsException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function redeem($productId): int
    {
        return DB::transaction(function () use ($productId): int {

            // Get the authenticated user and the selected product in the offer pool
            $user = Auth::user();

            // Get offer product by id
            $product = $this->productRepository->findOfferProductById($productId);
        
            // Ensure the user has enough points to redeem the product
            if ($user->points_balance < $product->point_cost) {
                throw new InsufficientPointsException();
            }
        
            // Deduct the required points from the user's balance
            $user->points_balance -= $product->point_cost;
            $user->save();
        
            // Log the redemption transaction in point_transactions table
            $this->recordTransaction($product, $user->id);
        
            DB::commit();
        
            return $user->points_balance;
        });
    }

    /**
     * Record a point transaction in the database.
     *
     * @param Product $product
     * @param int $userId
     * @return void
     */
    private function recordTransaction($product, $userId): void
    {
        TransactionHistory::create([
            'user_id' => $userId,
            'related_id' => $product->id,
            'amount'  => $product->point_cost,
            'type'    => TransactionTypeEnum::REDEMPTION->value,
            'source'  => TransactionSourceEnum::REDEEMED_PRODUCT->label($product->id),
        ]);
    }
}