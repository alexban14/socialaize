<?php

namespace App\Repositories\Skill;

use App\Models\Skill;
use Illuminate\Cache\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SkillRepository implements SkillRepositoryInterface
{
    public function __construct(
        private readonly Skill $model,
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
        return $this->cacheRepository->rememberForever('skills.all', function () {
            return $this->model->all();
        });
    }

    public function findById(int $id): ?Skill
    {
        return $this->cacheRepository->rememberForever("skills.{$id}", function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function findByName(string $name): ?Skill
    {
        return $this->model->where('name', $name)->first();
    }

    public function create(array $data): Skill
    {
        return $this->connection->transaction(function () use ($data) {
            $skill = $this->model->create($data);
            $this->cacheClear();
            return $skill;
        });
    }

    public function cacheClear(?Skill $skill = null): void
    {
        if ($skill) {
            $this->cacheRepository->forget("skills.{$skill->id}");
        }
        $this->cacheRepository->forget('skills.all');
    }
}