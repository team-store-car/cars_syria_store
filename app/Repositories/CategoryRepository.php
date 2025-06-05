<?php


namespace App\Repositories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Get all categories.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Category::all();
    }

    /**
     * Find a category by its ID.
     *
     * @param int $id
     * @return Category|null
     */
    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */

    public function create(array $data): Category
    {
        return Category::create($data);
    }


    /**
     * Update an existing category.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */

    public function update(int $id, array $data): Category
    {
        $category = Category::find($id);
        $category->update($data);
        // dd($data);
        return $category;
    }

    /**
     * Delete a category by its ID.
     *
     * @param int $id
     * @return bool
     */

    public function delete(int $id): bool
    {
        $category = Category::find($id);
        return $category ? $category->delete() : false;
    }

      /**
     * Get all cars belonging to a specific category with pagination.
     *
     * @param int $categoryId
     * @param int $perPage Number of cars per page.
     * @return LengthAwarePaginator
     */
    public function getCarsByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        $category = $this->findById($categoryId);
        if (!$category) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }
        return $category->cars()->paginate($perPage);
    }
}
