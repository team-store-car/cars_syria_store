<?php

namespace App\Services;

use App\Helpers\CarPermissionHelper;
use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

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
        return $this->carRepository->create($data);
    }

    public function updateCar(Car $car, array $data)
    {
        $this->authorizeCarAction($car);
        return $this->carRepository->update($car, $data);
    }

    public function deleteCar(int $id)
    {
        $car = $this->getCarById($id);
        $this->authorizeCarAction($car);
        return $this->carRepository->delete($id);
    }

    protected function authorizeCarAction(Car $car): void
    {
        if ($car->user_id !== Auth::id()) {
            throw new AuthorizationException('غير مصرح لك بإجراء هذا الإجراء على هذه السيارة.');
        }
    }
}