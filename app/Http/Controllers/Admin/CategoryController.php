<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->categoryService->getAllCategories();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return $this->ApiResponse('Category Created Successfull', 201, $category);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): Category
    {
        $category = $this->categoryService->getCategoryById($id);
        return $category;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $category = $this->categoryService->updateCategory($request, $id);
        
        return $this->ApiResponse('Category Updated Successfull', 200, $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        return $this->categoryService->deleteCategory($id);
    }
}