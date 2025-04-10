<?php
namespace App\Services;

use App\Models\Shop;
use App\Repositories\ShopAdRepository;
use Illuminate\Http\JsonResponse;
use App\Helpers\AdHelper;

class ShopAdService
{
    private ShopAdRepository $shopAdRepository;

    public function __construct(ShopAdRepository $shopAdRepository)
    {
        $this->shopAdRepository = $shopAdRepository;
    }

    public function createShopAd(array $data, Shop $shop): JsonResponse
    {
        if (AdHelper::hasReachedDailyLimit($shop, 'shopAds')) {
            return response()->json(['message' => 'لا يمكنك نشر أكثر من 3 إعلانات يومياً'], 403);
        }

        $shopAd = $this->shopAdRepository->create([
            'shop_id' => $shop->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
        ]);

        return response()->json($shopAd, 201);
    }
}
