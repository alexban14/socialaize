<?php

namespace App\Services\AI;

use App\Models\User;

interface AiServiceInterface
{
    public function synthesizeProfileFromPost(User $user, string $postContent, string $profileType): void;
}
