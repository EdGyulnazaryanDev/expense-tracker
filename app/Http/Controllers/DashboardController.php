<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Revenue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfYear()->toDateString());
        $categoryId = $request->input('category_id');

        $categoriesList = Category::all();

        $expenseData = $this->getExpenseData($categoryId, $startDate, $endDate);
        $totalSessionsExpenses = $expenseData['totalSessionsExpenses'];
        $expensesByMonth = $expenseData['expensesByMonth'];
        $expensesByCategory = $expenseData['expensesByCategory'];

        $revenueData = $this->getRevenueData($categoryId, $startDate, $endDate);
        $totalSessionsRevenues = $revenueData['totalSessionsRevenues'];
        $revenuesByMonth = $revenueData['revenuesByMonth'];
        $revenuesByCategory = $revenueData['revenuesByCategory'];


        return view('dashboard',
            compact(
                'categoriesList',
                'startDate',
                'endDate',
                'categoryId',
                'totalSessionsExpenses',
                'expensesByMonth',
                'expensesByCategory',
                'revenuesByMonth',
                'revenuesByCategory',
                'totalSessionsRevenues'
            )
        );
    }

    public function getExpenseData($categoryId = null, $startDate = null, $endDate = null)
    {
        $queryExpense = Expense::with('category')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($categoryId) {
            $queryExpense->where('category_id', $categoryId);
        }

        $expenses = $queryExpense->get();

        $totalSessions = $expenses->sum('amount');
        $expensesByMonth = $expenses->groupBy(function($date) {
            return Carbon::parse($date->date)->format('M');
        })->map(function ($row) {
            return $row->sum('amount');
        });

        $expensesByCategory = $expenses->groupBy('category.name')->map(function ($row) {
            return $row->sum('amount');
        });

        return [
            'expenses' => $expenses,
            'totalSessionsExpenses' => $totalSessions,
            'expensesByMonth' => $expensesByMonth,
            'expensesByCategory' => $expensesByCategory
        ];
    }
    public function getRevenueData($categoryId = null, $startDate = null, $endDate = null)
    {
        $queryRevenue = Revenue::with('category')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($categoryId) {
            $queryRevenue->where('category_id', $categoryId);
        }

        $revenues = $queryRevenue->get();

        $totalSessions = $revenues->sum('amount');
        $revenuesByMonth = $revenues->groupBy(function($date) {
            return Carbon::parse($date->date)->format('M');
        })->map(function ($row) {
            return $row->sum('amount');
        });

        $revenuesByCategory = $revenues->groupBy('category.name')->map(function ($row) {
            return $row->sum('amount');
        });

        return [
            'revenues' => $revenues,
            'totalSessionsRevenues' => $totalSessions,
            'revenuesByMonth' => $revenuesByMonth,
            'revenuesByCategory' => $revenuesByCategory
        ];
    }
}
