<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterWorkshopAdRequest;
use App\Http\Requests\StoreWorkshopAdRequest;
use App\Http\Requests\UpdateWorkshopAdRequest;
use App\Services\WorkshopAdService;
use App\Models\WorkshopAd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // تأكد من وجود هذا السطر
use App\Services\ImageService;
use App\Models\Image;
use App\Http\Requests\ImageRequest;

class WorkshopAdController extends Controller
{
    private WorkshopAdService $workshopAdService;
    private ImageService $imageService;

    public function __construct(WorkshopAdService $workshopAdService, ImageService $imageService)
    {
        $this->workshopAdService = $workshopAdService;
        $this->imageService = $imageService;
    }

    /**
     * Display a paginated listing of workshop ads with filters.
     *
     * @param FilterWorkshopAdRequest $request
     * @return JsonResponse
     */
    public function index(FilterWorkshopAdRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $request->query('per_page', 10);
        $workshopAds = $this->workshopAdService->getAllWorkshopAds($filters, $perPage);
        return response()->json($workshopAds, 200);
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

public function addImage(WorkshopAd $workshopAd, ImageRequest $request): JsonResponse
{
    // Überprüfen, ob der Benutzer berechtigt ist
    $user = $request->user();
    $workshop = $user->workshop;

    if (!$workshop || $workshopAd->workshop_id !== $workshop->id) {
        return response()->json(['message' => 'Nicht berechtigt, Bilder zu dieser Anzeige hinzuzufügen'], 403);
    }

    $data = $request->validated();
    $image = $this->imageService->addImageToCar($workshopAd, $data, $request->file('image'));

    return response()->json([
        'message' => 'Bild erfolgreich hinzugefügt',
        'image' => $image
    ], 201);
}

public function updateImage(Image $image, ImageRequest $request): JsonResponse
{
    // Überprüfen, ob der Benutzer berechtigt ist
    $user = $request->user();
    $workshop = $user->workshop;

    if (!$workshop || $image->imageable->workshop_id !== $workshop->id) {
        return response()->json(['message' => 'Nicht berechtigt, dieses Bild zu aktualisieren'], 403);
    }

    $data = $request->validated();
    $updatedImage = $this->imageService->updateImage($image, $data, $request->file('image'));

    return response()->json([
        'message' => 'Bild erfolgreich aktualisiert',
        'image' => $updatedImage
    ]);
}

public function deleteImage(Image $image, Request $request): JsonResponse
{
    // Überprüfen, ob der Benutzer berechtigt ist
    $user = $request->user();
    $workshop = $user->workshop;

    if (!$workshop || $image->imageable->workshop_id !== $workshop->id) {
        return response()->json(['message' => 'Nicht berechtigt, dieses Bild zu löschen'], 403);
    }

    $this->imageService->deleteImage($image);

    return response()->json(['message' => 'Bild erfolgreich gelöscht']);
}

}
