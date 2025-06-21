<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\CreditPackageResource;
use Illuminate\Http\Request;
use App\Models\CreditPackage;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\CreditPackageService;
use App\Http\Requests\Admin\CreditPackageRequest;

class CreditPackageController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private CreditPackageService $creditPackageService
    ) {}

    /**
     * Display a listing of credit packages.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $packages = $this->creditPackageService->getAllPackages($perPage);

        return $this->ApiResponse('Packages Returned Successfull', 200, CreditPackageResource::collection($packages));
    }

    /**
     * Store a newly created credit package.
     */
    public function store(CreditPackageRequest $request): JsonResponse
    {
        $package = $this->creditPackageService->createPackage($request->validated());

        return $this->ApiResponse('Package Created Successfull', 201, new CreditPackageResource($package));
    }

    /**
     * Display the specified creditPackage
     */
    public function show(CreditPackage $creditPackage): JsonResponse
    {
        $creditPackage = $this->creditPackageService->getcreditPackageById($creditPackage->id);

        return $this->ApiResponse('creditPackage Returned Successfull', 200, new CreditPackageResource($creditPackage));
    }

    /**
     * Update the specified credit package.
     */
    public function update(CreditPackageRequest $request, CreditPackage $creditPackage): JsonResponse
    {
        $package = $this->creditPackageService->updatePackage(
            $creditPackage, 
            $request->validated()
        );

        return $this->ApiResponse('Package Updated Successfull', 200, new CreditPackageResource($package));
    }

    /**
     * Remove the specified credit package.
     */
    public function destroy(CreditPackage $creditPackage): JsonResponse
    {
        $this->creditPackageService->deletePackage($creditPackage);

        return $this->ApiResponse('Package Deleted Successfull', 204, '');
    }
}
