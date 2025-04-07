<?php
namespace App\Services;

use App\Models\Workshop;
use App\Repositories\WorkshopAdRepository;
use Illuminate\Http\JsonResponse;
use App\Helpers\AdHelper;

class WorkshopAdService
{
    private WorkshopAdRepository $workshopAdRepository;

    public function __construct(WorkshopAdRepository $workshopAdRepository)
    {
        $this->workshopAdRepository = $workshopAdRepository;
    }

    public function createWorkshopAd(array $data, Workshop $workshop): JsonResponse
    {
        if (AdHelper::hasReachedDailyLimit($workshop, 'workshopAds')) {
            return response()->json(['message' => 'لا يمكنك نشر أكثر من 3 إعلانات يومياً'], 403);
        }

        $workshopAd = $this->workshopAdRepository->create([
            'workshop_id' => $workshop->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
        ]);

        return response()->json($workshopAd, 201);
    }
}
