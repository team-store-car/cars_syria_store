<?php

namespace App\Services;

use App\Helpers\CarPermissionHelper;
use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CarService
{
    protected CarRepository $carRepository;
    protected ImageService $imageService;

    public function __construct(CarRepository $carRepository, ImageService $imageService)
    {
        $this->carRepository = $carRepository;
        $this->imageService = $imageService;
    }

    public function getAllCars(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->carRepository->all($filters, $perPage);
    }

    public function getCarById(int $id)
    {
        $car = $this->carRepository->findById($id);
        if (!$car) {
            throw new \Exception('السيارة غير موجودة', 404);
        }
        return $car;
    }

    public function createCar(array $data)
    {
        $user = Auth::user();

        if (!CarPermissionHelper::canCreateCar($user, 'cars')) {
            throw new AuthorizationException(
                $user->role === 'workshop'
                    ? 'مستخدمو الورش لا يمكنهم إنشاء سيارات.'
                    : 'لقد وصلت إلى الحد الأقصى للسيارات.'
            );
        }

        $data['user_id'] = $user->id;
        $car = $this->carRepository->create($data);

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $index => $image) {
                $this->imageService->addImageToCar($car, [
                    'alt_text' => $data['alt_texts'][$index] ?? null,
                    'is_primary' => $index === 0, // Set first image as primary
                ], $image);
            }
        }

        return $car;
    }

    public function updateCar(Car $car, array $data)
    {
        $this->authorizeCarAction($car);
        $updatedCar = $this->carRepository->update($car, $data);

        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $index => $image) {
                $this->imageService->addImageToCar($updatedCar, [
                    'alt_text' => $data['alt_texts'][$index] ?? null,
                    'is_primary' => $index === 0,
                ], $image);
            }
        }

        return $updatedCar;
    }

    public function deleteCar(int $id)
    {

        $car = $this->getCarById($id);
        $this->authorizeCarAction($car);
        return $this->carRepository->delete($id);
    }

    protected function authorizeCarAction(Car $car): void
    {

        $user = Auth::user();

        if ($car->user_id !== Auth::id() && !$user->hasRole('admin')) {
            throw new AuthorizationException('غير مصرح لك بإجراء هذا الإجراء على هذه السيارة.');
        }
    }
}
