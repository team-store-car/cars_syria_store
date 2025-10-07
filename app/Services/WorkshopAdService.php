<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\WorkshopAd; // أضف هذا
use App\Repositories\WorkshopAdRepository;
use Illuminate\Http\JsonResponse;
use App\Helpers\AdHelper; // تأكد من أن هذا المساعد موجود ومناسب
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // لاستخدام صلاحيات Laravel (اختياري لكن جيد)

class WorkshopAdService
{
    private WorkshopAdRepository $workshopAdRepository;

    public function __construct(WorkshopAdRepository $workshopAdRepository)
    {
        $this->workshopAdRepository = $workshopAdRepository;
    }

    /**
     * Get all workshop ads with pagination and filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWorkshopAds(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->workshopAdRepository->all($filters, $perPage);
    }


    public function createWorkshopAd(array $data, Workshop $workshop): JsonResponse
    {
        // يمكنك الإبقاء على هذا الفحص أو نقله إلى Policy إذا كنت تستخدمها
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

    // خدمة تعديل الإعلان
    public function updateWorkshopAd(WorkshopAd $workshopAd, array $data, Workshop $userWorkshop): JsonResponse
    {
        // التحقق من الملكية - يجب أن يكون المستخدم هو مالك الورشة صاحبة الإعلان
        if ($workshopAd->workshop_id !== $userWorkshop->id && !(Auth::user()->hasRole('admin'))) {
             return response()->json(['message' => 'ليس لديك الصلاحية لتعديل هذا الإعلان'], 403);
             // أو استخدم نظام الصلاحيات في Laravel: Gate::authorize('update', $workshopAd);
        }

        $updated = $this->workshopAdRepository->update($workshopAd, $data);

        if ($updated) {
            // جلب المودل المحدث من قاعدة البيانات لضمان عرض أحدث البيانات
            return response()->json($workshopAd->fresh());
        }

        // يمكنك إضافة معالجة أكثر تفصيلاً للخطأ هنا إذا لزم الأمر
        return response()->json(['message' => 'حدث خطأ أثناء تعديل الإعلان'], 500);
    }

    // خدمة حذف الإعلان
    public function deleteWorkshopAd(WorkshopAd $workshopAd, Workshop $userWorkshop): JsonResponse
    {
        // التحقق من الملكية
        if ($workshopAd->workshop_id !== $userWorkshop->id && !(Auth::user()->hasRole('admin')) ) {
             return response()->json(['message' => 'ليس لديك الصلاحية لحذف هذا الإعلان'], 403);
             // أو استخدم نظام الصلاحيات في Laravel: Gate::authorize('delete', $workshopAd);
        }

        $deleted = $this->workshopAdRepository->delete($workshopAd);

        if ($deleted) {
            // لا يوجد محتوى لعرضه بعد الحذف بنجاح
            return response()->json(null, 204);
        }

        return response()->json(['message' => 'حدث خطأ أثناء حذف الإعلان'], 500);
    }
}
