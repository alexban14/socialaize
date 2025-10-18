<?php

namespace App\Providers;

use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Auth\PassportAuthService;
use App\Services\Auth\PasswordResetService;
use App\Services\Auth\PasswordResetServiceInterface;
use App\Services\User\UserService;
use App\Services\User\UserServiceInterface;
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

        // repositories
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
