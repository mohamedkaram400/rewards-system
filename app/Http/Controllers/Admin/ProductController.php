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

        return $this->ApiResponse('Products Returned Successfull', 200, $products);
    }

    /**
     * Store a newly created product
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return $this->ApiResponse('Product Created Successfull', 201, $product);
    }

    /**
     * Display the specified product
     */
    public function show(Product $product): JsonResponse
    {
        $product = $this->productService->getProductById($product->id);

        return $this->ApiResponse('Product Returned Successfull', 200, $product);
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

        return $this->ApiResponse('Product Updated Successfull', 200, $product);
    }

    /**
     * Toggle product's offer pool status
     */
    public function toggleOffer(Product $product): JsonResponse
    {
        $product = $this->productService->toggleOfferPool($product);

        return $this->ApiResponse('Offer Pool Status Updated', 200, $product);
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return $this->ApiResponse('Product Deleted Successfull', 204);
    }
}