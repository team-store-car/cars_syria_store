<?php
namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface FileStorageInterface
{
    public function upload(UploadedFile $file, string $path): string;
    public function delete(string $filePath): bool;
}