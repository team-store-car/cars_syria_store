<?php

namespace App\Repositories;

use App\Filters\CarFilter;
use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CarRepository
{
    public function all(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Car::query();
        $filter = new CarFilter($query, $filters);
        return $filter->apply()->paginate($perPage);
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
