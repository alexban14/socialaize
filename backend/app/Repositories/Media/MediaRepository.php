<?php

namespace App\Repositories\Media;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;

class MediaRepository implements MediaRepositoryInterface
{
    public function create(array $data): Media
    {
        return Media::create($data);
    }

    public function attach(Media $media, Model $model, string $tag): void
    {
        $model->media()->attach($media->id, ['tag' => $tag]);
    }

    public function detach(Media $media, Model $model, string $tag): void
    {
        $model->media()->wherePivot('tag', $tag)->detach($media->id);
    }

    public function sync(array $mediaIds, Model $model, string $tag): void
    {
        $model->media()->wherePivot('tag', $tag)->sync($mediaIds);
    }
}
