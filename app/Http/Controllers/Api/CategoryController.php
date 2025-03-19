<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json(CategoryResource::collection($categories));
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        return $category ? response()->json(new CategoryResource($category)) : response()->json(['error' => 'Not Found'], 404);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return response()->json(new CategoryResource($category), 201);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $updated = $this->categoryService->updateCategory($id, $request->validated());
        return $updated
            ? response()->json(['message' => 'Updated successfully'])
            : response()->json(['error' => 'Not Found'], 404);
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->categoryService->deleteCategory($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['error' => 'Not Found'], 404);
    }
}
