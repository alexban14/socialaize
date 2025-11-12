<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Skill\SkillServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function __construct(
        private readonly SkillServiceInterface $skillService
    ) {
    }

    public function index(): JsonResponse
    {
        $skills = $this->skillService->getAllSkills();
        return response()->json($skills);
    }

    public function userSkills(Request $request, string $profileType): JsonResponse
    {
        $profile = $request->user()->profiles()->where('profile_type', $profileType)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        return response()->json($profile->skills);
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

        $skill = $this->skillService->findOrCreate($request->input('name'));

        $this->skillService->addSkillToProfile($skill, $profile);

        return response()->json($skill, 201);
    }

    public function destroy(Request $request, string $profileType, int $skillId): JsonResponse
    {
        $profile = $request->user()->profiles()->where('profile_type', $profileType)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $skill = $this->skillService->getAllSkills()->find($skillId);

        if (!$skill) {
            return response()->json(['message' => 'Skill not found.'], 404);
        }

        $this->skillService->removeSkillFromProfile($skill, $profile);

        return response()->json(null, 204);
    }
}
