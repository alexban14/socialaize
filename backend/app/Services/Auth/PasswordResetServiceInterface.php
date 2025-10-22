<?php

namespace App\Services\Auth;

use Closure;

interface PasswordResetServiceInterface
{
    public function sendResetLink(array $credentials): string;
    public function resetPassword(array $data, Closure $callback): string;
}
