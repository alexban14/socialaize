<?php

namespace App\Services\UserProfile;

use App\Models\User;
use App\Models\UserProfile;

interface UserProfileServiceInterface
{
    public function createProfile(User $user, array $data): UserProfile;

    public function updateProfile(User $user, string $profileType, array $data): UserProfile;

    public function switchActiveProfile(User $user, string $profileType): ?UserProfile;

    public function getProfiles(User $user);
}
