<?php


namespace App\Services;

use App\Models\Car;
use App\Repositories\CarRepository;

class CarService
{
    protected CarRepository $carRepository;

    public function __construct(CarRepository $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function getAllCars(array $filters = [])
    {
        return $this->carRepository->all($filters);
    }

    public function getCarById(int $id)
    {
        return $this->carRepository->findById($id);
    }

    public function createCar(array $data)
    {
        return $this->carRepository->create($data);
    }

    public function updateCar(Car $car, array $data)
    {
        $update =  $this->carRepository->update($car, $data);
        return $update;
    }

    public function deleteCar(int $id)
    {
        return $this->carRepository->delete($id);
    }
}
