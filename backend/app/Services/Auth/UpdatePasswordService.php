<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordService implements UpdatePasswordServiceInterface
{
    public function update(User $user, array $data): bool
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            return false;
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return true;
    }
}
