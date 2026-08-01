<?php

namespace App\Repositories;

use App\Models\Expense;

class ExpenseRepository extends BaseRepository
{
    public function __construct(Expense $expense)
    {
        parent::__construct($expense);
    }

    public function getWithCategory()
    {
        return $this->model->with('expenseCategory')->get();
    }

    public function getByCategory($categoryId)
    {
        return $this->model->where('expense_category_id', $categoryId)
                          ->with('expenseCategory')
                          ->get();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('expense_date', [$startDate, $endDate])
                          ->with('expenseCategory')
                          ->get();
    }

    public function getByStatus($status)
    {
        return $this->model->where('status', $status)
                          ->with('expenseCategory')
                          ->get();
    }

    public function getByPaymentMethod($method)
    {
        return $this->model->where('payment_method', $method)
                          ->with('expenseCategory')
                          ->get();
    }

    public function getTotalExpenses($startDate = null, $endDate = null)
    {
        $query = $this->model->query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }
        
        return $query->where('status', 'paid')->sum('amount');
    }

    public function getMonthlyExpenses($year = null)
    {
        $year = $year ?? date('Y');
        return $this->model->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
                          ->whereYear('expense_date', $year)
                          ->where('status', 'paid')
                          ->groupBy('month')
                          ->orderBy('month')
                          ->pluck('total', 'month');
    }

    public function getExpensesByCategory($startDate = null, $endDate = null)
    {
        $query = $this->model->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                            ->select(
                                'expense_categories.name as category',
                                \DB::raw('SUM(expenses.amount) as total')
                            )
                            ->where('expenses.status', 'paid')
                            ->groupBy('expense_categories.id', 'expense_categories.name');

        if ($startDate && $endDate) {
            $query->whereBetween('expenses.expense_date', [$startDate, $endDate]);
        }

        return $query->get();
    }

    public function search($query)
    {
        return $this->model->where('title', 'like', "%{$query}%")
                          ->orWhereHas('expenseCategory', function($q) use ($query) {
                              $q->where('name', 'like', "%{$query}%");
                          })
                          ->orWhere('description', 'like', "%{$query}%")
                          ->with('expenseCategory')
                          ->paginate(15);
    }

    public function getRecentExpenses($limit = 10)
    {
        return $this->model->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->with('expenseCategory')
                          ->get();
    }
}