<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Str;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->all();
    }

    public function getActiveCategories()
    {
        return $this->categoryRepository->getActive();
    }

    public function getCategoryById($id)
    {
        return $this->categoryRepository->find($id);
    }

    public function createCategory(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['created_by'] = auth()->id();

        return $this->categoryRepository->create($data);
    }

    public function updateCategory($id, array $data)
    {
        $category = $this->categoryRepository->find($id);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->categoryRepository->update($category, $data);
    }

    public function deleteCategory($id)
    {
        $category = $this->categoryRepository->find($id);
        return $this->categoryRepository->delete($category);
    }

    public function toggleStatus($id)
    {
        $category = $this->categoryRepository->find($id);
        $category->is_active = !$category->is_active;
        return $category->save();
    }
}