<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\PatientProfile;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Central Dashboard with real database stats.
     */
    public function index(): View
    {
        $today = Carbon::today();

        $stats = [
            'total_users' => User::count(),
            'total_doctors' => Doctor::count(),
            'total_specialties' => Specialty::count(),
            'total_patients' => PatientProfile::count(),
            'appointments_today' => Appointment::whereDate('appointment_date', $today)->count(),
            'appointments_pending' => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
            'appointments_confirmed' => Appointment::where('status', Appointment::STATUS_CONFIRMED)->count(),
            'appointments_completed' => Appointment::where('status', Appointment::STATUS_COMPLETED)->count(),
        ];

        $recentAppointments = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAppointments'));
    }
}
