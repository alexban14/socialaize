<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findById(string $id): ?User;

    public function markEmailAsVerified(User $user): bool;

    public function update(User $user, array $data): bool;
}
