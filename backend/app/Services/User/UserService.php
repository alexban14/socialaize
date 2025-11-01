<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getById(string $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function update(User $user, array $data): User
    {
        $this->userRepository->update($user, $data);
        return $user->fresh();
    }
}
