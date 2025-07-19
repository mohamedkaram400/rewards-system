<?php

namespace App\Services;

use App\Models\User;
use App\Models\Purchase;
use App\Models\CreditPackage;
use App\Strategy\PaymentProcess;
use App\Enums\PurchaseStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\TransactionHistory;
use App\Enums\TransactionSourceEnum;
use App\Strategy\ConcreteStrategyFactory;

class PaymentService
{
    public function initPayment($data)
    {
        $factory = new ConcreteStrategyFactory();
        $context = new PaymentProcess();
        $strategy = $factory->InitStrategy($data['payment_method']);
        $context->setStrategy($strategy);
        $response = $context->paymentResponse($data);
        
        return $response;
    }

    public function PaymentCallback($request)
    {
        // Get Important resources from database
        $user = User::where('id', $request->user_id)->first();
        $purchase = Purchase::where('transaction_id', $request->transaction_id)->first();
        $package = CreditPackage::findOrFail($purchase->credit_package_id);

        if ($request->status == 'success') { 

            // - Add the bonus points from the package to the user's balance
            $user->points_balance += $package->bonus_points;
            $user->save();
        
            $purchase->status = PurchaseStatusEnum::COMPLETED->value;
            $purchase->save();

                    
            // - Log the transaction in point_transactions table
            $this->recordTransaction($package, $user->id, $purchase->id);
 
        } else {
            $purchase->status = PurchaseStatusEnum::FAILED->value;
            $purchase->save();
        }
                
    }

    /**
     * Record a point transaction in the database.
     *
     * @param CreditPackage $package
     * @param int $userId
     * @param int $purchaseId
     * @return void
     */
    private function recordTransaction($package, $userId, $purchaseId): void
    {
        TransactionHistory::create([
            'user_id' => $userId,
            'related_id' => $purchaseId,
            'amount'     => $package->bonus_points,
            'type'    => TransactionTypeEnum::REDEMPTION->value,
            'source'  => TransactionSourceEnum::ORDER->label($package->id),
        ]);
    }
}