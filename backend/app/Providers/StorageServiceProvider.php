<?php

namespace App\Providers;

use App\Enums\StorageDriver;
use App\Services\Storage\LocalStorageService;
use App\Services\Storage\MinioStorageService;
use App\Services\Storage\StorageServiceInterface;
use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(StorageServiceInterface::class, function ($app) {
            $driver = config('filesystems.default');

            return match ($driver) {
                StorageDriver::MINIO->value => new MinioStorageService(),
                default => new LocalStorageService(),
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
