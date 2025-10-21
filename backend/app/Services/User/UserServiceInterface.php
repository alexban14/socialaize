<?php

namespace App\Services\User;

use App\Models\User;

interface UserServiceInterface
{
    public function getById(string $id): ?User;
}
