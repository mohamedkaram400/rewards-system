<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Exceptions\NotFoundProductsException;
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
     * @throws \App\Exceptions\NotFoundProductsException
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $products = $this->productService->getAllProducts($request);

            return $this->ApiResponse('Products Returned Successfull', 200, ProductResource::collection($products));

        } catch (NotFoundProductsException $e) {
            return $this->apiResponse($e->getMessage(), 401);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created product in the database.
     *
     * Validated data comes from a custom request class.
     *
     * @param \App\Http\Requests\Product\CreateProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return $this->ApiResponse('Product Created Successfull', 201, new ProductResource($product));
    }

    /**
     * Display the details of a specific product.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        $product = $this->productService->getProductById($product->id);

        return $this->ApiResponse('Product Returned Successfull', 200, new ProductResource($product));
    }

    /**
     * Update the specified product with validated data.
     *
     * @param \App\Http\Requests\Product\UpdateProductRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
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
     * Toggle the offer pool status of the given product (on/off).
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleOffer(Product $product): JsonResponse
    {
        $product = $this->productService->toggleOfferPool($product);

        return $this->ApiResponse('Offer Pool Status Updated', 200, new ProductResource($product));
    }

    /**
     * Delete the given product from the database.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return $this->ApiResponse('Product Deleted Successfull', 204);
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
        $packages = $this->productService->getProductsRedemptionable($request->per_page);

        return $this->ApiResponse('Products Returned Successfull', 200, ProductResource::collection($packages));
    }
}