<?php

namespace App\Services\Auth;

use Closure;
use Illuminate\Support\Facades\Password;

class PasswordResetService implements PasswordResetServiceInterface
{
    public function sendResetLink(array $credentials): string
    {
        return Password::sendResetLink($credentials);
    }

    public function resetPassword(array $data, Closure $callback): string
    {
        return Password::reset($data, $callback);
    }
}
