<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds
        
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'memory_usage' => memory_get_usage(true),
            'user_id' => auth()->id(),
        ];
        
        // Log based on status code
        if ($response->getStatusCode() >= 500) {
            Log::error('HTTP Request Error', $logData);
        } elseif ($response->getStatusCode() >= 400) {
            Log::warning('HTTP Request Client Error', $logData);
        } else {
            Log::info('HTTP Request', $logData);
        }
        
        return $response;
    }
}
