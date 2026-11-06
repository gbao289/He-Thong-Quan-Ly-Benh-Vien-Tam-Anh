<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemHealthCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SystemHealthController extends Controller
{
    public function __construct(protected SystemHealthCheckService $healthService)
    {
    }

    /**
     * Display comprehensive system infrastructure health status.
     */
    public function index(): View
    {
        $report = $this->healthService->runFullDiagnostic();

        return view('admin.system_health.index', [
            'report' => $report,
        ]);
    }

    /**
     * Send test mail to verify mailer transport.
     */
    public function pingMail(Request $request): RedirectResponse
    {
        $targetEmail = $request->input('email', $request->user()->email);

        try {
            Mail::raw("Đây là email kiểm tra kết nối từ Hệ thống Quản lý Bệnh Viện Tâm Anh lúc " . now()->format('H:i:s d/m/Y'), function ($msg) use ($targetEmail) {
                $msg->to($targetEmail)
                    ->subject("Kiểm tra kết nối Máy chủ Email - BV Tâm Anh");
            });

            return back()->with('success', "Đã gửi email kiểm tra thành công tới địa chỉ: {$targetEmail}!");
        } catch (\Throwable $e) {
            return back()->with('error', "Gửi email kiểm tra thất bại: " . $e->getMessage());
        }
    }
}
