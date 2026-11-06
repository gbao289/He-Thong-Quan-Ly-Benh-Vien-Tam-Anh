<?php

namespace App\Services;

use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class SecurityAuditService
{
    /**
     * Log a security audit event (callable statically and non-statically).
     */
    public static function log(
        string $eventType,
        ?string $description = null,
        mixed $userOrId = null,
        string $severity = 'info',
        ?array $payload = null,
        ?Request $request = null
    ): SecurityAuditLog {
        $req = $request ?? (request() instanceof Request ? request() : null);
        $ip = $req ? $req->ip() : '127.0.0.1';
        $ua = $req ? substr($req->userAgent() ?? '', 0, 500) : 'CLI/System';

        $userId = null;
        if ($userOrId instanceof User) {
            $userId = $userOrId->id;
        } elseif (is_numeric($userOrId)) {
            $userId = (int) $userOrId;
        } elseif ($req && $req->user()) {
            $userId = $req->user()->id;
        }

        // Check for potential brute-force pattern if failed login
        if ($eventType === 'login_failed' && $ip) {
            $failedAttempts = SecurityAuditLog::where('ip_address', $ip)
                ->where('event_type', 'login_failed')
                ->where('created_at', '>=', now()->subMinutes(10))
                ->count();

            if ($failedAttempts >= 5) {
                SecurityAuditLog::create([
                    'user_id' => $userId,
                    'event_type' => 'brute_force_alert',
                    'severity' => 'critical',
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                    'description' => "Cảnh báo an ninh: Phát hiện {$failedAttempts} lần thử đăng nhập thất bại từ IP {$ip} trong 10 phút.",
                    'payload' => ['attempts' => $failedAttempts + 1],
                ]);
            }
        }

        return SecurityAuditLog::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'severity' => in_array($severity, ['info', 'warning', 'critical']) ? $severity : 'info',
            'ip_address' => $ip,
            'user_agent' => $ua,
            'description' => $description,
            'payload' => $payload,
        ]);
    }
}
