# Media Management Architecture

This document outlines the architecture of the media management system used in the Socialaize backend. It is designed to be flexible, allowing any model (like `User`, `Post`, etc.) to have associated media files (images, videos) stored efficiently using drivers like Minio or local storage.

## 1. Overview

The system is built around a polymorphic many-to-many relationship. This means a single `media` record can be associated with multiple other models, and any model can have multiple media files attached to it with specific tags (e.g., 'avatar', 'cover_image', 'post_attachment').

This is achieved through:
- A `media` table to store metadata about each file.
- A `mediables` pivot table to link media to other models.
- A `HasMedia` trait that can be added to any Eloquent model.
- A set of services and repositories to handle the business logic of file storage and database records.

## 2. Database Schema

Two main tables power this system.

### The `media` Table

This table stores metadata for every uploaded file. The file itself is stored in the configured filesystem (e.g., Minio).

*Migration: `2025_10_31_114336_create_media_table.php`*
```php
Schema::create('media', function (Blueprint $table) {
    $table->id();
    $table->string('disk');
    $table->string('directory');
    $table->string('filename');
    $table->string('extension');
    $table->string('mime_type');
    $table->unsignedBigInteger('size');
    $table->timestamps();
});
```

### The `mediables` Table

This is the polymorphic pivot table. It connects a `media` record to any other model record in the application.

*Migration: `2025_10_31_120812_create_mediables_table.php`*
```php
Schema::create('mediables', function (Blueprint $table) {
    $table->foreignId('media_id')->constrained()->onDelete('cascade');
    $table->morphs('mediable'); // Creates `mediable_id` (BIGINT) and `mediable_type` (VARCHAR)
    $table->string('tag');      // A tag to identify the media's purpose (e.g., 'avatar')
    $table->primary(['media_id', 'mediable_id', 'mediable_type', 'tag']);
});
```
- `mediable_id`: The ID of the model the media is attached to (e.g., a `users.id`).
- `mediable_type`: The class name of the model (e.g., `App\Models\User`).
- `tag`: A string to categorize the media (e.g., 'avatar', 'cover_image').

## 3. The `HasMedia` Trait

This trait (`App\Models\Concerns\HasMedia.php`) can be included in any Eloquent model to give it media handling capabilities.

```php
trait HasMedia
{
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')->withPivot('tag');
    }

    public function getAvatarAttribute()
    {
        return $this->media()->wherePivot('tag', 'avatar')->first()?->url;
    }

    public function getCoverImageAttribute()
    {
        return $this->media()->wherePivot('tag', 'cover_image')->first()?->url;
    }
}
```

- **`media()`**: This method defines the `morphToMany` relationship with the `Media` model.
- **`getAvatarAttribute()` / `getCoverImageAttribute()`**: These are Eloquent Accessors. They provide a convenient way to get a user's avatar or cover image URL directly (e.g., `$user->avatar`). The `wherePivot('tag', ...)` clause correctly filters the `mediables` table to find the media with the specified tag.

## 4. Core Services

The logic is abstracted into several service layers for flexibility and maintainability.

### `StorageServiceInterface` & Implementations
- **Interface**: `App\Services\Storage\StorageServiceInterface.php` defines a contract for storage operations (`upload`, `getUrl`, `delete`).
- **Implementations**: `MinioStorageService.php` and `LocalStorageService.php` provide the actual logic for interacting with Minio and the local filesystem, respectively.
- **`StorageServiceProvider`**: This service provider (`App\Providers\StorageServiceProvider.php`) dynamically binds the correct storage service based on the `FILESYSTEM_DISK` setting in the `.env` file. This makes it easy to switch between local development and a cloud storage provider like Minio.

### `MediaRepositoryInterface`
- **Interface**: `App\Repositories\Media\MediaRepositoryInterface.php` abstracts the database interactions for creating and attaching/detaching media records.

### `MediaService`
- **Class**: `App\Services\Media\MediaService.php` is the main service that orchestrates the entire process.
- **`uploadAndAttach()`**: Uploads a file using the `StorageService` and then creates a `Media` record and a `mediables` pivot record using the `MediaRepository`.
- **`replace()`**: This is a key method. It first finds and detaches any existing media with the same tag for a given model (deleting the old file and database records), then calls `uploadAndAttach()` to add the new one. This ensures a model only has one of each unique media type (e.g., one avatar).

## 5. Example Flow: Updating a User's Avatar

Here is the step-by-step flow when a user uploads a new avatar via `POST /api/v1/user`:

1.  **`UserController`**: The `update` method receives the request. It validates the uploaded file.
2.  **`MediaService` Call**: The controller calls `$this->mediaService->replace($user, $request->file('avatar'), 'avatar');`.
3.  **`MediaService->replace()`**:
    a. It finds the user's existing media tagged 'avatar' (e.g., `old_avatar.jpg`).
    b. It calls its own `detach()` method on the old media.
    c. **`detach()`**:
        i. It deletes the pivot record from the `mediables` table.
        ii. It calls the `StorageService` to delete the actual file (`old_avatar.jpg`) from Minio.
        iii. It deletes the record from the `media` table.
    d. After detaching, it calls `uploadAndAttach()` with the new file.
4.  **`MediaService->uploadAndAttach()`**:
    a. **`StorageService->upload()`**: The `MinioStorageService` uploads the new file to the Minio bucket (e.g., into a `users/1/` directory).
    b. **`MediaRepository->create()`**: A new record is created in the `media` table with the new file's metadata (filename, disk, size, etc.).
    c. **`MediaRepository->attach()`**: A new record is created in the `mediables` pivot table, linking the new `media` ID to the `user` ID with the `tag` 'avatar'.
5.  **`UserController` Response**: The controller reloads the `User` model. The `getAvatarAttribute` accessor is automatically called, which queries the relationship and returns the new avatar's public URL from Minio storage. This updated user object is returned as a JSON response.
