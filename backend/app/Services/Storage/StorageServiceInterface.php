<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;

interface StorageServiceInterface
{
    public function upload(UploadedFile $file, string $path = '/', string $visibility = 'private'): string;

    public function getUrl(string $path): string;

    public function delete(string $path): bool;
}
