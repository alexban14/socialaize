<?php

namespace App\Services\Skill;

use App\Models\Skill;
use App\Models\UserProfile;
use App\Repositories\Skill\SkillRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SkillService implements SkillServiceInterface
{
    public function __construct(
        private readonly SkillRepositoryInterface $skillRepository
    ) {
    }

    public function getAllSkills(): Collection
    {
        return $this->skillRepository->getAll();
    }

    public function findOrCreate(string $skillName): Skill
    {
        $skill = $this->skillRepository->findByName($skillName);

        if (!$skill) {
            $skill = $this->skillRepository->create(['name' => $skillName]);
        }

        return $skill;
    }

    public function addSkillToProfile(Skill $skill, UserProfile $profile): void
    {
        $profile->skills()->syncWithoutDetaching([$skill->id]);
    }

    public function removeSkillFromProfile(Skill $skill, UserProfile $profile): void
    {
        $profile->skills()->detach($skill->id);
    }
}
