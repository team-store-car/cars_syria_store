<?php

namespace App\Repositories;

use App\Filters\CarFilter;
use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CarRepository
{
    public function all(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = Car::query()->with([
            'category',
            'images',
            'user.store',
            'offer',
        ]);
        if($filters != null){
            $query = new CarFilter($query, $filters);
            return $query->apply()->paginate($perPage);
        }
        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Car
    {
        return Car::find($id);
    }

    public function create(array $data): Car
    {
        return Car::create($data);
    }

    public function update(Car $car, array $data)
    {
        $car->update($data);
        return $car;
    }

    public function delete(int $id): bool
    {
        return Car::destroy($id);
    }
}
