<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\BarangayProfile;
use App\Services\TwoFactorAuthService;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactorAuth
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $operation
     */
    public function handle(Request $request, Closure $next, ?string $operation = null): Response
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = BarangayProfile::find($userId);
        
        if (!$user || !$user->hasTwoFactorEnabled()) {
            // 2FA not enabled, allow access
            return $next($request);
        }

        // Check if device is trusted
        $deviceId = Session::get('2fa_device_id') ?? $this->twoFactorService->generateDeviceId($request);
        
        if ($this->twoFactorService->isDeviceTrusted($userId, $deviceId)) {
            // Device is trusted, allow access
            return $next($request);
        }

        // Check if operation was recently verified
        if ($operation && $this->twoFactorService->isRequiredForOperation($userId, $operation)) {
            // Check if already verified for this operation
            $cacheKey = "2fa_verified_{$userId}_{$operation}";
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                // For DELETE requests, we need to store the request data and redirect to a special handler
                if ($request->isMethod('DELETE')) {
                    Session::put('pending_delete_operation', [
                        'route' => $request->route()->getName(),
                        'route_params' => $request->route()->parameters(),
                        'operation' => $operation,
                    ]);
                    return redirect()->route('2fa.verify-operation', [
                        'operation' => $operation,
                        'redirect' => route('admin.residents'),
                    ]);
                }
                
                // Redirect to 2FA verification
                return redirect()->route('2fa.verify-operation', [
                    'operation' => $operation,
                    'redirect' => $request->fullUrl(),
                ]);
            }
        }

        // For login flow, check if 2FA is pending
        if (Session::has('2fa_pending_user_id')) {
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
