<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Interest\InterestServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function __construct(
        private readonly InterestServiceInterface $interestService
    ) {
    }

    public function index(): JsonResponse
    {
        $interests = $this->interestService->getAllInterests();
        return response()->json($interests);
    }

    public function userInterests(Request $request, string $profileType): JsonResponse
    {
        $profile = $request->user()->profiles()->where('profile_type', $profileType)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        return response()->json($profile->interests);
    }

    public function store(Request $request, string $profileType): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $profile = $request->user()->profiles()->where('profile_type', $profileType)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $interest = $this->interestService->findOrCreate($request->input('name'));

        $this->interestService->addInterestToProfile($interest, $profile);

        return response()->json($interest, 201);
    }

    public function destroy(Request $request, string $profileType, int $interestId): JsonResponse
    {
        $profile = $request->user()->profiles()->where('profile_type', $profileType)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $interest = $this->interestService->getAllInterests()->find($interestId);

        if (!$interest) {
            return response()->json(['message' => 'Interest not found.'], 404);
        }

        $this->interestService->removeInterestFromProfile($interest, $profile);

        return response()->json(null, 204);
    }
}
