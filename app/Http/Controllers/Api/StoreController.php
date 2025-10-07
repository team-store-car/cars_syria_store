<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterStoreRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\StoreUpdateRequest;
use App\Http\Resources\CarCollection;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Services\StoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }
    //test

/**
     * Display a paginated listing of stores with filters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(FilterStoreRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->query('per_page', 15);
        $stores = $this->storeService->getAllStores($filters, $perPage);
        return response()->json(new StoreCollection($stores), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $store = $this->storeService->createStore($request->validated());
        return new StoreResource($store);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $store = $this->storeService->getStoreById($id);
        return new StoreResource($store);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateRequest $request,Store $store)
    {
        // return $request;
        $this->storeService->updateStore($store, $request->validated());
        return new StoreResource($store);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->storeService->deleteStore($id);
        return response()->json(['message' => 'Store deleted successfully']);
    }

     /**
     * Get all cars belonging to the owner of the specified store.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function cars(Request $request, $id): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $cars = $this->storeService->getStoreOwnerCars($id, (int) $perPage);
        return response()->json(new CarCollection($cars), 200);
    }
}
