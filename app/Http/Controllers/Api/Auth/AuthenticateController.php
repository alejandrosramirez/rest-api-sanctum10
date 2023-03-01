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

        /** @var \App\Models\User $user */
        $user = Auth::guard('web')->user();

        return response()->json([
            'token' => $user->createToken('auth')->plainTextToken,
            'user' => $user,
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
        Auth::guard('web')->logout();

        /** @var \App\Models\Administrator $user */
        $user = Auth::guard('web')->user();

        return response()->json(['logout' => $user == null]);
    }
}
