<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $category = Category::create(['name' => $request->name]);
        $categories = Category::all();
        return redirect()->route('category.index')->with('success', 'Category created successfully')->with('categories', $categories);
//        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {

    }
    public function edit(Category $category)
    {

    }
    public function update(Request $request, Category $category)
    {
        $category->update(['name' => $request->name]);
//        $categories = Category::all();
        return response()->json($category->only('name'));
//        return view('categories.index', compact('categories'));
//        return redirect()->route('category.index')->with('categories', $categories);
    }
    public function destroy(Category $category)
    {
        return $category->delete();
    }
}
