<?php
// Service for operation stores
namespace App\Services;

use App\Contracts\FileStorageInterface;
use App\Models\Store;
use App\Repositories\StoreRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class StoreService
{
    protected $storeRepository;

    /**
     * The image service instance.
     *
     * @var ImageService
     */
    protected $imageService;

    /**
     * Create a new StoreService instance.
     *
     * @param StoreRepository $storeRepository
     * @param ImageService $imageService
     */

    public function __construct(StoreRepository $storeRepository , ImageService $imageService)
    {
        $this->storeRepository = $storeRepository;
        $this->imageService = $imageService;
    }

     /**
     * Get all stores with pagination.
     *
     * @param int $perPage Number of stores per page.
     * @return LengthAwarePaginator
     */
    public function getAllStores(int $perPage = 15): LengthAwarePaginator
    {
        return $this->storeRepository->getAll($perPage);
    }
    /**
     * Get a store by its ID.
     *
     * @param int $id
     * @return Store
     */

    public function getStoreById($id)
    {
        return $this->storeRepository->findById($id);
    }
     /**
     * Create a new store and associate a logo image if provided.
     *
     * @param array $data Store data including optional logo file and alt_text.
     * @return Store The created store instance.
     */

    public function createStore(array $data)
    {

        $user = Auth::user();
         // Store the logo image if provided
         $logoData = [
            'alt_text' => $data['alt_text'] ?? 'Store logo',
            'is_primary' => true, // Logo is typically the primary image
        ];

        // Remove logo and alt_text from data to avoid storing in stores table
        $logoFile = $data['logo'] ?? null;
        unset($data['logo'], $data['alt_text']);
        $data['user_id'] = $user->id;
        // Create the store
        $store = $this->storeRepository->create($data);

        // Associate the logo image with the store
        if ($logoFile) {
            $this->imageService->storeSingleImage(
                imageable: $store,
                data: $logoData,
                file: $logoFile,
                directory: 'stores/logos'
            );
        }

        return $store;
    }

       /**
     * Update an existing store and its logo if provided.
     *
     * @param Store $store The store of the store to update.
     * @param array $data Store data including optional logo file and alt_text.
     * @return Store The updated store instance.
     */

    public function updateStore(Store $store , array $data)
    {

        $this->authorizeCarAction($store);
        // Handle logo update if provided
        if (isset($data['logo'])) {
            // Delete the existing primary image (logo) if it exists
            if ($logo = $store->logo()) {
                $this->imageService->deleteImage($logo);
            }

            // Store the new logo image
            $logoData = [
                'alt_text' => $data['alt_text'] ?? 'Store logo',
                'is_primary' => true, // Logo is typically the primary image
            ];

            $this->imageService->storeSingleImage(
                imageable: $store,
                data: $logoData,
                file: $data['logo'],
                directory: 'stores/logos'
            );
        }

        // Remove logo and alt_text from data to avoid storing in stores table
        unset($data['logo'], $data['alt_text']);

        // Update the store
        return $this->storeRepository->update($store, $data);
    }

     /**
     * Delete a store and its associated logo by its ID.
     *
     * @param int $id
     * @return bool
     */

    public function deleteStore($id)
    {
               // Find the store
               $store = $this->storeRepository->findById($id);
               $this->authorizeCarAction($store);
               // Delete the logo if it exists
               if ($logo = $store->logo()) {
                   $this->imageService->deleteImage($logo);
               }

               // Delete the store
               return $this->storeRepository->delete($id);
    }


    /**
     * Get all cars belonging to the owner of a store with pagination.
     *
     * @param int $storeId
     * @param int $perPage Number of cars per page.
     * @return LengthAwarePaginator
     */
    public function getStoreOwnerCars(int $storeId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->storeRepository->getCarsByStoreOwner($storeId, $perPage);
    }


    /**
     * Authorize the store action for the given store.
     *
     * @param Store $store
     * @throws AuthorizationException
     */
    
    protected function authorizeCarAction(Store $store): void
    {

        if ($store->user_id !== Auth::id()) {
            throw new AuthorizationException('غير مصرح لك بإجراء هذا الإجراء على هذه السيارة.');
        }
    }
}
