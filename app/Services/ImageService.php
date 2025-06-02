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