<?php

namespace App\Http\Middleware;

use App\Exceptions\GenericException;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            !$request->user() ||
            ($request->user() instanceof MustVerifyEmail && !$request->user()->hasVerifiedEmail())
        ) {
            /** @var \App\Models\User $user */
            $user = $request->user();

            throw new GenericException(__('Account of :name isn’t verified.', ['name' => "{$user->name} {$user->lastname}"]));
        }

        return $next($request);
    }
}
