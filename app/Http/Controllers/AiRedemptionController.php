<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Auth;

class AiRedemptionController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle the incoming request.
     */
    public function __invoke(): JsonResponse
    {

        // Get the authenticated user and the selected product in the offer pool
        $user = Auth::user();
        $products = Product::where('is_offer_pool', true)->get();

        $prompt = "Given the following user balance: {$user->points_balance} and product list:\n";

        foreach ($products as $product) {
            $prompt .= "- {$product->name} (Cost: {$product->points_cost} points)\n";
        }

        $prompt .= "\nWhich product would you recommend and why?";

        $result = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        return $this->apiResponse('success',
            200,
            ['recommendation' => $result->choices[0]->message->content],
        );
    }
}
