<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail($id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function destroy($id): bool
    {
        return $this->model->destroy($id);
    }

    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getInactive()
    {
        return $this->model->where('is_active', false)->get();
    }

    public function getTrashed()
    {
        return $this->model->onlyTrashed()->get();
    }

    public function restore($id): bool
    {
        return $this->model->withTrashed()->find($id)->restore();
    }

    public function forceDelete($id): bool
    {
        return $this->model->withTrashed()->find($id)->forceDelete();
    }
}