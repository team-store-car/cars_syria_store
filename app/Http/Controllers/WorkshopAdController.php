<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopAdRequest;
use App\Http\Requests\UpdateWorkshopAdRequest;
use App\Services\WorkshopAdService;
use App\Models\WorkshopAd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // تأكد من وجود هذا السطر

class WorkshopAdController extends Controller
{
    private WorkshopAdService $workshopAdService;

    public function __construct(WorkshopAdService $workshopAdService)
    {
        $this->workshopAdService = $workshopAdService;
    }

    public function store(StoreWorkshopAdRequest $request): JsonResponse
    {

        $user = $request->user(); // يفضل الحصول على المستخدم مرة واحدة


        $workshop = $user?->workshop; // استخدم ?-> للأمان


        if (!$workshop) {
            return response()->json(['message' => 'يجب أن تكون مالك ورشة لإنشاء إعلان'], 403);
        }

        $validatedData = $request->validated();

     

        $result = $this->workshopAdService->createWorkshopAd($validatedData, $workshop);


        return $result;
    }
    public function update(UpdateWorkshopAdRequest $request, WorkshopAd $workshopAd): JsonResponse
{
    // الحصول على المستخدم الحالي
    $user = $request->user();

    // التأكد من وجود ورشة للمستخدم
    $workshop = $user->workshop;
    if (!$workshop) {
        return response()->json(['message' => 'يجب أن تكون مالك ورشة لتحديث الإعلان'], 403);
    }

    // التحقق من صحة البيانات الواردة
    $validatedData = $request->validated();

    // استدعاء خدمة التحديث الموجودة بالخدمة WorkshopAdService
    return $this->workshopAdService->updateWorkshopAd($workshopAd, $validatedData, $workshop);
}


public function destroy(Request $request, WorkshopAd $workshopAd): JsonResponse
{
    $user = $request->user();

    // الحصول على الورشة الخاصة بالمستخدم للتحقق من الملكية
    $workshop = $user->workshop;
    if (!$workshop) {
        return response()->json(['message' => 'يجب أن تكون مالك ورشة لحذف الإعلان'], 403);
    }

    // استدعاء خدمة الحذف الموجودة في WorkshopAdService
    return $this->workshopAdService->deleteWorkshopAd($workshopAd, $workshop);
}

}