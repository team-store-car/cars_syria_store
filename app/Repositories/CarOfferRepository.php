<?php

namespace App\Repositories;

use App\Models\CarOffer;
use Illuminate\Pagination\LengthAwarePaginator;

class CarOfferRepository
{
    public function all(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = CarOffer::query()->with('car');

        if (isset($filters['listing_type'])) {
            $query->where('listing_type', $filters['listing_type']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?CarOffer
    {
        return CarOffer::with('car')->find($id);
    }

    public function create(array $data): CarOffer
    {
        return CarOffer::create($data);
    }

    public function update(CarOffer $offer, array $data): CarOffer
    {
        $offer->update($data);
        return $offer;
    }

    public function delete(int $id): bool
    {
        return CarOffer::destroy($id);
    }
}