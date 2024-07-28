<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $revenues = Auth::user()->revenues()->with('category')->get();
        if (isset($request->category_id) && !is_null($request->category_id)) {
            $revenues = $revenues->where('category_id', $request->category_id);
        }
        $categories = Category::all();

        return view(
            'revenues.index',
            compact('revenues', 'categories')
        );
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        if ($validated->passes()) {
//            dd($validated->getData());
            $passedData = $validated->getData();
            $passedData['user_id'] = Auth::id();
            $revenue = Revenue::create($passedData);
            $revenues = Auth::user()->revenues()->with('category')->get();
            $categories = Category::all();
            return redirect()->route('revenues.index')->with('success', 'Revenue created successfully')->with(['revenues' => $revenues, 'categories' => $categories]);
            return response()->json($revenue, 201);
        }

        if ($validated->fails()){
            $errors = $validated->errors()->getMessages();
            return view('revenues.index', compact('validated', 'errors'));
        }
    }

    public function show(Revenue $revenue)
    {
        if ($revenue->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($revenue);
    }

    public function update(Request $request, Revenue $revenue)
    {
        if ($revenue->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $revenue->update($validated);
        return response()->json($revenue);
    }

    public function destroy(Revenue $revenue)
    {
        if ($revenue->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $revenue->delete();
    }
}
