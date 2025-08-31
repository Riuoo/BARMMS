<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InputSanitizationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize all input data
        $this->sanitizeInput($request);
        
        // Log suspicious input patterns
        $this->logSuspiciousInput($request);
        
        $response = $next($request);
        
        return $response;
    }

    /**
     * Sanitize all input data to prevent XSS and injection attacks.
     */
    protected function sanitizeInput(Request $request): void
    {
        $inputs = $request->all();
        
        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                // Remove potentially dangerous HTML tags
                $value = $this->removeDangerousTags($value);
                
                // Escape HTML entities
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                
                // Remove null bytes
                $value = str_replace("\0", '', $value);
                
                // Update the request with sanitized value
                $request->merge([$key => $value]);
            } elseif (is_array($value)) {
                // Recursively sanitize array values
                $this->sanitizeArray($request, $key, $value);
            }
        }
    }

    /**
     * Recursively sanitize array values.
     */
    protected function sanitizeArray(Request $request, string $key, array $array): void
    {
        foreach ($array as $subKey => $subValue) {
            if (is_string($subValue)) {
                $subValue = $this->removeDangerousTags($subValue);
                $subValue = htmlspecialchars($subValue, ENT_QUOTES, 'UTF-8');
                $subValue = str_replace("\0", '', $subValue);
                $array[$subKey] = $subValue;
            } elseif (is_array($subValue)) {
                $this->sanitizeArray($request, $key . '.' . $subKey, $subValue);
            }
        }
        
        $request->merge([$key => $array]);
    }

    /**
     * Remove potentially dangerous HTML tags and attributes.
     */
    protected function removeDangerousTags(string $value): string
    {
        // Remove script tags and their content
        $value = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $value);
        
        // Remove other potentially dangerous tags
        $dangerousTags = [
            'javascript:', 'vbscript:', 'onload', 'onerror', 'onclick', 'onmouseover',
            'onfocus', 'onblur', 'onchange', 'onsubmit', 'onreset', 'onselect',
            'onunload', 'onabort', 'onbeforeunload', 'onerror', 'onhashchange',
            'onmessage', 'onoffline', 'ononline', 'onpagehide', 'onpageshow',
            'onpopstate', 'onstorage', 'oncontextmenu', 'oninput', 'oninvalid'
        ];
        
        foreach ($dangerousTags as $tag) {
            $value = str_ireplace($tag, '', $value);
        }
        
        // Remove iframe tags
        $value = preg_replace('/<iframe\b[^>]*>.*?<\/iframe>/mi', '', $value);
        
        // Remove object and embed tags
        $value = preg_replace('/<(object|embed)\b[^>]*>.*?<\/(object|embed)>/mi', '', $value);
        
        return $value;
    }

    /**
     * Log suspicious input patterns for security monitoring.
     */
    protected function logSuspiciousInput(Request $request): void
    {
        $inputs = $request->all();
        $suspiciousPatterns = [];
        
        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                // Check for SQL injection patterns
                if (preg_match('/(union|select|insert|update|delete|drop|create|alter|exec|execute|script|javascript|vbscript)/i', $value)) {
                    $suspiciousPatterns[] = [
                        'field' => $key,
                        'pattern' => 'SQL/Code injection attempt',
                        'value' => substr($value, 0, 100) // Log first 100 chars only
                    ];
                }
                
                // Check for XSS patterns
                if (preg_match('/(<script|javascript:|vbscript:|on\w+\s*=|<iframe|<object)/i', $value)) {
                    $suspiciousPatterns[] = [
                        'field' => $key,
                        'pattern' => 'XSS attempt',
                        'value' => substr($value, 0, 100)
                    ];
                }
                
                // Check for command injection patterns
                if (preg_match('/(\||&|;|`|\$\(|\$\{)/', $value)) {
                    $suspiciousPatterns[] = [
                        'field' => $key,
                        'pattern' => 'Command injection attempt',
                        'value' => substr($value, 0, 100)
                    ];
                }
            }
        }
        
        if (!empty($suspiciousPatterns)) {
            Log::warning('Suspicious input detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => $request->session()->get('user_id'),
                'suspicious_patterns' => $suspiciousPatterns,
                'timestamp' => now()
            ]);
        }
    }
}
