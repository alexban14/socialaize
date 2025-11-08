<?php

namespace App\Services\UserProfile;

use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\UserProfile\UserProfileRepositoryInterface;

class UserProfileService implements UserProfileServiceInterface
{
    public function __construct(
        public readonly UserProfileRepositoryInterface $userProfileRepository
    ) {
    }

    public function createProfile(User $user, array $data): UserProfile
    {
        $data['user_id'] = $user->id;

        // If this is the user's first profile, set it to active
        if ($user->profiles()->count() === 0) {
            $data['is_active'] = true;
        }

        return $this->userProfileRepository->create($data);
    }

    public function updateProfile(User $user, string $profileType, array $data): UserProfile
    {
        $profile = $this->userProfileRepository->findByUserIdAndType($user->id, $profileType);

        if (!$profile) {
            $data['profile_type'] = $profileType;
            return $this->createProfile($user, $data);
        }

        $this->userProfileRepository->update($profile, $data);

        return $profile->fresh();
    }

    public function switchActiveProfile(User $user, string $profileType): ?UserProfile
    {
        $profile = $this->userProfileRepository->findByUserIdAndType($user->id, $profileType);

        if ($profile) {
            $this->userProfileRepository->setActive($user, $profile);
            return $profile->fresh();
        }

        return null;
    }

    public function getProfiles(User $user)
    {
        return $user->profiles()->get();
    }
}
