<?php

namespace App\Repositories\UserProfile;

use App\Models\User;
use App\Models\UserProfile;

interface UserProfileRepositoryInterface
{
    public function findByUserIdAndType(int $userId, string $type): ?UserProfile;

    public function create(array $data): UserProfile;

    public function update(UserProfile $profile, array $data): bool;

    public function setActive(User $user, UserProfile $profile): void;
}
