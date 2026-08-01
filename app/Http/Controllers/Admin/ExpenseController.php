<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseRequest;
use App\Services\ExpenseService;
use App\Services\ExpenseCategoryService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $expenseService;
    protected $expenseCategoryService;

    public function __construct(
        ExpenseService $expenseService,
        ExpenseCategoryService $expenseCategoryService
    ) {
        $this->expenseService = $expenseService;
        $this->expenseCategoryService = $expenseCategoryService;
    }

    public function index()
    {
        $expenses = $this->expenseService->getAllExpenses();
        return view('admin.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $categories = $this->expenseCategoryService->getActiveExpenseCategories();
        return view('admin.expenses.create', compact('categories'));
    }

    public function store(ExpenseRequest $request)
    {
        $this->expenseService->createExpense($request->validated());
        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    public function edit($id)
    {
        $expense = $this->expenseService->getExpenseById($id);
        $categories = $this->expenseCategoryService->getActiveExpenseCategories();
        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(ExpenseRequest $request, $id)
    {
        $this->expenseService->updateExpense($id, $request->validated());
        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        $this->expenseService->deleteExpense($id);
        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}