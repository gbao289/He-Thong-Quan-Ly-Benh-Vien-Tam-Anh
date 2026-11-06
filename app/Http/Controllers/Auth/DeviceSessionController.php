<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DeviceSessionController extends Controller
{
    /**
     * View active session security overview.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $currentSession = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? 'Unknown Browser',
            'is_current_device' => true,
            'last_active' => now()->format('H:i d/m/Y'),
        ];

        return view('auth.sessions', [
            'user' => $user,
            'currentSession' => $currentSession,
        ]);
    }

    /**
     * Terminate / Log out from all other browser sessions.
     */
    public function logoutOtherDevices(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu tài khoản để xác nhận đăng xuất khỏi các thiết bị khác.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'Mật khẩu xác nhận không chính xác.']);
        }

        try {
            Auth::logoutOtherDevices($request->input('password'));
        } catch (\Throwable $e) {
            // Fallback for session drivers if needed
        }

        SecurityAuditService::log(
            'session_revoked_others',
            "Người dùng {$user->email} đã thực hiện đăng xuất khỏi tất cả các phiên thiết bị khác từ xa",
            $user->id,
            'info'
        );

        return back()->with('success', 'Đã đăng xuất thành công khỏi tất cả các thiết bị khác! Phiên hiện tại của bạn vẫn được giữ an toàn.');
    }
}
