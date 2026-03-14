<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->onboarding_completed) {
            return $next($request);
        }

        if ($request->routeIs('onboarding.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('onboarding.show');
    }
}
