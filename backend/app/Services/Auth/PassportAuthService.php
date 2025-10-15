<?php

namespace App\Services\Auth;

use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PassportAuthService implements AuthServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        $user = $this->userRepository->create($data);

        $token = $user->createToken('authToken')->accessToken;

        return ['user' => $user, 'token' => $token];
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
}
