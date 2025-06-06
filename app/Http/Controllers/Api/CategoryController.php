<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Exports\CategoriesExport;
use App\Imports\CategoriesImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category-manage');
    }

   public function index()
{
    try {
        $categories = Category::latest()->get();

        $response = [
            'error' => false,
            'message' => 'Categories fetched successfully',
            'data' => CategoryResource::collection($categories)
        ];

        return response()->json($response, 200);
    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to fetch categories',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}

public function store(CategoryRequest $request)
{
    try {
        $category = Category::create($request->validated());

        $response = [
            'error' => false,
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category)
        ];

        return response()->json($response, 201);
    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to create category',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}

public function show(Category $category)
{
    try {
        $response = [
            'error' => false,
            'message' => 'Category fetched successfully',
            'data' => new CategoryResource($category)
        ];

        return response()->json($response, 200);
    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to fetch category',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}

public function destroy(Category $category)
{
    try {
        if ($category->posts()->exists()) {
            $response = [
                'error' => true,
                'message' => 'Cannot delete category with posts'
            ];

            return response()->json($response, 400);
        }

        $category->delete();

        $response = [
            'error' => false,
            'message' => 'Category deleted successfully'
        ];

        return response()->json($response, 200);
    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to delete category',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}

public function import(Request $request)
{
    try {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new CategoriesImport, $request->file('file'));

        $response = [
            'error' => false,
            'message' => 'Categories imported successfully'
        ];

        return response()->json($response, 200);

    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to import categories',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}

public function export()
{
    try {
        if (Category::count() === 0) {
            return response()->json([
                'error' => true,
                'message' => 'No categories found to export'
            ], 404);
        }

        return Excel::download(new CategoriesExport, 'categories.xlsx');
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => 'Failed to export categories',
            'exception' => $e->getMessage()
        ], 500);
    }
}


public function forceDelete($id)
{
    $this->authorize('category-manage');

    DB::beginTransaction();
    try {
        $category = Category::withTrashed()->findOrFail($id);

        $hasPosts = $category->posts()->exists();

        if ($hasPosts) {
            return response()->json(['message' => 'Cannot permanently delete category with associated posts'], 400);
        }

        $category->forceDelete();

        DB::commit();
        return response()->json(['message' => 'Category permanently deleted'], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Something went wrong: ' . $e->getMessage()
        ], 500);
    }
}



    public function restore($id)
    {
        $this->authorize('category-manage');
        // dd($id);

        DB::beginTransaction();
        try {
            $category = Category::withTrashed()->findOrFail($id);
            $category->restore();
            DB::commit();
            return response()->json(['message' => 'Category restored successfully', 'data' => new CategoryResource($category)], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


}
