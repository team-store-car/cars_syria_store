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
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use App\Models\Car;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    /**
     * The car service instance.
     *
     * @var CarService
     */
    protected $carService;

    /**
     * The image service instance.
     *
     * @var ImageService
     */
    protected $imageService;

    /**
     * Create a new CarController instance.
     *
     * @param CarService $carService
     * @param ImageService $imageService
     */
    public function __construct(CarService $carService, ImageService $imageService)
    {
        $this->carService = $carService;
        $this->imageService = $imageService;
    }

    /**
     * Display a paginated listing of cars with filters.
     *
     * @param FilterCarRequest $request
     * @return JsonResponse
     */
    public function index(FilterCarRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->query('per_page', 10);
        $cars = $this->carService->getAllCars($filters, $perPage);
        return response()->json(new CarCollection($cars), 200);
    }

    /**
     * Store a newly created car.
     *
     * @param StoreCarRequest $request
     * @return JsonResponse
     */
    public function store(StoreCarRequest $request): JsonResponse
    {
        $car = $this->carService->createCar($request->validated());
        return response()->json(new CarResource($car), 201);
    }

    /**
     * Display the specified car.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $car = $this->carService->getCarById($id);
        return response()->json(new CarResource($car), 200);
    }

    /**
     * Update the specified car.
     *
     * @param UpdateCarRequest $request
     * @param Car $car
     * @return JsonResponse
     */
    public function update(UpdateCarRequest $request, Car $car): JsonResponse
    {
        $updatedCar = $this->carService->updateCar($car, $request->validated());
        return response()->json(new CarResource($updatedCar), 200);
    }

    /**
     * Delete the specified car.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->carService->deleteCar($id);
        return response()->json(['message' => 'Car deleted successfully'], 200);
    }

    /**
     * Add an image to the specified car.
     *
     * @param Car $car
     * @param ImageRequest $request
     * @return JsonResponse
     */
    public function addImage(Car $car, ImageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $image = $this->imageService->addImageToCar($car, $data, $request->file('image'));
        return response()->json(['message' => 'Image added successfully', 'image' => $image], 201);
    }

    /**
     * Update the specified image.
     *
     * @param Image $image
     * @param ImageRequest $request
     * @return JsonResponse
     */
    public function updateImage(Image $image, ImageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $updatedImage = $this->imageService->updateImage($image, $data, $request->file('image'));
        return response()->json(['message' => 'Image updated successfully', 'image' => $updatedImage], 200);
    }

    /**
     * Delete the specified image.
     *
     * @param Image $image
     * @return JsonResponse
     */
    public function deleteImage(Image $image): JsonResponse
    {
        $this->imageService->deleteImage($image);
        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
}