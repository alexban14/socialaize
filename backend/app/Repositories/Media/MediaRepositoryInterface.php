<?php

namespace App\Repositories\Media;

use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface MediaRepositoryInterface
{
    public function query(): Builder;

    public function findById(string $id): ?Media;

    public function create(array $data): Media;

    public function delete(Media $media): bool;

    public function attach(Media $media, Model $model, string $tag): void;

    public function detach(Media $media, Model $model, string $tag): void;

    public function sync(array $mediaIds, Model $model, string $tag): void;
}