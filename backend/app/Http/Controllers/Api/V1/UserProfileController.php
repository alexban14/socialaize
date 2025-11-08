<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\UserProfile\UserProfileServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class UserProfileController extends Controller
{
    public function __construct(
        public readonly UserProfileServiceInterface $userProfileService
    ) {
    }

    #[OA\Get(
        path: '/api/v1/user/profiles',
        summary: 'Get all profiles for the authenticated user',
        tags: ['Profiles'],
        security: [['BearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $profiles = $this->userProfileService->getProfiles($request->user());
        return response()->json($profiles);
    }

    #[OA\Post(
        path: '/api/v1/user/profiles',
        summary: 'Create a new profile for the authenticated user',
        tags: ['Profiles'],
        security: [['BearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['profile_type'],
                properties: [
                    new OA\Property(property: 'profile_type', type: 'string', enum: ['personal', 'business', 'academic', 'creator']),
                    new OA\Property(property: 'title', type: 'string', example: 'Software Engineer'),
                    new OA\Property(property: 'bio', type: 'string', example: 'Experienced software engineer...'),
                    new OA\Property(property: 'location', type: 'string', example: 'San Francisco, CA'),
                    new OA\Property(property: 'website', type: 'string', example: 'https://example.com'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Profile created successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'profile_type' => 'required|string|in:personal,business,academic,creator',
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        $profileType = $validatedData['profile_type'];
        unset($validatedData['profile_type']);

        $profile = $this->userProfileService->updateProfile(
            $request->user(),
            $profileType,
            $validatedData
        );

        return response()->json($profile, 201);
    }

    #[OA\Put(
        path: '/api/v1/user/profiles/{profile_type}',
        summary: 'Update a specific profile for the authenticated user',
        tags: ['Profiles'],
        security: [['BearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'profile_type',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['personal', 'business', 'academic', 'creator'])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Senior Software Engineer'),
                    new OA\Property(property: 'bio', type: 'string', example: 'Lead developer with 10 years of experience...'),
                    new OA\Property(property: 'location', type: 'string', example: 'New York, NY'),
                    new OA\Property(property: 'website', type: 'string', example: 'https://my-portfolio.com'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Profile updated successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, string $profileType): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $profile = $this->userProfileService->updateProfile($request->user(), $profileType, $validator->validated());

        return response()->json($profile);
    }

    #[OA\Post(
        path: '/api/v1/user/profiles/active/{profile_type}',
        summary: 'Set a profile as active for the authenticated user',
        tags: ['Profiles'],
        security: [['BearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'profile_type',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['personal', 'business', 'academic', 'creator'])
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Active profile switched successfully'),
            new OA\Response(response: 404, description: 'Profile not found'),
        ]
    )]
    public function setActive(Request $request, string $profileType): JsonResponse
    {
        $profile = $this->userProfileService->switchActiveProfile($request->user(), $profileType);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }
}