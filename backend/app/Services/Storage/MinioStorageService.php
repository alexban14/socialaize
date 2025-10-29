<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MinioStorageService implements StorageServiceInterface
{
    public function upload(UploadedFile $file, string $path = '/'): string
    {
        $path = Storage::disk('minio')->put($path, $file);

        return $path;
    }

    public function getUrl(string $path): string
    {
        return Storage::disk('minio')->url($path);
    }

    public function delete(string $path): bool
    {
        return Storage::disk('minio')->delete($path);
    }
}
