<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Auth::user()->expenses()->with('category')->get();
        $categories = Category::all();

        return view(
            'expenses.index',
            compact('expenses', 'categories')
        );
        return response()->json($expenses );
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
            $expense = Expense::create($passedData);
            $expenses = Auth::user()->expenses()->with('category')->get();
            $categories = Category::all();
            return redirect()->route('expenses.index')->with('success', 'Expense created successfully')->with(['expenses' => $expenses, 'categories' => $categories]);
            return response()->json($expense, 201);
        }

        if ($validated->fails()){
            $errors = $validated->errors()->getMessages();
            return view('expenses.index', compact('validated', 'errors'));
        }
    }

    public function show(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($expense);
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $expense->update($validated);
        return response()->json($expense);
    }

    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $expense->delete();
    }
}
