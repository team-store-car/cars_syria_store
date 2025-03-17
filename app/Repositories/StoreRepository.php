<?php

//  Repository for Store Operations
namespace App\Repositories;

use App\Models\Store;

class StoreRepository
{
    public function getAll()
    {
        return Store::all();
    }

    public function findById($id)
    {
        return Store::findOrFail($id);
    }

    public function create(array $data)
    {
        return Store::create($data);
    }

    public function update($id, array $data)
    {
        $store = Store::findOrFail($id);
        $store->update($data);
        return $store;
    }

    public function delete($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();
        return true;
    }
}