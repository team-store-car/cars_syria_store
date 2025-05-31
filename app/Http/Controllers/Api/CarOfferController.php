<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarOfferRequest;
use App\Http\Requests\UpdateCarOfferRequest;
use App\Http\Resources\CarOfferResource;
use App\Models\Car;
use App\Models\CarOffer;
use App\Services\CarOfferService;
use Illuminate\Http\JsonResponse;

class CarOfferController extends Controller
{
    protected $carOfferService;

    public function __construct(CarOfferService $carOfferService)
    {
        $this->carOfferService = $carOfferService;
    }

    public function index(CarOfferRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->query('per_page', 10);
        $offers = $this->carOfferService->getAllOffers($filters, $perPage);
        return response()->json(CarOfferResource::collection($offers), 200);
    }

    public function store(CarOfferRequest $request, Car $car): JsonResponse
    {
        $offer = $this->carOfferService->createOffer($car, $request->validated());
        return response()->json(new CarOfferResource($offer), 201);
    }

    public function show(CarOffer $offer): JsonResponse
    {
        $offer = $this->carOfferService->getOfferById($offer->id);
        return response()->json(new CarOfferResource($offer), 200);
    }

    public function update(UpdateCarOfferRequest $request, CarOffer $offer): JsonResponse
    {
        $updatedOffer = $this->carOfferService->updateOffer($offer, $request->validated());
        return response()->json(new CarOfferResource($updatedOffer), 200);
    }

    public function destroy(CarOffer $offer): JsonResponse
    {
        $this->carOfferService->deleteOffer($offer);
        return response()->json(['message' => 'The offer has been successfully deleted.'], 200);
    }
}