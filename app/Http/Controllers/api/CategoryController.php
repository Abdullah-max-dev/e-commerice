<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Controllers\Controller;


class CategoryController extends Controller
{
    public function index()
    {
        return Category::latest()->get();
    }

    public function store(Request $request)
    {
        $save = $request->validate([
            'c_name' => 'required|unique:categories,c_name',
            'c_commission' => 'required|numeric'
        ]);

        return Category::create($save);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validate([
            'c_name' => 'required',
            'c_commission' => 'required|numeric'
        ]);
        $category->update($data);
        return $category;
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return response()->json(['success' => true]);
    }

    public function toggleTop($id)
    {
        $category = Category::findOrFail($id);
        $category->is_popular = !$category->is_popular;
        $category->save();

        return $category;
    }
}

