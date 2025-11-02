<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        public readonly User $user,
        public readonly Hasher $hasher,
        public readonly Repository $cacheRepository,
        public readonly Connection $connection
    ) {
        //
    }

    public function query(): Builder
    {
        return $this->user->query();
    }

    public function create(array $data): User
    {
        return $this->connection->transaction(function () use ($data) {
            if (array_key_exists('password', $data)) {
                $data['password'] = $this->hasher->make($data['password']);
            }

            $user = $this->user->create($data);
            $this->cacheClear($user);

            return $user;
        });
    }

    public function findByEmail(string $email): ?User
    {
        $user = $this->where('email', $email)->first();

        return $this->cacheRepository->rememberForever("users.{$user->id}", function () use ($user) {
            return $user;
        });
    }

    public function findById(string $id): ?User
    {
        return $this->cacheRepository->rememberForever("users.{$id}", function () use ($id) {
            return $this->user->find($id);
        });
    }

    public function markEmailAsVerified(User $user): bool
    {
        return $this->connection->transaction(function () use ($user) {
            return $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
        });
    }

    public function update(User $user, array $data): bool
    {
        return $this->connection->transaction(function () use ($user, $data) {
            $user->fill($data);

            if ($user->isClean()) {
                return false;
            }

            $this->cacheClear($user);

            return $user->save();
        });
    }

    public function cacheClear(?User $user = null): void
    {
        if ($user) {
            $this->cacheRepository->forget("users.{$user->id}");
        }

        $this->cacheRepository->forget('users.all');
    }
}
