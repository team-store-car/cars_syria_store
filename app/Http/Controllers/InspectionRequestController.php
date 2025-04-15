<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionRequestRequest;
use App\Services\InspectionRequestService;
use Illuminate\Http\JsonResponse; 
use Illuminate\Http\Request; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InspectionRequestController extends Controller
{
     use AuthorizesRequests;
    protected InspectionRequestService $inspectionRequestService;

    public function __construct(InspectionRequestService $inspectionRequestService)
    {
        $this->inspectionRequestService = $inspectionRequestService;
    }

    public function store(StoreInspectionRequestRequest $request): JsonResponse 
    {
        $user = $request->user(); 
        $validatedData = $request->validated(); 

        $inspectionRequest = $this->inspectionRequestService->createInspectionRequest($validatedData, $user);

       
        return response()->json([
            'message' => 'تم إرسال طلب الفحص بنجاح.',
            'data' => $inspectionRequest
        ], 201); 
    }


    public function destroy($id)
    {
        $inspectionRequest = $this->inspectionRequestService->getRequestForAuthorization($id);
    
        $this->authorize('delete', $inspectionRequest);
    
        $success = $this->inspectionRequestService->deleteInspectionRequest($id);
    
        if (!$success) {
            return response()->json(['message' => 'Inspection request not found.'], 404);
        }
    
        return response()->json(['message' => 'Inspection request deleted successfully.']);
    }
    

}