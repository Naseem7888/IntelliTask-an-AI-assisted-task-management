<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AIRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $key = 'ai-requests:' . ($user ? $user->id : 'guest:' . $request->ip());
        $maxAttempts = (int) config('ai.rate_limit', 10);
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'Too many requests. Please try again in ' . $seconds . ' seconds.'
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
        ]);

        return $response;
    }
}
