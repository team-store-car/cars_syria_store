<?php

namespace App\Services;

use App\Models\Image;
use App\Repositories\ImageRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    protected ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function addImageToCar(Model $model, array $data, $file): Image
    {
        $this->authorizeImageAction($model);
        $path = $file->store('images', 'public');
        $imageData = [
            'path' => $path,
            'alt_text' => $data['alt_text'] ?? null,
            'is_primary' => $data['is_primary'] ?? false,
        ];
        $image = $this->imageRepository->create($imageData, $model);
        if ($image->is_primary) {
            $this->imageRepository->setPrimaryImage($image, $model);
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
            // Altes Bild löschen
            if ($image->path) {
                Storage::disk('public')->delete($image->path);
            }
            $data['path'] = $file->store('images', 'public');
        }
        return $this->imageRepository->update($image, $data);
    }

    public function deleteImage(Image $image): bool
    {
        $this->authorizeImageAction($image->imageable);
        // Bild von der Festplatte löschen
        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }
        return $this->imageRepository->delete($image);
    }

    protected function authorizeImageAction($model): void
    {
        // Für Workshop-Anzeigen
        if (method_exists($model, 'workshop')) {
            $workshop = $model->workshop;
            if ($workshop && $workshop->user_id !== Auth::id()) {
                throw new AuthorizationException('Sie sind nicht berechtigt, Bilder für diese Anzeige zu verwalten.');
            }
        }
        // Für Autos
        elseif (property_exists($model, 'user_id') && $model->user_id !== Auth::id()) {
            throw new AuthorizationException('Sie sind nicht berechtigt, Bilder für dieses Objekt zu verwalten.');
        }
    }
}