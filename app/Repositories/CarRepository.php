<?php

namespace App\Repositories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;

class CarRepository
{
    public function all(): Collection
    {
        return Car::all();
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
