<?php

namespace App\Repositories;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageRepository
{
    public function create(array $data, $imageable): Image
    {
        return Image::create([
            'imageable_id' => $imageable->id,
            'imageable_type' => get_class($imageable),
            'path' => $data['path'],
            'alt_text' => $data['alt_text'] ?? null,
            'is_primary' => $data['is_primary'] ?? false,
        ]);
    }

    public function update(Image $image, array $data): Image
    {
        if (isset($data['path']) && $image->path !== $data['path']) {
            Storage::disk('public')->delete($image->path);
        }
        $image->update($data);
        return $image;
    }

    public function delete(Image $image): bool
    {
        Storage::disk('public')->delete($image->path);
        return $image->delete();
    }

    public function setPrimaryImage(Image $image, $imageable): void
    {
        $imageable->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
    }
}