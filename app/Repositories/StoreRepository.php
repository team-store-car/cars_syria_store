<?php

//  Repository for Store Operations
namespace App\Repositories;

use App\Filters\StoreFilter;
use App\Models\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StoreRepository
{
     /**
     * Get all stores with pagination and filters.
     *
     * @param array $filters Filters to apply.
     * @param int $perPage Number of stores per page.
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Store::query()->with([
            'owner',
            'images',
        ]);
        $filter = new StoreFilter($query, $filters);
        return $filter->apply()->paginate($perPage);
    }

     /**
     * Find a store by its ID.
     *
     * @param int $id
     * @return Store
     */

    public function findById($id)
    {
        return Store::findOrFail($id);
    }

    /**
     * Create a new store.
     *
     * @param array $data
     * @return Store
     */

    public function create(array $data)
    {
        return Store::create($data);
    }

     /**
     * Update an existing store.
     *
     * @param Store $store
     * @param array $data
     * @return Store
     */

    public function update(Store $store, array $data)
    {
        $store->update($data);
        return $store;
    }

    /**
     * Delete a store by its ID.
     *
     * @param int $id
     * @return bool
     */

    public function delete($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();
        return true;
    }

    /**
     * Get all cars belonging to the owner of a store with pagination.
     *
     * @param int $storeId
     * @param int $perPage Number of cars per page.
     * @return LengthAwarePaginator
     */
    public function getCarsByStoreOwner(int $storeId, int $perPage = 15): LengthAwarePaginator
    {
        $store = $this->findById($storeId);
        return $store->owner->cars()->paginate($perPage);
    }
}
