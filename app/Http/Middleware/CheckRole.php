<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();

        // Support both variadic arguments (...$roles) and comma-separated string ('admin,receptionist')
        $allowedRoles = [];
        foreach ($roles as $roleItem) {
            foreach (explode(',', $roleItem) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (empty($allowedRoles) || in_array($user->role, $allowedRoles, true)) {
            return $next($request);
        }

        // Audit Trail: Record security anomaly on unauthorized role boundary traversal
        try {
            \App\Services\SecurityAuditService::log(
                'unauthorized_access_attempt',
                "Người dùng {$user->email} ({$user->role}) bị chặn khi truy cập URL yêu cầu quyền [" . implode(',', $allowedRoles) . "]: {$request->path()}",
                $user->id,
                'warning'
            );
        } catch (\Throwable $e) {
            // Fail open for logging, do not break abort flow
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập vào chức năng này.',
            ], 403);
        }

        abort(403, 'Bạn không có quyền truy cập vào khu vực này.');
    }
}
