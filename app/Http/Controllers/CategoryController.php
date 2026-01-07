<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * ==========================================================
     * 📌 GET ALL CATEGORIES (Vue dropdown + listings)
     * ==========================================================
     * RETURNS:
     * {
     *   "categories": [
     *     { "CategoryID": 1, "CategoryName": "Roof" }
     *   ]
     * }
     * ==========================================================
     */
    public function index()
    {
        $categories = Category::select(
                'CategoryID',
                'CategoryName'
            )
            ->orderBy('CategoryName')
            ->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * ==========================================================
     * 📌 GET SINGLE CATEGORY (FIXED)
     * ==========================================================
     * RETURNS:
     * {
     *   "category": {
     *     "CategoryID": 1,
     *     "CategoryName": "Roof"
     *   }
     * }
     * ==========================================================
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'category' => [
                'CategoryID'   => $category->CategoryID,
                'CategoryName' => $category->CategoryName,
            ]
        ]);
    }

    /**
     * ==========================================================
     * 📌 CREATE CATEGORY
     * ==========================================================
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CategoryName' => 'required|string|max:255|unique:categories,CategoryName',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create([
            'CategoryName' => $request->CategoryName
        ]);

        return response()->json([
            'status'   => 'success',
            'category' => $category
        ], 201);
    }

    /**
     * ==========================================================
     * 📌 UPDATE CATEGORY
     * ==========================================================
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'CategoryName' =>
                "required|string|max:255|unique:categories,CategoryName,$id,CategoryID",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update([
            'CategoryName' => $request->CategoryName
        ]);

        return response()->json([
            'status'   => 'success',
            'category' => $category
        ]);
    }

    /**
     * ==========================================================
     * 📌 DELETE CATEGORY
     * ==========================================================
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Category deleted successfully.'
        ]);
    }
}
