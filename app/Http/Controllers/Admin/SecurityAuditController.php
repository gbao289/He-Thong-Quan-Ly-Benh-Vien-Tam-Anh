<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecurityAuditController extends Controller
{
    /**
     * Display paginated audit trail logs with advanced filtering.
     */
    public function index(Request $request): View
    {
        $query = SecurityAuditLog::with('user')->latest();

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->input('event_type'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => SecurityAuditLog::count(),
            'warnings' => SecurityAuditLog::where('severity', 'warning')->count(),
            'critical' => SecurityAuditLog::where('severity', 'critical')->count(),
            'today' => SecurityAuditLog::whereDate('created_at', today())->count(),
        ];

        $eventTypes = SecurityAuditLog::distinct()->pluck('event_type')->filter()->values();

        return view('admin.security_audit.index', [
            'logs' => $logs,
            'stats' => $stats,
            'eventTypes' => $eventTypes,
            'filters' => $request->only('event_type', 'severity', 'search'),
        ]);
    }

    /**
     * Export security audit logs as downloadable CSV stream.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = SecurityAuditLog::with('user')->latest();

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->input('event_type'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        $filename = 'security_audit_logs_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel Vietnamese characters display
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID',
                'Thời gian',
                'Loại sự kiện',
                'Mức độ',
                'Tài khoản',
                'IP Address',
                'User Agent',
                'Chi tiết sự kiện',
            ]);

            $query->chunk(200, function ($records) use ($handle) {
                foreach ($records as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->event_type,
                        strtoupper($log->severity),
                        $log->user ? "{$log->user->name} ({$log->user->email})" : 'Khách / Hệ thống',
                        $log->ip_address,
                        $log->user_agent,
                        $log->description,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
