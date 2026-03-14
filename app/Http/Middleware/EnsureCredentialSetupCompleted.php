<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCredentialSetupCompleted
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

        if ($user->credential_setup_completed) {
            return $next($request);
        }

        if ($request->routeIs('credential.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('credential.show');
    }
}
