<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Auth\UpdatePasswordServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class UpdatePasswordController extends Controller
{
    protected UpdatePasswordServiceInterface $updatePasswordService;

    public function __construct(UpdatePasswordServiceInterface $updatePasswordService)
    {
        $this->updatePasswordService = $updatePasswordService;
    }

    #[OA\Post(
        path: '/api/v1/update-password',
        summary: 'Update user password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['current_password', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'current_password', type: 'string', format: 'password', example: 'password'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'new_password'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'new_password'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Password updated successfully.'),
            new OA\Response(response: 422, description: 'Validation error or current password does not match.'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $success = $this->updatePasswordService->update($request->user(), $validatedData);

        if (!$success) {
            return response()->json(['message' => 'Current password does not match'], 422);
        }

        return response()->json(['message' => 'Password updated successfully.']);
    }
}