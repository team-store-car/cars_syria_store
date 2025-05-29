<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Image;
use App\Repositories\ImageRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    protected ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function addImageToCar(Car $car, array $data, $file): Image
    {
        $this->authorizeImageAction($car);
        $path = $file->store('car_images', 'public');
        $imageData = [
            'path' => $path,
            'alt_text' => $data['alt_text'] ?? null,
            'is_primary' => $data['is_primary'] ?? false,
        ];
        $image = $this->imageRepository->create($imageData, $car);
        if ($image->is_primary) {
            $this->imageRepository->setPrimaryImage($image, $car);
        }
        return $image;
    }

    public function updateImage(Image $image, array $data, $file = null): Image
    {
        $this->authorizeImageAction($image->imageable);
        if ($file) {
            $data['path'] = $file->store('car_images', 'public');
        }
        return $this->imageRepository->update($image, $data);
    }

    public function deleteImage(Image $image): bool
    {
        $this->authorizeImageAction($image->imageable);
        return $this->imageRepository->delete($image);
    }

    protected function authorizeImageAction($imageable): void
    {
        if ($imageable->user_id !== Auth::id()) {
            throw new AuthorizationException('غير مصرح لك بإجراء هذا الإجراء على هذه الصورة.');
        }
    }
}