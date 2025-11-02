<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Repositories\Media\MediaRepositoryInterface;
use App\Services\Storage\StorageServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class MediaService implements MediaServiceInterface
{
    public function __construct(
        public readonly MediaRepositoryInterface $mediaRepository,
        public readonly StorageServiceInterface $storageService
    ) {
        //
    }

    public function uploadAndAttach(Model $model, UploadedFile $file, string $tag, string $visibility = 'private'): Media
    {
        $path = $this->storageService->upload($file, $model->getTable() . '/' . $model->getKey(), $visibility);

        $media = $this->mediaRepository->create([
            'disk' => config('filesystems.default'),
            'directory' => dirname($path),
            'filename' => basename($path),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        $this->mediaRepository->attach($media, $model, $tag);

        return $media;
    }

    public function detach(Media $media, Model $model, string $tag): void
    {
        $this->mediaRepository->detach($media, $model, $tag);
        $this->storageService->delete($media->directory . '/' . $media->filename);
        $this->mediaRepository->delete($media);
    }

    public function replace(Model $model, UploadedFile $file, string $tag, string $visibility = 'private'): Media
    {
        // Detach existing media for this tag
        $existingMedia = $model->media()->wherePivot('tag', $tag)->first();
        if ($existingMedia) {
            $this->detach($existingMedia, $model, $tag);
        }

        // Upload and attach new media
        return $this->uploadAndAttach($model, $file, $tag, $visibility);
    }
}
