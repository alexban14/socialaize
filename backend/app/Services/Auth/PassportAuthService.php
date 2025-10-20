<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassportAuthService implements AuthServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): \App\Models\User
    {
        $user = $this->userRepository->create($data);

        $user->sendEmailVerificationNotification();

        return $user;
    }

    public function login(array $credentials): array | null
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = $this->userRepository->findByEmail($credentials['email']);
        $token = $user->createToken('authToken')->accessToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(Request $request): void
    {
        $request->user()->token()->revoke();
    }

    public function verifyEmail(string $id, string $hash, ?string $redirectUrl): ?string
    {
        $user = $this->userRepository->findById($id);

        if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return null;
        }

        if (!$user->hasVerifiedEmail()) {
            $this->userRepository->markEmailAsVerified($user);
        }

        $defaultUrl = config('socialaize.spa_url', 'http://localhost:6326') . '/auth?verified=1';

        if (!$this->isAllowedRedirect($redirectUrl)) {
            return $defaultUrl;
        }

        return $redirectUrl . '/auth?verified=1';
    }

    public function resendVerificationEmail(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }

    /**
     * Check if the given URL is in the allowed hosts list.
     *
     * @param string|null $url
     * @return bool
     */
    private function isAllowedRedirect(?string $url): bool
    {
        if (!$url) {
            return false;
        }

        $allowedHosts = config('auth.email_verification_redirect_allowlist', []);
        $host = parse_url($url, PHP_URL_HOST);

        // Also allow relative URLs which parse_url returns as null host
        if ($host === null && substr($url, 0, 1) === '/') {
            return true;
        }

        return in_array($host, $allowedHosts, true);
    }
}
