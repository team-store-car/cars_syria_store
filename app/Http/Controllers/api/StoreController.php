<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\StoreUpdateRequest;
use App\Http\Resources\StoreResource;
use App\Services\StoreService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = $this->storeService->getAllStores();
        return StoreResource::collection($stores);
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
    public function update(StoreUpdateRequest $request, $id)
    {
        // return $request;
        $store = $this->storeService->updateStore($id, $request->validated());
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
}
