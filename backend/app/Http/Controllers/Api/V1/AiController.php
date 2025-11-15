<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProfileType;
use App\Http\Controllers\Controller;
use App\Services\AI\AiServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class AiController extends Controller
{
    public function __construct(
        public readonly AiServiceInterface $aiService
    ) {
    }

    public function synthesizeProfile(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string',
            'profile_type' => ['required', new Enum(ProfileType::class)],
        ]);

        $this->aiService->synthesizeProfileFromPost(
            $request->user(),
            $request->input('content'),
            $request->input('profile_type')
        );

        return response()->json(['message' => 'Profile synthesis started.']);
    }
}