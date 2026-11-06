<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SystemHealthCheckService
{
    /**
     * Run all health checks and return structured diagnostic status.
     *
     * @return array<string, mixed>
     */
    public function runFullDiagnostic(): array
    {
        $db = $this->checkDatabase();
        $storage = $this->checkStorage();
        $cache = $this->checkCache();
        $mail = $this->checkMail();
        $runtime = $this->checkRuntime();

        $allHealthy = ($db['status'] === 'healthy') &&
            ($storage['status'] === 'healthy') &&
            ($cache['status'] === 'healthy') &&
            ($mail['status'] === 'healthy');

        return [
            'status' => $allHealthy ? 'healthy' : 'degraded',
            'checked_at' => now()->toIso8601String(),
            'database' => $db,
            'storage' => $storage,
            'cache' => $cache,
            'mail' => $mail,
            'runtime' => $runtime,
        ];
    }

    /**
     * Database connectivity and query latency check.
     */
    public function checkDatabase(): array
    {
        $start = microtime(true);
        try {
            DB::select('SELECT 1');
            $latencyMs = round((microtime(true) - $start) * 1000, 2);
            $conn = DB::connection();

            return [
                'status' => 'healthy',
                'message' => 'Kết nối cơ sở dữ liệu hoạt động bình thường',
                'latency_ms' => $latencyMs,
                'driver' => $conn->getDriverName(),
                'database' => $conn->getDatabaseName(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage(),
                'latency_ms' => null,
                'driver' => config('database.default'),
                'database' => null,
            ];
        }
    }

    /**
     * Storage read/write permissions & disk space.
     */
    public function checkStorage(): array
    {
        $appPath = storage_path('app');
        $isWritable = is_writable($appPath);
        
        $freeBytes = @disk_free_space($appPath);
        $totalBytes = @disk_total_space($appPath);

        $freeGb = $freeBytes ? round($freeBytes / (1024 * 1024 * 1024), 2) : null;
        $totalGb = $totalBytes ? round($totalBytes / (1024 * 1024 * 1024), 2) : null;
        $usedPercentage = ($freeBytes && $totalBytes && $totalBytes > 0)
            ? round((($totalBytes - $freeBytes) / $totalBytes) * 100, 1)
            : null;

        return [
            'status' => $isWritable ? 'healthy' : 'unhealthy',
            'storage_path_writable' => $isWritable,
            'free_disk_gb' => $freeGb,
            'total_disk_gb' => $totalGb,
            'disk_usage_percent' => $usedPercentage,
        ];
    }

    /**
     * Cache read/write/delete test.
     */
    public function checkCache(): array
    {
        $testKey = 'health_check_probe_' . uniqid();
        $testValue = microtime(true);

        try {
            Cache::put($testKey, $testValue, 10);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);

            $ok = ($retrieved == $testValue);

            return [
                'status' => $ok ? 'healthy' : 'unhealthy',
                'driver' => config('cache.default'),
                'read_write_ok' => $ok,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'driver' => config('cache.default'),
                'read_write_ok' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mail transport configuration check.
     */
    public function checkMail(): array
    {
        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');

        return [
            'status' => !empty($mailer) ? 'healthy' : 'degraded',
            'default_mailer' => $mailer,
            'host' => $host,
            'port' => $port,
            'from_address' => config('mail.from.address'),
        ];
    }

    /**
     * Runtime environment and resources.
     */
    public function checkRuntime(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit');

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS_FAMILY,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI / Standalone',
            'memory_usage_mb' => round($memoryUsage / (1024 * 1024), 2),
            'memory_peak_mb' => round($memoryPeak / (1024 * 1024), 2),
            'memory_limit' => $memoryLimit,
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'server_time' => now()->format('Y-m-d H:i:s T'),
        ];
    }
}
