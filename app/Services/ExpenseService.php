<?php

namespace App\Services;

use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
    protected $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
    }

    public function getAllExpenses()
    {
        return $this->expenseRepository->getWithCategory();
    }

    public function getExpenseById($id)
    {
        return $this->expenseRepository->find($id);
    }

    public function createExpense(array $data)
    {
        $data['created_by'] = auth()->id();

        if (isset($data['receipt']) && $data['receipt']) {
            $data['receipt'] = $this->uploadReceipt($data['receipt']);
        }

        return $this->expenseRepository->create($data);
    }

    public function updateExpense($id, array $data)
    {
        $expense = $this->expenseRepository->find($id);

        if (isset($data['receipt']) && $data['receipt']) {
            if ($expense->receipt) {
                Storage::delete('public/' . $expense->receipt);
            }
            $data['receipt'] = $this->uploadReceipt($data['receipt']);
        }

        return $this->expenseRepository->update($expense, $data);
    }

    public function deleteExpense($id)
    {
        $expense = $this->expenseRepository->find($id);
        if ($expense->receipt) {
            Storage::delete('public/' . $expense->receipt);
        }
        return $this->expenseRepository->delete($expense);
    }

    protected function uploadReceipt($receipt)
    {
        $filename = time() . '_' . uniqid() . '.' . $receipt->getClientOriginalExtension();
        return $receipt->storeAs('expenses/receipts', $filename, 'public');
    }

    public function getExpensesByCategory($categoryId)
    {
        return $this->expenseRepository->getByCategory($categoryId);
    }

    public function getExpensesByDateRange($startDate, $endDate)
    {
        return $this->expenseRepository->getByDateRange($startDate, $endDate);
    }

    public function getExpensesByStatus($status)
    {
        return $this->expenseRepository->getByStatus($status);
    }

    public function getTotalExpenses($startDate = null, $endDate = null)
    {
        return $this->expenseRepository->getTotalExpenses($startDate, $endDate);
    }

    public function getMonthlyExpenses($year = null)
    {
        return $this->expenseRepository->getMonthlyExpenses($year);
    }

    public function getExpensesByCategorySummary($startDate = null, $endDate = null)
    {
        return $this->expenseRepository->getExpensesByCategory($startDate, $endDate);
    }

    public function searchExpenses($query)
    {
        return $this->expenseRepository->search($query);
    }

    public function getRecentExpenses($limit = 10)
    {
        return $this->expenseRepository->getRecentExpenses($limit);
    }

    public function getExpenseStatistics()
    {
        $total = $this->expenseRepository->sum('amount');
        $paid = $this->expenseRepository->where('status', 'paid')->sum('amount');
        $pending = $this->expenseRepository->where('status', 'pending')->sum('amount');
        $cancelled = $this->expenseRepository->where('status', 'cancelled')->sum('amount');

        return [
            'total' => $total,
            'paid' => $paid,
            'pending' => $pending,
            'cancelled' => $cancelled,
        ];
    }
}