<?php

namespace App\Http\Controllers;

use Exception;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Services\RedemptionService;
use App\Http\Requests\MakeRedemptionRequest;
use App\Exceptions\InsufficientPointsException;

class RedemptionController extends Controller
{
    use ApiResponseTrait;

    /**
     * RedemptionController constructor.
     *
     * @param RedemptionService $redemptionService
     */
    public function __construct(
        private RedemptionService $redemptionService
    ) {}

    /**
     * Redeem a product using reward points.
     *
     * @param MakeRedemptionRequest $request
     * @return JsonResponse
     *
     * @throws InsufficientPointsException
     * @throws \Exception
     */
    public function redeem(MakeRedemptionRequest $request): JsonResponse
    {
        try {
            $balance = $this->redemptionService->redeem($request->product_id);

            return $this->apiResponse('Product redeemed successfully.', 200,$balance);

        } catch (InsufficientPointsException $e) {
            return $this->apiResponse($e->getMessage(), 401);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }
}
