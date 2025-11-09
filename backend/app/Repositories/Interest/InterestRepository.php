<?php

namespace App\Repositories\Interest;

use App\Models\Interest;
use Illuminate\Cache\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class InterestRepository implements InterestRepositoryInterface
{
    public function __construct(
        private readonly Interest $model,
        private readonly Repository $cacheRepository,
        private readonly Connection $connection
    ) {
    }

    public function query(): Builder
    {
        return $this->model->query();
    }

    public function getAll(): Collection
    {
        return $this->cacheRepository->rememberForever('interests.all', function () {
            return $this->model->all();
        });
    }

    public function findById(int $id): ?Interest
    {
        return $this->cacheRepository->rememberForever("interests.{$id}", function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function findByName(string $name): ?Interest
    {
        return $this->model->where('name', $name)->first();
    }

    public function create(array $data): Interest
    {
        return $this->connection->transaction(function () use ($data) {
            $interest = $this->model->create($data);
            $this->cacheClear();
            return $interest;
        });
    }

    public function cacheClear(?Interest $interest = null): void
    {
        if ($interest) {
            $this->cacheRepository->forget("interests.{$interest->id}");
        }
        $this->cacheRepository->forget('interests.all');
    }
}
