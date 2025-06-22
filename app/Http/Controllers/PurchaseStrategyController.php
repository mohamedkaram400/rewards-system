<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\CreditPackage;
use App\Services\PaymentService;
use App\Traits\ApiResponseTrait;
use App\Enums\PurchaseStatusEnum;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AddPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseStrategyController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public PaymentService $paymentMethod){}

    /**
     * Handle the incoming request.
     */
    public function purchase(AddPurchase $request): JsonResponse
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
            $transactionId = $this->paymentMethod->initPayment($user, $request->type);

            // - Store the purchase record
            Purchase::create([
                'user_id'           => $user->id,
                'price_paid'        => $package->price,
                'credit_package_id' => $package->id,
                'transaction_id'    => $transactionId, // 
                'status'            => PurchaseStatusEnum::PENDING->value,
            ]);

            DB::commit();
        
            return $this->apiResponse('Purchase data sended to payment successfully.', 200);
        
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    public function callback(Request $request): JsonResponse
    {
        try {
            $this->paymentMethod->PaymentCallback($request);
            return $this->apiResponse('Callback processed successfully', 200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }
}
