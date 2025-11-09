<?php

namespace App\Services\Interest;

use App\Models\Interest;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Collection;

interface InterestServiceInterface
{
    public function getAllInterests(): Collection;

    public function findOrCreate(string $interestName): Interest;

    public function addInterestToProfile(Interest $interest, UserProfile $profile): void;

    public function removeInterestFromProfile(Interest $interest, UserProfile $profile): void;
}
