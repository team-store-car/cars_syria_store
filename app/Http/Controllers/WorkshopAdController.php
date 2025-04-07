<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopAdRequest;
use App\Services\WorkshopAdService;
use Illuminate\Http\JsonResponse;

class WorkshopAdController extends Controller
{
    private WorkshopAdService $workshopAdService;

    public function __construct(WorkshopAdService $workshopAdService)
    {
        $this->workshopAdService = $workshopAdService;
    }

    public function store(StoreWorkshopAdRequest $request): JsonResponse
    {
        $workshop = auth()->user()->workshop;

        if (!$workshop) {
            return response()->json(['message' => 'يجب أن تكون مالك ورشة لإنشاء إعلان'], 403);
        }

        return $this->workshopAdService->createWorkshopAd($request->validated(), $workshop);
    }
}
