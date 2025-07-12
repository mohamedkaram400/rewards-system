<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getAllProducts($request);

        if ($products->count() === 0) {
            return $this->ApiResponse('No products found', 404);
        }

        return $this->ApiResponse('Products Returned Successfull', 200, ProductResource::collection($products));
    }

    /**
     * Store a newly created product
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return $this->ApiResponse('Product Created Successfull', 201, new ProductResource($product));
    }

    /**
     * Display the specified product
     */
    public function show(Product $product): JsonResponse
    {
        $product = $this->productService->getProductById($product->id);

        return $this->ApiResponse('Product Returned Successfull', 200, new ProductResource($product));
    }

    /**
     * Update the specified product
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->updateProduct(
            $product, 
            $request->validated()
        );

        return $this->ApiResponse('Product Updated Successfull', 200, new ProductResource($product));
    }

    /**
     * Toggle product's offer pool status
     */
    public function toggleOffer(Product $product): JsonResponse
    {
        $product = $this->productService->toggleOfferPool($product);

        return $this->ApiResponse('Offer Pool Status Updated', 200, new ProductResource($product));
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return $this->ApiResponse('Product Deleted Successfull', 204);
    }

    /**
     * Return Redemptionable products only
     */
    public function getProductsRedemptionable(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $packages = $this->productService->getProductsRedemptionable($perPage);

        return $this->ApiResponse('Products Returned Successfull', 200, ProductResource::collection($packages));
    }
}