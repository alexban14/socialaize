<?php

namespace App\Repositories\Skill;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface SkillRepositoryInterface
{
    public function query(): Builder;

    public function getAll(): Collection;

    public function findById(int $id): ?Skill;

    public function findByName(string $name): ?Skill;

    public function create(array $data): Skill;

    public function cacheClear(?Skill $skill = null): void;
}
