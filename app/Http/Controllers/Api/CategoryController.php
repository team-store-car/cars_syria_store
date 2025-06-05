<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CarCollection;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * The category service instance.
     *
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * Create a new CategoryController instance.
     *
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display all categories.
     */

     public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json(['data' => CategoryResource::collection($categories)]);
    }

    /**
     * Display the specified category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        return $category ? response()->json(new CategoryResource($category)) : response()->json(['error' => 'Not Found'], 404);
    }

    /**
     * Store a newly created category.
     *
     * @param StoreCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return response()->json(new CategoryResource($category), 201);
    }

    /**
     * Update an existing category.
     *
     * @param UpdateCategoryRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        // dd($request->all());
        $updated = $this->categoryService->updateCategory($id, $request->validated());
        return $updated
            ? response()->json(new CategoryResource($updated),201,['sucessful' => 'update category success'])
            : response()->json(['error' => 'Not Found'], 404);
    }

    /**
     * Delete a category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->categoryService->deleteCategory($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['error' => 'Not Found'], 404);
    }

    /**
     * Get all cars belonging to the specified category.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function cars(Request $request, int $id): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $cars = $this->categoryService->getCategoryCars($id, (int) $perPage);
        return response()->json(new CarCollection($cars), 200);
    }
}