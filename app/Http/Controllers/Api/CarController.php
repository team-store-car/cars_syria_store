<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterCarRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarCollection;
use App\Http\Resources\CarResource;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    protected $carService;
    protected ImageService $imageService;




    public function __construct(CarService $carService , ImageService $imageService)
    {
        $this->carService = $carService;
        $this->imageService = $imageService;
    }

    public function index(FilterCarRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->query('per_page', 10);
        $cars = $this->carService->getAllCars($filters, $perPage);
        return response()->json(new CarCollection($cars), 200);
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

    public function update(UpdateCarRequest $request, Car $car): JsonResponse
    {
        $updatedCar = $this->carService->updateCar($car, $request->validated());
        return response()->json(new CarResource($updatedCar), 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->carService->deleteCar($id);
        return response()->json(['message' => 'Car deleted successfully'], 200);
    }
    public function addImage(Car $car, ImageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $image = $this->imageService->addImageToCar($car, $data, $request->file('image'));
        return response()->json(['message' => 'Image added successfully', 'image' => $image], 201);
    }

    public function updateImage(Image $image, ImageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $updatedImage = $this->imageService->updateImage($image, $data, $request->file('image'));
        return response()->json(['message' => 'Image updated successfully', 'image' => $updatedImage], 200);
    }

    public function deleteImage(Image $image): JsonResponse
    {
        $this->imageService->deleteImage($image);
        return response()->json(['message' => 'Image deleted successfully'], 200);
    }


    // public function searchAndFilterCars(array $validatedData): LengthAwarePaginator
    // {



    // }
}

