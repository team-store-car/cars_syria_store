<?php

namespace App\Services;

use App\Models\Car;
use App\Models\CarOffer;
use App\Repositories\CarOfferRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CarOfferService
{
    protected CarOfferRepository $carOfferRepository;

    public function __construct(CarOfferRepository $carOfferRepository)
    {
        $this->carOfferRepository = $carOfferRepository;
    }

    public function getAllOffers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->carOfferRepository->all($filters, $perPage);
    }

    public function getOfferById(int $id): CarOffer
    {
        $offer = $this->carOfferRepository->findById($id);
        if (!$offer) {
            throw new \Exception('the offer not found', 404);
        }
        return $offer;
    }

    public function createOffer(Car $car, array $data): CarOffer
    {
        $this->authorizeOfferAction($car);

        $data['car_id'] = $car->id;
        $offer = $this->carOfferRepository->create($data);

        return $offer;
    }

    public function updateOffer(CarOffer $offer, array $data): CarOffer
    {
        $this->authorizeOfferAction($offer->car);
        $updatedOffer = $this->carOfferRepository->update($offer, $data);
        return $updatedOffer;
    }

    public function deleteOffer(CarOffer $offer): bool
    {
        $this->authorizeOfferAction($offer->car);
        return $this->carOfferRepository->delete($offer->id);
    }

    protected function authorizeOfferAction(Car $car): void
    {
        if ($car->user_id !== Auth::id()) {
            throw new AuthorizationException('غير مصرح لك بإجراء هذا الإجراء على هذا العرض.');
        }
    }
}