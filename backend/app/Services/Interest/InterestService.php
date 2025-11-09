<?php

namespace App\Services\Interest;

use App\Models\Interest;
use App\Models\UserProfile;
use App\Repositories\Interest\InterestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InterestService implements InterestServiceInterface
{
    public function __construct(
        private readonly InterestRepositoryInterface $interestRepository
    ) {
    }

    public function getAllInterests(): Collection
    {
        return $this->interestRepository->getAll();
    }

    public function findOrCreate(string $interestName): Interest
    {
        $interest = $this->interestRepository->findByName($interestName);

        if (!$interest) {
            $interest = $this->interestRepository->create(['name' => $interestName]);
        }

        return $interest;
    }

    public function addInterestToProfile(Interest $interest, UserProfile $profile): void
    {
        $profile->interests()->syncWithoutDetaching([$interest->id]);
    }

    public function removeInterestFromProfile(Interest $interest, UserProfile $profile): void
    {
        $profile->interests()->detach($interest->id);
    }
}
