<?php

namespace App\Repositories\UserProfile;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Cache\Repository;
use Illuminate\Database\Connection;

class UserProfileRepository implements UserProfileRepositoryInterface
{
    public function __construct(
        public readonly UserProfile $userProfile,
        public readonly Connection $connection,
        public readonly Repository $cacheRepository
    ) {
    }

    public function findByUserIdAndType(int $userId, string $type): ?UserProfile
    {
        return $this->cacheRepository->rememberForever("user.{$userId}.profile.{$type}", function () use ($userId, $type) {
            return $this->userProfile->where('user_id', $userId)->where('profile_type', $type)->first();
        });
    }

    public function create(array $data): UserProfile
    {
        $profile = $this->userProfile->create($data);
        $this->cacheClear($profile);
        return $profile;
    }

    public function update(UserProfile $profile, array $data): bool
    {
        $updated = $profile->update($data);
        $this->cacheClear($profile);
        return $updated;
    }

    public function setActive(User $user, UserProfile $profile): void
    {
        $this->connection->transaction(function () use ($user, $profile) {
            $user->profiles()->update(['is_active' => false]);
            $profile->update(['is_active' => true]);
            foreach ($user->profiles as $p) {
                $this->cacheClear($p);
            }
        });
    }

    public function cacheClear(UserProfile $profile): void
    {
        $this->cacheRepository->forget("user.{$profile->user_id}.profile.{$profile->profile_type->value}");
    }
}
