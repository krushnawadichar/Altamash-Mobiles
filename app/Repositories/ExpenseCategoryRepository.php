<?php

namespace App\Repositories;

use App\Models\ExpenseCategory;

class ExpenseCategoryRepository extends BaseRepository
{
    public function __construct(ExpenseCategory $expenseCategory)
    {
        parent::__construct($expenseCategory);
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->paginate(15);
    }

    public function toggleStatus($id)
    {
        $expenseCategory = $this->find($id);
        if ($expenseCategory) {
            $expenseCategory->is_active = !$expenseCategory->is_active;
            $expenseCategory->save();
            return $expenseCategory;
        }
        return null;
    }

    public function getWithExpenses()
    {
        return $this->model->with('expenses')->get();
    }

    public function getActiveWithExpenses()
    {
        return $this->model->where('is_active', true)->with('expenses')->get();
    }

    public function getCategoriesWithExpenseCount()
    {
        return $this->model->withCount('expenses')->get();
    }
}