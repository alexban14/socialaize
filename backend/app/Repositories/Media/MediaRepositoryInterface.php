<?php

namespace App\Repositories\Media;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

interface MediaRepositoryInterface
{
    public function create(array $data): Media;

    public function attach(Media $media, Model $model, string $tag): void;

    public function detach(Media $media, Model $model, string $tag): void;

    public function sync(array $mediaIds, Model $model, string $tag): void;
}
