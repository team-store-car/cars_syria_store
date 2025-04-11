<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopRequest;
use App\Http\Requests\UpdateWorkshopRequest;
use App\Models\Workshop;
use App\Services\WorkshopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    protected WorkshopService $workshopService;

    public function __construct(WorkshopService $workshopService)
    {
        $this->workshopService = $workshopService;
    }

    public function store(StoreWorkshopRequest $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validated();
        $workshop = $this->workshopService->createWorkshop($validated, $user);

        return response()->json($workshop, 201);
    }

    public function update(UpdateWorkshopRequest $request, Workshop $workshop): JsonResponse
    {
        $user = $request->user();

        $updated = $this->workshopService->updateWorkshop($workshop, $request->validated(), $user);

        return response()->json($updated);
    }

    public function destroy(Request $request, Workshop $workshop): JsonResponse
    {
        $user = $request->user();

        $this->workshopService->deleteWorkshop($workshop, $user);

        return response()->json(null, 204);
    }
}

