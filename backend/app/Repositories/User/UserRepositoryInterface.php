<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

interface UserRepositoryInterface
{
    public function query(): Builder;

    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findById(string $id): ?User;

    public function markEmailAsVerified(User $user): bool;

    public function update(User $user, array $data): bool;

    public function cacheClear(?User $user = null): void;
}
