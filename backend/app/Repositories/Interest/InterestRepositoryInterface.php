<?php

namespace App\Repositories\Interest;

use App\Models\Interest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface InterestRepositoryInterface
{
    public function query(): Builder;

    public function getAll(): Collection;

    public function findById(int $id): ?Interest;

    public function findByName(string $name): ?Interest;

    public function create(array $data): Interest;

    public function cacheClear(?Interest $interest = null): void;
}
