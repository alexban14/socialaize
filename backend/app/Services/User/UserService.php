<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        public readonly UserRepositoryInterface $userRepository
    ) {
        //
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
