<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalStorageService implements StorageServiceInterface
{
    public function upload(UploadedFile $file, string $path = '/', string $visibility = 'private'): string
    {
        $options = ($visibility === 'public') ? ['visibility' => 'public'] : [];
        $path = Storage::disk('public')->put($path, $file, $options);

        return $path;
    }

    public function getUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    public function delete(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
