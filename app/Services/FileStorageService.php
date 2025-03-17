<?php


namespace App\Services;

use App\Contracts\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService implements FileStorageInterface
{
    public function upload(UploadedFile $file, string $path): string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($path, $fileName, 'public');

        return $filePath;
    }

    public function delete(string $filePath): bool
    {
        return Storage::disk('public')->exists($filePath) ? Storage::disk('public')->delete($filePath) : false;
    }
}
