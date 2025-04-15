<?php

namespace App\Repositories;

use App\Models\InspectionRequest; // استيراد الموديل الخاص بطلبات الفحص

class InspectionRequestRepository
{
    /**
     * إنشاء سجل طلب فحص جديد.
     *
     * @param array $data بيانات الطلب الجديد.
     * @return InspectionRequest الطلب المُنشأ.
     */
    public function create(array $data): InspectionRequest
    {
        // استخدام موديل Eloquent مباشرة لإنشاء السجل
        return InspectionRequest::create($data);
    }

    /**
     * تحديث بيانات طلب فحص موجود.
     *
     * @param InspectionRequest $inspectionRequest الطلب المراد تحديثه.
     * @param array $data البيانات الجديدة.
     * @return InspectionRequest الطلب بعد تحديثه.
     */
    public function update(InspectionRequest $inspectionRequest, array $data): InspectionRequest
    {
        // تحديث بيانات الكائن مباشرة
        $inspectionRequest->update($data);
        // إرجاع الكائن المحدث
        return $inspectionRequest->refresh(); // استخدام refresh لضمان الحصول على أحدث البيانات من قاعدة البيانات
        // أو ببساطة: return $inspectionRequest;
    }

    /**
     * حذف طلب فحص.
     *
     * @param InspectionRequest $inspectionRequest الطلب المراد حذفه.
     * @return void
     */
    public function delete(InspectionRequest $inspectionRequest): void
    {
        // حذف الكائن مباشرة
        $inspectionRequest->delete();
    }

    /**
     * البحث عن طلب فحص بواسطة الـ ID.
     *
     * @param int $id معرف الطلب.
     * @return InspectionRequest|null الطلب إذا وجد، وإلا null.
     */
    public function find(int $id): ?InspectionRequest
    {
        return InspectionRequest::find($id);
    }

     /**
      * البحث عن طلبات فحص خاصة بمستخدم معين.
      *
      * @param int $userId معرف المستخدم.
      * @return \Illuminate\Database\Eloquent\Collection مجموعة الطلبات.
      */
     public function findByUser(int $userId): \Illuminate\Database\Eloquent\Collection
     {
         return InspectionRequest::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
     }

      /**
      * البحث عن طلبات فحص خاصة بورشة معينة.
      *
      * @param int $workshopId معرف الورشة.
      * @return \Illuminate\Database\Eloquent\Collection مجموعة الطلبات.
      */
     public function findByWorkshop(int $workshopId): \Illuminate\Database\Eloquent\Collection
     {
         return InspectionRequest::where('workshop_id', $workshopId)->orderBy('created_at', 'desc')->get();
     }
}