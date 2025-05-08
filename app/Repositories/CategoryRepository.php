<?php


namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function all(): Collection
    {
        return Category::all();
    }

    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = Category::find($id);
        return $category ? $category->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $category = Category::find($id);
        return $category ? $category->delete() : false;
    }
}
