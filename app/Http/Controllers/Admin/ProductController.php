<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\DTOs\Product\ProductDTO;
use App\Services\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Exceptions\NotFoundException;
use App\Http\Resources\ProductResource;
use App\Exceptions\ProductDeletionException;
use App\Http\Requests\Admin\CreateProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display a paginated list of products with optional filters.
     *
     * Filters can include search term, category, offer pool flag, etc.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \App\Exceptions\NotFoundException
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $products = $this->productService->getAllProducts($request);

            return $this->ApiResponse('Products Returned Successfull', 200, ProductResource::collection($products));

        } catch (NotFoundException $e) {
            return $this->apiResponse('No products found', 401);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created product in the database.
     *
     * Validated data comes from a custom request class.
     *
     * @param \App\Http\Requests\Admin\CreateProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        try {
            $dto = ProductDTO::fromArray($request->validated());

            $product = $this->productService->createProduct($dto);

            return $this->ApiResponse('Product Created Successfull', 201, new ProductResource($product));
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the details of a specific product.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        try {

            $product = $this->productService->getProductById($product->id);

            return $this->ApiResponse('Product Returned Successfull', 200, new ProductResource($product));
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified product with validated data.
     *
     * @param \App\Http\Requests\Admin\UpdateProductRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        try {
            $dto = ProductDTO::fromArray($request->validated());

            $product = $this->productService->updateProduct(
                $product, 
                $dto
            );

            return $this->ApiResponse('Product Updated Successfull', 200, new ProductResource($product));
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    /**
     * Toggle the offer pool status of the given product (on/off).
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleOffer(Product $product): JsonResponse
    {
        try {

            $product = $this->productService->toggleOfferPool($product);

            return $this->ApiResponse('Offer Pool Status Updated', 200, new ProductResource($product));
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete the given product from the database.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
    try {
        $this->productService->deleteProduct($product);
        return $this->apiResponse('Product deleted successfully', 200);
        } catch (ProductDeletionException $e) {
            return $this->apiResponse($e->getMessage(), 500);
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }
    /**
     * Return a paginated list of products eligible for redemption.
     *
     * Products are filtered based on the offer pool flag.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsRedemptionable(Request $request): JsonResponse
    {
        try {

            $packages = $this->productService->getProductsRedemptionable($request->per_page);

            return $this->ApiResponse('Products Returned Successfull', 200, ProductResource::collection($packages));
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }
}