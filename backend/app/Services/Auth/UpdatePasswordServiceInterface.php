<?php

namespace App\Services\Auth;

use App\Models\User;

interface UpdatePasswordServiceInterface
{
    public function update(User $user, array $data): bool;
}
