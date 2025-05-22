<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    protected $carService;





    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }

    public function index(): JsonResponse
    {
        $cars = $this->carService->getAllCars();
        return response()->json(CarResource::collection($cars), 200);
    }

    public function store(StoreCarRequest $request): JsonResponse
    {
        $car = $this->carService->createCar($request->validated());
        return response()->json(new CarResource($car), 201);
    }

    public function show($id): JsonResponse
    {
        $car = $this->carService->getCarById($id);
        return response()->json(new CarResource($car), 200);
    }

    public function update(UpdateCarRequest $request, Car $car)
    {

        return  $request->all();
        $updatedCar = $this->carService->updateCar($car, $request->validate());
        return response()->json(new CarResource($updatedCar), 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->carService->deleteCar($id);
        return response()->json(['message' => 'Car deleted successfully'], 200);
    }


    public function searchAndFilterCars(array $validatedData): LengthAwarePaginator
    {



    }
}

