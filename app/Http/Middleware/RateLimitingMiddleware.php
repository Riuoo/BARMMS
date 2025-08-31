<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitingMiddleware
{
    /**
     * The rate limiter instance.
     */
    protected $limiter;

    /**
     * Create a new middleware instance.
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $this->logRateLimitExceeded($request);
            
            return $this->buildRateLimitResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addRateLimitHeaders(
            $response, $maxAttempts,
            $this->limiter->remaining($key, $maxAttempts)
        );
    }

    /**
     * Resolve request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $identifier = $request->ip();
        
        // Add user ID to signature if authenticated
        if ($request->session()->has('user_id')) {
            $identifier .= ':' . $request->session()->get('user_id');
        }
        
        // Add route name to signature for more granular control
        $routeName = $request->route() ? $request->route()->getName() : 'unknown';
        $identifier .= ':' . $routeName;
        
        return sha1($identifier);
    }

    /**
     * Create a 'too many attempts' response.
     */
    protected function buildRateLimitResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->limiter->availableIn($key);
        
        $message = 'Too many requests. Please try again in ' . 
                   ceil($retryAfter / 60) . ' minute(s).';
        
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => $message,
                'retry_after' => $retryAfter
            ], 429);
        }
        
        notify()->error($message);
        return back()->withInput();
    }

    /**
     * Add the limit header information to the given response.
     */
    protected function addRateLimitHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $remainingAttempts);
        
        return $response;
    }

    /**
     * Log rate limit exceeded attempts for security monitoring.
     */
    protected function logRateLimitExceeded(Request $request): void
    {
        Log::warning('Rate limit exceeded', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => $request->session()->get('user_id'),
            'route' => $request->route() ? $request->route()->getName() : 'unknown',
            'timestamp' => now()
        ]);
    }
}
