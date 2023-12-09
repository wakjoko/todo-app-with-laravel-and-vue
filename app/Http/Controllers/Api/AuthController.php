<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register the user.
     */
    public function register(RegisterRequest $request): Response
    {
        $validated = $request->validated();

        $validated['name'] = explode('@', $validated['email'])[0];

        $user = User::create($validated);
        
        $token = $user->newAccessToken();

        return new JsonResponse($token);
    }

    /**
     * Log the user in (Create a new personal access token).
     */
    public function login(LoginRequest $request): Response
    {
        $request->authenticateOrFail();

        $user = $request->user();
        
        $token = $user->newAccessToken();

        return new JsonResponse($token);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse(status: 204);
    }
}
