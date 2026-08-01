<?php

namespace App\Services;

use App\Models\ExpenseCategory;
use App\Repositories\ExpenseCategoryRepository;
use Illuminate\Support\Str;

class ExpenseCategoryService
{
    protected $expenseCategoryRepository;

    public function __construct(ExpenseCategoryRepository $expenseCategoryRepository)
    {
        $this->expenseCategoryRepository = $expenseCategoryRepository;
    }

    public function getAllExpenseCategories()
    {
        return $this->expenseCategoryRepository->all();
    }

    public function getActiveExpenseCategories()
    {
        return $this->expenseCategoryRepository->getActive();
    }

    public function getExpenseCategoryById($id)
    {
        return $this->expenseCategoryRepository->find($id);
    }

    public function createExpenseCategory(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['created_by'] = auth()->id();
        return $this->expenseCategoryRepository->create($data);
    }

    public function updateExpenseCategory($id, array $data)
    {
        $expenseCategory = $this->expenseCategoryRepository->find($id);
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->expenseCategoryRepository->update($expenseCategory, $data);
    }

    public function deleteExpenseCategory($id)
    {
        $expenseCategory = $this->expenseCategoryRepository->find($id);
        return $this->expenseCategoryRepository->delete($expenseCategory);
    }

    public function toggleStatus($id)
    {
        $expenseCategory = $this->expenseCategoryRepository->find($id);
        $expenseCategory->is_active = !$expenseCategory->is_active;
        return $expenseCategory->save();
    }
}