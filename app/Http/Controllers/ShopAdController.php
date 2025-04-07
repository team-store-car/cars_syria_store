<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreShopAdRequest;
use App\Services\ShopAdService;
use Illuminate\Http\JsonResponse;

class ShopAdController extends Controller
{
    private ShopAdService $shopAdService;

    public function __construct(ShopAdService $shopAdService)
    {
        $this->shopAdService = $shopAdService;
    }

    public function store(StoreShopAdRequest $request): JsonResponse
    {
        $shop = auth()->user()->shop;

        if (!$shop) {
            return response()->json(['message' => 'يجب أن تكون مالك معرض سيارات لإنشاء إعلان'], 403);
        }

        return $this->shopAdService->createShopAd($request->validated(), $shop);
    }
}
  