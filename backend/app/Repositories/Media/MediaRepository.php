<?php

namespace App\Repositories\Media;

use App\Models\Media;
use Illuminate\Cache\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaRepository implements MediaRepositoryInterface
{
    public function __construct(
        public readonly Media $media,
        public readonly Repository $cacheRepository,
        public readonly Connection $connection
    ) {
        //
    }

    public function query(): Builder
    {
        return $this->media->query();
    }

    public function findById(string $id): ?Media
    {
        return $this->cacheRepository->rememberForever("media.{$id}", function () use ($id) {
            return $this->media->find($id);
        });
    }

    public function create(array $data): Media
    {
        return $this->connection->transaction(function () use ($data) {
            $media = $this->media->create($data);
            $this->cacheClear();
            return $media;
        });
    }

    public function delete(Media $media): bool
    {
        return $this->connection->transaction(function () use ($media) {
            $this->cacheClear($media);
            return $media->delete();
        });
    }

    public function attach(Media $media, Model $model, string $tag): void
    {
        $this->connection->transaction(function () use ($media, $model, $tag) {
            $model->media()->attach($media->id, ['tag' => $tag]);
        });
    }

    public function detach(Media $media, Model $model, string $tag): void
    {
        $this->connection->transaction(function () use ($media, $model, $tag) {
            $model->media()->wherePivot('tag', $tag)->detach($media->id);
        });
    }

    public function sync(array $mediaIds, Model $model, string $tag): void
    {
        $this->connection->transaction(function () use ($mediaIds, $model, $tag) {
            $model->media()->wherePivot('tag', $tag)->sync($mediaIds);
        });
    }

    public function cacheClear(?Media $media = null): void
    {
        if ($media) {
            $this->cacheRepository->forget("media.{$media->id}");
        }

        $this->cacheRepository->forget('media.all');
    }
}