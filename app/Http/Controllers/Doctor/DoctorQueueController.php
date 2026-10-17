<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorQueueController extends Controller
{
    /**
     * Display the consultation queue for logged-in doctor.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $doctor = Doctor::with('specialty')->where('user_id', $user->id)->first();

        $appointments = collect();

        if ($doctor) {
            $today = Carbon::today();
            $appointments = Appointment::with(['patientProfile', 'specialty'])
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $today)
                ->orderByRaw("FIELD(status, 'confirmed', 'pending', 'completed', 'cancelled')")
                ->orderBy('created_at')
                ->get();
        }

        return view('doctor.queue', compact('doctor', 'appointments'));
    }
}
