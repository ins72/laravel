<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class ApiRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->getMaxAttempts($request);
        $decayMinutes = $this->getDecayMinutes($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return sha1($user->getAuthIdentifier());
        }

        return sha1($request->ip());
    }

    /**
     * Get the maximum number of attempts for the given request.
     */
    protected function getMaxAttempts(Request $request): int
    {
        // Different limits for different endpoints
        $path = $request->path();
        
        if (str_contains($path, 'auth/login') || str_contains($path, 'auth/register')) {
            return 5; // 5 attempts per minute for auth endpoints
        }
        
        if (str_contains($path, 'admin')) {
            return 200; // 200 attempts per minute for admin endpoints
        }
        
        return 100; // 100 attempts per minute for regular endpoints
    }

    /**
     * Get the number of minutes to decay the rate limit.
     */
    protected function getDecayMinutes(Request $request): int
    {
        return 1; // 1 minute decay
    }

    /**
     * Calculate the number of remaining attempts.
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return $maxAttempts - RateLimiter::attempts($key);
    }

    /**
     * Build the response for when a request exceeds the rate limit.
     */
    protected function buildResponse(string $key, int $maxAttempts): JsonResponse
    {
        $retryAfter = RateLimiter::availableIn($key);

        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'RATE_LIMIT_EXCEEDED',
                'message' => 'Too many requests. Please try again later.',
                'details' => [
                    'retry_after' => $retryAfter,
                    'max_attempts' => $maxAttempts,
                ]
            ],
            'status_code' => 429,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
        ]);
    }

    /**
     * Add the limit header information to the given response.
     */
    protected function addHeaders($response, int $maxAttempts, int $remainingAttempts): mixed
    {
        if (method_exists($response, 'header')) {
            $response->header('X-RateLimit-Limit', $maxAttempts);
            $response->header('X-RateLimit-Remaining', $remainingAttempts);
        }

        return $response;
    }
} 