<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Attachment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\PatientProfile;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Central Dashboard with real database stats and Chart.js analytics.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalCompleted = Appointment::where('status', Appointment::STATUS_COMPLETED)->count();
        $totalCancelled = Appointment::where('status', Appointment::STATUS_CANCELLED)->count();
        $totalDoneOrCancelled = $totalCompleted + $totalCancelled;
        $completionRate = $totalDoneOrCancelled > 0 ? round(($totalCompleted / $totalDoneOrCancelled) * 100, 1) : 100.0;

        $stats = [
            'total_users' => User::count(),
            'total_doctors' => Doctor::count(),
            'total_specialties' => Specialty::count(),
            'total_patients' => PatientProfile::count(),
            'appointments_today' => Appointment::whereDate('appointment_date', $today)->count(),
            'appointments_pending' => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
            'appointments_confirmed' => Appointment::where('status', Appointment::STATUS_CONFIRMED)->count(),
            'appointments_completed' => $totalCompleted,
            'appointments_cancelled' => $totalCancelled,
            'appointments_month' => Appointment::where('appointment_date', '>=', $startOfMonth)->count(),
            'total_records' => MedicalRecord::count(),
            'total_prescriptions' => Prescription::count(),
            'total_attachments' => Attachment::count(),
            'completion_rate' => $completionRate,
        ];

        // Biểu đồ 1: Phân bổ trạng thái tất cả ca hẹn
        $statusChartData = [
            'pending' => $stats['appointments_pending'],
            'confirmed' => $stats['appointments_confirmed'],
            'completed' => $stats['appointments_completed'],
            'cancelled' => $stats['appointments_cancelled'],
        ];

        // Biểu đồ 2: Xu hướng lượt khám 7 ngày gần đây
        $trend7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $trend7Days->push([
                'label' => $day->format('d/m'),
                'count' => Appointment::whereDate('appointment_date', $day)->count(),
            ]);
        }

        // Biểu đồ 3: Phân bổ ca khám theo chuyên khoa
        $specialtyStats = Specialty::withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->take(6)
            ->get();

        $recentAppointments = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
            ->latest()
            ->take(7)
            ->get();

        $recentRecords = MedicalRecord::with(['patientProfile', 'doctor.user', 'appointment'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'statusChartData',
            'trend7Days',
            'specialtyStats',
            'recentAppointments',
            'recentRecords'
        ));
    }
}
