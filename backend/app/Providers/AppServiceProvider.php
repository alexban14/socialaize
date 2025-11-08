<?php

namespace App\Providers;

use App\Repositories\Media\MediaRepository;
use App\Repositories\Media\MediaRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserProfile\UserProfileRepository;
use App\Repositories\UserProfile\UserProfileRepositoryInterface;
use App\Services\AI\AiService;
use App\Services\AI\AiServiceInterface;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Auth\PassportAuthService;
use App\Services\Auth\PasswordResetService;
use App\Services\Auth\PasswordResetServiceInterface;
use App\Services\Auth\UpdatePasswordService;
use App\Services\Auth\UpdatePasswordServiceInterface;
use App\Services\Media\MediaService;
use App\Services\Media\MediaServiceInterface;
use App\Services\User\UserService;
use App\Services\User\UserServiceInterface;
use App\Services\UserProfile\UserProfileService;
use App\Services\UserProfile\UserProfileServiceInterface;
use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        // services
        $this->app->bind(AuthServiceInterface::class, PassportAuthService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(PasswordResetServiceInterface::class, PasswordResetService::class);
        $this->app->bind(UpdatePasswordServiceInterface::class, UpdatePasswordService::class);
        $this->app->bind(MediaServiceInterface::class, MediaService::class);
        $this->app->bind(UserProfileServiceInterface::class, UserProfileService::class);
        $this->app->bind(AiServiceInterface::class, AiService::class);

        // repositories
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(UserProfileRepositoryInterface::class, UserProfileRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
