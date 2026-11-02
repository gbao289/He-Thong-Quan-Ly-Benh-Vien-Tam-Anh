<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAuditLog extends Model
{
    use HasFactory;

    public const EVENT_LOGIN_SUCCESS = 'login_success';
    public const EVENT_LOGIN_FAILED = 'login_failed';
    public const EVENT_LOGOUT = 'logout';
    public const EVENT_PASSWORD_CHANGED = 'password_changed';
    public const EVENT_2FA_ENABLED = '2fa_enabled';
    public const EVENT_2FA_DISABLED = '2fa_disabled';
    public const EVENT_2FA_FAILED = '2fa_failed';
    public const EVENT_ACCESS_DENIED = 'access_denied'; // 403 Forbidden
    public const EVENT_BRUTE_FORCE_ALERT = 'brute_force_alert';
    public const EVENT_SESSION_REVOKED = 'session_revoked';
    public const EVENT_MAINTENANCE_TOGGLED = 'maintenance_toggled';

    public const SEVERITY_INFO = 'info';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_DANGER = 'danger';

    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'ip_address',
        'user_agent',
        'description',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSeverityBadgeClassAttribute(): string
    {
        return match ($this->severity) {
            self::SEVERITY_DANGER => 'bg-danger text-white',
            self::SEVERITY_WARNING => 'bg-warning text-dark',
            default => 'bg-info text-dark',
        };
    }
}
