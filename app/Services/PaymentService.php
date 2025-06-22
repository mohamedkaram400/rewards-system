<?php

namespace App\Services;

use App\Models\User;
use App\Models\Purchase;
use App\Models\CreditPackage;
use App\Models\PointTransaction;
use App\Strategy\PaymentProcess;
use App\Enums\PurchaseStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Strategy\ConcreteStrategyFactory;

class PaymentService
{
    public function initPayment($type)
    {
        $factory = new ConcreteStrategyFactory();
        $context = new PaymentProcess();
        $strategy = $factory->InitStrategy($type);
        $context->setStrategy($strategy);
        $response = $context->paymentResponse();
        
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
            PointTransaction::create([
                'user_id'    => $user->id,
                'related_id' => $purchase->id,
                'type'       => TransactionTypeEnum::PURCHASE->value,
                'amount'     => $package->bonus_points,
            ]);

        } else {
            $purchase->status += PurchaseStatusEnum::FAILED->value;
            $purchase->save();
        }
                
    }
}