<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CategoryService
{
    use ApiResponseTrait;

    /**
     * Get all categoires
     */
    public function getAllCategories(): JsonResponse 
    {
        $categories = DB::table('categories')->get();

        return $this->ApiResponse('Categories Returned Successfull', 201, $categories);
    }

    /**
     * Get a single category by ID
     *
     * @throws ModelNotFoundException
     */
    public function getCategoryById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    /**
     * Create a new category
     */
    public function createCategory($request): Category
    {
        $request->validate([
            'name'   => 'required|string'
        ]);

        $category = Category::create(['name' => $request->name]);

        return $category;
    }

    /**
     * Update an existing category
     */
    public function updateCategory($request, $id): Category
    {
        $request->validate([
            'name'   => 'required|string'
        ]);

        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);

        return $category;
    }

    /**
     * Delete a category
     */
    public function deleteCategory($id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return $this->ApiResponse('Category Deleted Successfull', 200); 
    }
}