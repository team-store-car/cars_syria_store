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
     /**
     * Store a single image for an imageable entity.
     *
     * @param mixed $imageable The entity to associate the image with (e.g., Car).
     * @param array $data Additional image data (alt_text, is_primary).
     * @param mixed $file The uploaded image file.
     * @param string $directory The storage directory for the image.
     * @return Image The created image instance.
     */
    public function storeSingleImage($imageable, array $data, $file, string $directory): Image
    {
        $this->authorizeImageAction($imageable);

        $path = $file->store($directory, 'public');
        $imageData = [
            'path' => $path,
            'alt_text' => $data['alt_text'] ?? null,
            'is_primary' => $data['is_primary'] ?? false,
        ];

        $image = $this->imageRepository->create($imageData, $imageable);
        if ($image->is_primary) {
            $this->imageRepository->setPrimaryImage($image, $imageable);
        }

        return $image;
    }

    /**
     * Store multiple images for an imageable entity.
     *
     * @param mixed $imageable The entity to associate the images with (e.g., Car).
     * @param array $files Array of uploaded image files.
     * @param array $data Additional image data (alt_texts, is_primary).
     * @param string $directory The storage directory for the images.
     * @return array Array of created image instances.
     */
    public function storeMultipleImages($imageable, array $files, array $data, string $directory): array
    {
        $this->authorizeImageAction($imageable);
        $images = [];

        foreach ($files as $index => $file) {
            $path = $file->store($directory, 'public');
            $imageData = [
                'path' => $path,
                'alt_text' => $data['alt_texts'][$index] ?? null,
                'is_primary' => isset($data['is_primary']) && $data['is_primary'] == $index,
            ];

            $image = $this->imageRepository->create($imageData, $imageable);

            if ($image->is_primary) {
                $this->imageRepository->setPrimaryImage($image, $imageable);
            }

            $images[] = $image;
        }

        return $images;
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