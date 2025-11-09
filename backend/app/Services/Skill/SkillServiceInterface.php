<?php

namespace App\Services\Skill;

use App\Models\Skill;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Collection;

interface SkillServiceInterface
{
    public function getAllSkills(): Collection;

    public function findOrCreate(string $skillName): Skill;

    public function addSkillToProfile(Skill $skill, UserProfile $profile): void;

    public function removeSkillFromProfile(Skill $skill, UserProfile $profile): void;
}
