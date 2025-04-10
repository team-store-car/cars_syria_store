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
        // --- نقطة تسجيل 1: بداية الدالة ---
        Log::info('WorkshopAdController@store: === Entered store method ===');

        $user = $request->user(); // يفضل الحصول على المستخدم مرة واحدة

        // --- نقطة تسجيل 2: قبل الحصول على الورشة ---
        Log::info('WorkshopAdController@store: Attempting to get workshop for user.', ['user_id' => $user?->id]);

        $workshop = $user?->workshop; // استخدم ?-> للأمان

        // --- نقطة تسجيل 3: بعد الحصول على الورشة ---
        Log::info('WorkshopAdController@store: Got workshop.', ['workshop_id' => $workshop?->id]);

        if (!$workshop) {
            // --- نقطة تسجيل 4: حالة عدم وجود ورشة ---
            Log::info('WorkshopAdController@store: User does not own a workshop. Returning 403.');
            return response()->json(['message' => 'يجب أن تكون مالك ورشة لإنشاء إعلان'], 403);
        }

        $validatedData = $request->validated();

        // --- نقطة تسجيل 5: قبل استدعاء الـ Service ---
        Log::info('WorkshopAdController@store: Calling WorkshopAdService->createWorkshopAd.', [
            'workshop_id' => $workshop->id,
            'validated_data_keys' => array_keys($validatedData) // لعرض مفاتيح البيانات فقط
        ]);

        $result = $this->workshopAdService->createWorkshopAd($validatedData, $workshop);

        // --- نقطة تسجيل 6: بعد استدعاء الـ Service (فقط إذا وصل التنفيذ إلى هنا) ---
        Log::info('WorkshopAdController@store: === Returned from WorkshopAdService, sending response ===', ['status_code' => $result->getStatusCode()]);

        return $result;
    }

    // ... باقي دوال الكنترولر ...
}