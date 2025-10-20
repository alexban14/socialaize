<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class EmailVerificationController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Mark the user's email address as verified.
     *
     * @param Request $request
     * @param string $id
     * @param string $hash
     * @return RedirectResponse
     */
    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $redirectUrl = $this->authService->verifyEmail($id, $hash, $request->query('redirect_url'));

        if ($redirectUrl === null) {
            $fallbackUrl = config('socialaize.spa_url', 'http://localhost:6326') . '/auth?verified=0&error=invalid_link';
            return redirect($fallbackUrl);
        }

        return redirect($redirectUrl);
    }

    /**
     * Resend the email verification notification.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        $this->authService->resendVerificationEmail($request->user());

        return response()->json(['message' => 'Verification link sent!']);
    }
}
