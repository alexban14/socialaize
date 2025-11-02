# Minio Integration Documentation

This document outlines the integration of Minio for object storage in the Laravel backend.

## 1. Overview

The application now supports Minio as a file storage driver. This is achieved through Laravel's Filesystem abstraction, allowing for a flexible and scalable approach to file storage. The implementation uses a service-oriented architecture with a design pattern that allows for easy switching between different storage providers (e.g., local, Minio, S3).

## 2. Configuration

### 2.1. Filesystem Configuration

A new disk named `minio` has been added to `config/filesystems.php`:

```php
'minio' => [
    'driver' => 's3',
    'key' => env('MINIO_ACCESS_KEY_ID'),
    'secret' => env('MINIO_SECRET_ACCESS_KEY'),
    'region' => env('MINIO_DEFAULT_REGION'),
    'bucket' => env('MINIO_BUCKET'),
    'url' => env('MINIO_URL'),
    'endpoint' => env('MINIO_ENDPOINT'),
    'use_path_style_endpoint' => env('MINIO_USE_PATH_STYLE_ENDPOINT', true),
    'throw' => false,
],
```

### 2.2. Environment Variables

The following environment variables have been added to `.env.development` and `.env.example`:

```
MINIO_ACCESS_KEY_ID=socializeAI-admin
MINIO_SECRET_ACCESS_KEY=admin123
MINIO_DEFAULT_REGION=us-east-1
MINIO_BUCKET=socialaize
MINIO_URL=http://localhost:6320
MINIO_ENDPOINT=http://minio:9000
MINIO_USE_PATH_STYLE_ENDPOINT=true
```

In `.env.development`, the `FILESYSTEM_DISK` has been set to `minio`.

## 3. Architecture

The implementation follows a layered architecture and uses the Strategy design pattern to allow for interchangeable storage drivers.

### 3.1. StorageDriver Enum

An `Enum` located at `app/Enums/StorageDriver.php` defines the available storage drivers:

```php
namespace App\Enums;

enum StorageDriver: string
{
    case LOCAL = 'local';
    case MINIO = 'minio';
}
```

### 3.2. StorageServiceInterface

An interface located at `app/Services/Storage/StorageServiceInterface.php` defines the contract for all storage services:

```php
namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;

interface StorageServiceInterface
{
    public function upload(UploadedFile $file, string $path = '/'): string;
    public function getUrl(string $path): string;
    public function delete(string $path): bool;
}
```

### 3.3. Concrete Implementations

Two concrete implementations of `StorageServiceInterface` have been created:

-   `app/Services/Storage/MinioStorageService.php`: Implements the interface using the `minio` disk.
-   `app/Services/Storage/LocalStorageService.php`: Implements the interface using the `public` disk.

### 3.4. StorageServiceProvider

A new service provider at `app/Providers/StorageServiceProvider.php` dynamically binds the correct storage service implementation based on the `FILESYSTEM_DISK` environment variable. This provider is registered in `bootstrap/providers.php`.

## 4. API Endpoint

A new API endpoint has been created to handle file uploads:

-   **URL:** `POST /api/v1/media`
-   **Controller:** `app/Http/Controllers/Api/V1/MediaUploadController.php`
-   **Authentication:** This route is protected by the `auth:api` middleware, meaning only authenticated users can upload files.

## 5. Usage

To switch between storage drivers, simply change the `FILESYSTEM_DISK` variable in your `.env` file to either `local` or `minio`.
