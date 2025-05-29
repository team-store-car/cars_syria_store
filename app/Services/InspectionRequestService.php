<?php
namespace App\Services;

use App\Models\InspectionRequest;
use App\Models\User;
use App\Models\Workshop;
use App\Notifications\NewInspectionRequestNotification;
// استيراد الكلاس الملموس مباشرة
use App\Repositories\InspectionRequestRepository; // <-- تغيير هنا
use Illuminate\Support\Facades\Notification;

class InspectionRequestService
{
    // استخدام الكلاس الملموس كـ Type Hint
    protected InspectionRequestRepository $inspectionRequestRepository; // <-- تغيير هنا

    public function __construct(
        // حقن الكلاس الملموس
        InspectionRequestRepository $inspectionRequestRepository // <-- تغيير هنا
        )
    {
        $this->inspectionRequestRepository = $inspectionRequestRepository;
    }

    // باقي الكود في الـ Service يبقى كما هو...
    public function createInspectionRequest(array $validatedData, User $user): InspectionRequest
    {
        $dataToCreate = array_merge($validatedData, [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // استدعاء الدالة في الـ Repository الملموس
        $inspectionRequest = $this->inspectionRequestRepository->create($dataToCreate);

        $workshop = Workshop::find($inspectionRequest->workshop_id);
        if ($workshop) {

            Notification::send($workshop, new NewInspectionRequestNotification($inspectionRequest));
        }

        return $inspectionRequest;
    }


    public function deleteInspectionRequest(int $id): bool
    {
        $inspectionRequest = $this->inspectionRequestRepository->find($id);
   
        if (!$inspectionRequest) {
            Log::warning("Attempted to delete non-existent inspection request with ID: " . $id);
            return false; // <- يُرجع bool
        }
   
   
        try {
            $this->inspectionRequestRepository->delete($inspectionRequest);
            return true;
   
        } catch (\Exception $e) {
            Log::error("Failed to delete inspection request ID: {$id}. Error: " . $e->getMessage());
            return false; 
        }
   
    }

    public function getRequestForAuthorization(int $id): ?InspectionRequest
{
    return $this->inspectionRequestRepository->find($id);
}
}