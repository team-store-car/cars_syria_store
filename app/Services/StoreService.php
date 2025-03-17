<?php
// Service for operation stores
namespace App\Services;

use App\Contracts\FileStorageInterface;
use App\Repositories\StoreRepository;

class StoreService
{
    protected $storeRepository;
    protected $fileStorageService;

    public function __construct(StoreRepository $storeRepository , FileStorageInterface $fileStorageService)
    {
        $this->storeRepository = $storeRepository;
        $this->fileStorageService = $fileStorageService;
    }

    public function getAllStores()
    {
        return $this->storeRepository->getAll();
    }

    public function getStoreById($id)
    {
        return $this->storeRepository->findById($id);
    }

    public function createStore(array $data)
    {
        if (isset($data['logo'])) {
            $data['logo'] = $this->fileStorageService->upload($data['logo'], 'stores/logos');
        }

        return $this->storeRepository->create($data);
    }

    public function updateStore($id, array $data)
    {
        $store = $this->storeRepository->findById($id);

        if (isset($data['logo'])) {
            if ($store->logo) {
                $this->fileStorageService->delete($store->logo);
            }
            $data['logo'] = $this->fileStorageService->upload($data['logo'], 'stores/logos');
        }

        return $this->storeRepository->update($id, $data);
    }

    public function deleteStore($id)
    {
        return $this->storeRepository->delete($id);
    }
}
