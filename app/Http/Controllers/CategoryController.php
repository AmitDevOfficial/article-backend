<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Create Category
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $extension = $image->getClientOriginalExtension();

            $filename = Str::uuid() . '_' . time() . '.' . $extension;

            $image->move(public_path('uploads/categories'), $filename);

        } else {

            $filename = 'default.png';

        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $filename,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category Created Successfully',
            'data' => $category
        ], 201);
    }

    // View Categories
    public function index()
    {
        $category = Category::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }

    // Single Category
    public function edit($id)
    {
        $category = Category::find($id);

        if (!$category) {

            return response()->json([
                'status' => false,
                'message' => 'Category Not Found'
            ], 404);

        }

        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }

    // Update Category
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {

            return response()->json([
                'status' => false,
                'message' => 'Category Not Found'
            ], 404);

        }

        $request->validate([
            'name' => "required|unique:categories,name,$id",
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {

            if (
                $category->image != 'default.png' &&
                File::exists(public_path('uploads/categories/' . $category->image))
            ) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }

            $image = $request->file('image');

            $extension = $image->getClientOriginalExtension();

            $filename = Str::uuid() . '_' . time() . '.' . $extension;

            $image->move(public_path('uploads/categories'), $filename);

            $category->image = $filename;
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->status = $request->status;

        $category->save();

        return response()->json([
            'status' => true,
            'message' => 'Category Updated Successfully',
            'data' => $category
        ]);
    }

    // Delete Category
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {

            return response()->json([
                'status' => false,
                'message' => 'Category Not Found'
            ], 404);

        }

        if (
            $category->image != 'default.png' &&
            File::exists(public_path('uploads/categories/' . $category->image))
        ) {
            File::delete(public_path('uploads/categories/' . $category->image));
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully'
        ]);
    }
}