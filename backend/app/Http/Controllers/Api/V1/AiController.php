<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AI\AiServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        ]);

        $this->aiService->synthesizeProfileFromPost($request->user(), $request->input('content'));

        return response()->json(['message' => 'Profile synthesis started.']);
    }
}