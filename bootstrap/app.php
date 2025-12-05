<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\CheckResidentRole;
use App\Http\Middleware\CheckAdminSecretaryAccess;
use App\Http\Middleware\RateLimitingMiddleware;
use App\Http\Middleware\LoginRateLimitMiddleware;
use App\Http\Middleware\InputSanitizationMiddleware;
use App\Http\Middleware\RequireTwoFactorAuth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.role' => CheckAdminRole::class,
            'resident.role' => CheckResidentRole::class,
            'admin.secretary' => CheckAdminSecretaryAccess::class,
            'rate.limit' => RateLimitingMiddleware::class,
            'login.rate.limit' => LoginRateLimitMiddleware::class,
            'input.sanitize' => InputSanitizationMiddleware::class,
            '2fa' => RequireTwoFactorAuth::class,
        ]);

        // Add security headers to all responses (temporarily disabled)
        // $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
