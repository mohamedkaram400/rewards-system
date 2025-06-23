<?php
namespace App\Strategy\Creators;

use Illuminate\Support\Facades\Http;
use App\Strategy\Interfaces\PaymentMethodInterface;

class PaymobPaymentStrategy implements PaymentMethodInterface
{
    public function excute($data)
    {
        // dd(config('services.paymob.username'), config('services.paymob.password'));

        // Step 1: Authenticate to get token
        $authResponse = Http::post(env('PAYMOB_BASE_URL') . '/auth/tokens', [
            'username' => config('services.paymob.username'),
            'password' => config('services.paymob.password'),
        ]);

        if ($authResponse->failed()) {
            throw new \Exception("Paymob authentication failed: " . $authResponse->body());
        }

        $token = $authResponse->json('token');
        return $this->sendPaymentInfo($token, $data);
    }

    private function sendPaymentInfo($token, $data)
    {
        // Step 2: Create the order
        $orderResponse = Http::post(env('PAYMOB_BASE_URL') . '/ecommerce/orders', [
            'auth_token' => $token,
            'api_source' => 'INVOICE',
            'amount_cents' => $data['package_price'],
            'currency' => 'EGP',
            'shipping_data' => [
                'first_name' => $data['name'],
                'last_name' => '',
                'phone_number' => '01010101010',
                'email' => $data['email'],
            ],
            'integrations' => [4277015], 
            'items' => [
                [
                    'name' => 'ASC1525',
                    'amount_cents' => '4000',
                    'quantity' => '1',
                    'description' => 'Smart Watch',
                ]
            ],
            'delivery_needed' => false,
        ]);

        if ($orderResponse->failed()) {
            throw new \Exception("Paymob order creation failed: " . $orderResponse->body());
        }

        $orderData = $orderResponse->json();

        return $orderData;
    }
}