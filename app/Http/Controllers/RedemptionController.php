<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Models\PointTransaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Enums\TransactionTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MakeRedemptionRequest;

class RedemptionController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle the incoming request.
     */
    public function __invoke(MakeRedemptionRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
        
            // Get the authenticated user and the selected product in the offer pool
            $user = Auth::user();
            $data = $request->validated();
            $product = Product::where('id', $data['product_id'])
                ->where('is_offer_pool', true)
                ->firstOrFail();
        
            // Ensure the user has enough points to redeem the product
            if ($user->points_balance < $product->point_cost) {
                return $this->apiResponse('User does not have enough points to redeem this product.', 401);
            }
        
            // Deduct the required points from the user's balance
            $user->points_balance -= $product->point_cost;
            $user->save();
        
            // Log the redemption transaction in point_transactions table
            PointTransaction::create([
                'user_id'    => $user->id,
                'amount'     => $product->point_cost,
                'type'       => TransactionTypeEnum::REDEMPTION->value,
                'related_id' => $product->id,
            ]);
        
            DB::commit();
        
            return $this->apiResponse('Product redeemed successfully.', 200, [
                'new_points_balance' => $user->points_balance
            ]);
        
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiResponse($e->getMessage(), 500);
        }
    }
}
