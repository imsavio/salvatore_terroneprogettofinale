<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    /**
     * Basic health check endpoint
     */
    public function index()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0')
        ]);
    }

    /**
     * Detailed health check with system status
     */
    public function detailed()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'mail' => $this->checkMail(),
        ];

        $overallStatus = collect($checks)->every(fn($check) => $check['status'] === 'ok') ? 'ok' : 'error';

        return response()->json([
            'status' => $overallStatus,
            'timestamp' => now()->toISOString(),
            'checks' => $checks
        ], $overallStatus === 'ok' ? 200 : 503);
    }

    /**
     * Check database connection
     */
    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'ok',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check cache system
     */
    private function checkCache()
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, 'test', 60);
            $value = Cache::get($key);
            Cache::forget($key);
            
            if ($value === 'test') {
                return [
                    'status' => 'ok',
                    'message' => 'Cache system working'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Cache system not working properly'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache system error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check storage system
     */
    private function checkStorage()
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::disk('public')->put($testFile, 'test');
            $content = Storage::disk('public')->get($testFile);
            Storage::disk('public')->delete($testFile);
            
            if ($content === 'test') {
                return [
                    'status' => 'ok',
                    'message' => 'Storage system working'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Storage system not working properly'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage system error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check mail configuration
     */
    private function checkMail()
    {
        try {
            $mailConfig = config('mail.default');
            if (empty($mailConfig)) {
                return [
                    'status' => 'error',
                    'message' => 'Mail configuration not set'
                ];
            }

            return [
                'status' => 'ok',
                'message' => 'Mail configuration is set to: ' . $mailConfig
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Mail configuration error: ' . $e->getMessage()
            ];
        }
    }
}
