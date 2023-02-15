<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @group Auth Endpoints
 */
class AuthenticateController extends Controller
{
   /**
     * Handle an incoming login request.
     *
     * @unauthenticated
     *
     * @bodyParam email string required Example: alejandrosram@outlook.com
     * @bodyParam password string required Example: Ejemplo123456
     *
     * @param  \App\Http\Requests\Api\Auth\LoginRequest  $request
     */
    public function login(LoginRequest $request): Response|JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\Administrator $administrator */
        $administrator = Auth::guard('web_administrator')->user();

        return response()->json([
            'token' => $administrator->createToken('authAdministrator')->plainTextToken,
            'administrator' => $administrator,
        ]);
    }

    /**
     * Handle and incoming logout request.
     *
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function logout(Request $request): Response|JsonResponse
    {
        Auth::guard('web_administrator')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        /** @var \App\Models\Administrator $administrator */
        $administrator = Auth::guard('web_administrator')->user();

        return response()->json(['logout' => $administrator == null]);
    }
}
