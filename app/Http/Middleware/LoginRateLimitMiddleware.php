<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimitMiddleware
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
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveLoginSignature($request);
        
        // Stricter limits for login attempts: 5 attempts per 15 minutes
        $maxAttempts = 5;
        $decayMinutes = 15;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $this->logLoginRateLimitExceeded($request);
            
            return $this->buildLoginRateLimitResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addRateLimitHeaders(
            $response, $maxAttempts,
            $this->limiter->remaining($key, $maxAttempts)
        );
    }

    /**
     * Resolve request signature for login rate limiting.
     */
    protected function resolveLoginSignature(Request $request): string
    {
        $identifier = $request->ip();
        
        // Add email to signature for more granular control
        if ($request->has('email')) {
            $identifier .= ':' . $request->input('email');
        }
        
        return sha1($identifier);
    }

    /**
     * Create a 'too many login attempts' response.
     */
    protected function buildLoginRateLimitResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->limiter->availableIn($key);
        
        $message = 'Too many login attempts. Please try again in ' . 
                   ceil($retryAfter / 60) . ' minute(s). For security reasons, your account may be temporarily locked.';
        
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Login rate limit exceeded',
                'message' => $message,
                'retry_after' => $retryAfter
            ], 429);
        }
        
        notify()->error($message);
        return back()->withInput()->withErrors(['email' => $message]);
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
     * Log login rate limit exceeded attempts for security monitoring.
     */
    protected function logLoginRateLimitExceeded(Request $request): void
    {
        Log::warning('Login rate limit exceeded', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'email' => $request->input('email'),
            'url' => $request->fullUrl(),
            'timestamp' => now()
        ]);
    }
}
