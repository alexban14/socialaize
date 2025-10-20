<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    public function register(array $data);

    public function login(array $credentials);

    public function logout(Request $request);

    public function verifyEmail(string $id, string $hash, ?string $redirectUrl): ?string;

    public function resendVerificationEmail(User $user): void;
}
