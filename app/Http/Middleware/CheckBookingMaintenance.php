<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBookingMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If maintenance is enabled and current user is not admin
        if (SystemSetting::isTrue('booking_maintenance_mode')) {
            $user = $request->user();
            if (!$user || $user->role !== 'admin') {
                $maintenanceNotice = SystemSetting::get('booking_maintenance_message', 'Hệ thống đặt lịch trực tuyến đang được nâng cấp bảo trì định kỳ.');
                return response()->view('public.booking_maintenance', [
                    'message' => $maintenanceNotice,
                ], 503);
            }
        }

        return $next($request);
    }
}
