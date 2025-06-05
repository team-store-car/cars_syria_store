<?php
namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
      /**
     * The category repository instance.
     *
     * @var CategoryRepository
     */

    protected CategoryRepository $categoryRepository;

    /**
     * Create a new CategoryService instance.
     *
     * @param CategoryRepository $categoryRepository
     */

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories.
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    /**
     * Get a category by its ID.
     *
     * @param int $id
     * @return Category|null
     */

    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */

    public function createCategory(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    /**
     * Update an existing category.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */

    public function updateCategory(int $id, array $data): Category
    {
        return $this->categoryRepository->update($id, $data);
    }

    /**
     * Delete a category by its ID.
     *
     * @param int $id
     * @return bool
     */

    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

       /**
     * Get all cars belonging to a specific category with pagination.
     *
     * @param int $categoryId
     * @param int $perPage Number of cars per page.
     * @return LengthAwarePaginator
     */
    public function getCategoryCars(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->getCarsByCategory($categoryId, $perPage);
    }
}
