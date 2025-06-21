<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Purchase;
use Illuminate\Support\Str;
use App\Models\CreditPackage;
use App\Models\PointTransaction;
use App\Traits\ApiResponseTrait;
use App\Enums\PurchaseStatusEnum;
use Illuminate\Http\JsonResponse;
use App\Enums\TransactionTypeEnum;
use App\Http\Requests\AddPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle the incoming request.
     */
    public function __invoke(AddPurchase $request): JsonResponse
    {
        try {
            DB::beginTransaction();
        
            $data = $request->validated();
        
            // - Get the authenticated user and the selected active credit package
            $user = Auth::user();
            $package = CreditPackage::where('id', $data['credit_package_id'])
                ->where('is_active', true)
                ->firstOrFail();
        
            // - Generate a unique transaction ID (simulating a payment gateway response)
            $uuid = Str::uuid();
        
            // - Add the bonus points from the package to the user's balance
            $user->points_balance += $package->bonus_points;
            $user->save();
        
            // - Store the purchase record
            $purchase = Purchase::create([
                'user_id'           => $user->id,
                'price_paid'        => $package->price,
                'credit_package_id' => $package->id,
                'transaction_id'    => $uuid,
                'status'            => PurchaseStatusEnum::PENDING->value,
            ]);
        
            // - Log the transaction in point_transactions table
            PointTransaction::create([
                'user_id'    => $user->id,
                'related_id' => $purchase->id,
                'type'       => TransactionTypeEnum::PURCHASE->value,
                'amount'     => $package->bonus_points,
            ]);
        
            DB::commit();
        
            return $this->apiResponse('Purchase completed successfully.', 200, [
                'transaction_id' => $uuid,
                'new_points_balance' => $user->points_balance
            ]);
        
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiResponse($e->getMessage(), 500);
        }
    }
}
