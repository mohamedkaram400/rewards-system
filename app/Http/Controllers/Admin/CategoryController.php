<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DB::table('categories')->get();

        return $this->ApiResponse('Categories Returned Successfull', 201, $categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string'
        ]);

        $category = Category::create(['name' => $request->name]);

        return $this->ApiResponse('Category Created Successfull', 201, $category);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|string'
        ]);

        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);
        
        return $this->ApiResponse('Category Updated Successfull', 200, $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return $this->ApiResponse('Category Deleted Successfull', 200);
    }
}