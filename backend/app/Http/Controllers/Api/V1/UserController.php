<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Media\MediaServiceInterface;
use App\Services\User\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    protected UserServiceInterface $userService;
    protected MediaServiceInterface $mediaService;

    public function __construct(UserServiceInterface $userService, MediaServiceInterface $mediaService)
    {
        $this->userService = $userService;
        $this->mediaService = $mediaService;
    }

    #[OA\Get(
        path: '/api/v1/user',
        summary: 'Get authenticated user',
        tags: ['Users'],
        security: [['BearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function show(Request $request): JsonResponse
    {
        $user = $this->userService->getById($request->user()->id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    #[OA\Put(
        path: '/api/v1/user',
        summary: 'Update authenticated user profile',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Test User'),
                        new OA\Property(property: 'bio', type: 'string', example: 'This is a bio.'),
                        new OA\Property(property: 'website', type: 'string', format: 'url', example: 'https://example.com'),
                        new OA\Property(property: 'location', type: 'string', example: 'New York, USA'),
                        new OA\Property(property: 'avatar', type: 'string', format: 'binary', description: 'Avatar image file'),
                        new OA\Property(property: 'cover_image', type: 'string', format: 'binary', description: 'Cover image file'),
                    ]
                )
            )
        ),
        tags: ['Users'],
        security: [['BearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'User updated successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
            'website' => 'nullable|url',
            'location' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $this->mediaService->replace($user, $request->file('avatar'), 'avatar', 'public');
            unset($validatedData['avatar']);
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $this->mediaService->replace($user, $request->file('cover_image'), 'cover_image', 'public');
            unset($validatedData['cover_image']);
        }

        // Update other user data
        if (!empty($validatedData)) {
            $this->userService->update($user, $validatedData);
        }
        
        // Reload user to get updated media URLs
        $updatedUser = $this->userService->getById($user->id);

        return response()->json($updatedUser);
    }
}
