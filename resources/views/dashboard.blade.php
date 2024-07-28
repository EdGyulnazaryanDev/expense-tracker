@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .chart-container {
            width: 45%;
        }
        .metric-container {
            width: 30%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .metric {
            font-size: 2rem;
            margin: 0;
        }
        .metric-label {
            color: #777;
        }
    </style>
    <div class="container">

        <form method="GET" action="{{ route('dashboard') }}" class="form-group">
            <div>
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
            </div>
            <div>
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
            </div>
            <div>
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categoriesList as $category)
                        <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-outline-success mt-3 form-control">Filter</button>
            </div>
        </form>

        <!-- Total Sessions -->
        <div class="metric-container">
            <p class="metric">{{ $totalSessionsExpenses }} $</p>
            <p class="metric-label">Total Expenses</p>
        </div>

        <div class="metric-container">
            <p class="metric">{{ $totalSessionsRevenues }} $</p>
            <p class="metric-label">Total Revenues</p>
        </div>

        <!-- Monthly Expenses Chart -->
        <div class="chart-container">
            <canvas id="monthlyExpensesChart"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="monthlyRevenuesChart"></canvas>
        </div>

        <!-- Expenses by Category Chart -->
        <div class="chart-container">
            <canvas id="expensesByCategoryChart"></canvas>
        </div>

        <div class="chart-container">
            <canvas id="revenuesByCategoryChart"></canvas>
        </div>
    </div>

    <script>
        // Monthly Expenses Chart
        var monthlyCtxExp = document.getElementById('monthlyExpensesChart').getContext('2d');
        var monthlyChartExp = new Chart(monthlyCtxExp, {
            type: 'bar',
            data: {
                labels: {!! json_encode($expensesByMonth->keys()) !!},
                datasets: [{
                    label: 'Monthly Expenses',
                    data: {!! json_encode($expensesByMonth->values()) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        var monthlyCtxRev = document.getElementById('monthlyRevenuesChart').getContext('2d');
        var monthlyChartRev = new Chart(monthlyCtxRev, {
            type: 'bar',
            data: {
                labels: {!! json_encode($revenuesByMonth->keys()) !!},
                datasets: [{
                    label: 'Monthly Revenues',
                    data: {!! json_encode($revenuesByMonth->values()) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Expenses by Category Chart
        var categoryCtxExp = document.getElementById('expensesByCategoryChart').getContext('2d');
        var categoryChartExp = new Chart(categoryCtxExp, {
            type: 'pie',
            data: {
                labels: {!! json_encode($expensesByCategory->keys()) !!},
                datasets: [{
                    label: 'Expenses by Category',
                    data: {!! json_encode($expensesByCategory->values()) !!},
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });

        var categoryCtxRev = document.getElementById('revenuesByCategoryChart').getContext('2d');
        var categoryChartRev = new Chart(categoryCtxRev, {
            type: 'pie',
            data: {
                labels: {!! json_encode($revenuesByCategory->keys()) !!},
                datasets: [{
                    label: 'Revenues by Category',
                    data: {!! json_encode($revenuesByCategory->values()) !!},
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });
    </script>
@endsection
