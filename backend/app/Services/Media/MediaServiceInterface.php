<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

interface MediaServiceInterface
{
    public function uploadAndAttach(Model $model, UploadedFile $file, string $tag, string $visibility = 'private'): Media;

    public function detach(Media $media, Model $model, string $tag): void;

    public function replace(Model $model, UploadedFile $file, string $tag, string $visibility = 'private'): Media;
}
