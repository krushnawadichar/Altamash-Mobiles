<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getWithProducts()
    {
        return $this->model->with('products')->get();
    }

    public function getActiveWithProducts()
    {
        return $this->model->where('is_active', true)->with('products')->get();
    }

    public function getCategoriesWithProductCount()
    {
        return $this->model->withCount('products')->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->paginate(15);
    }
}