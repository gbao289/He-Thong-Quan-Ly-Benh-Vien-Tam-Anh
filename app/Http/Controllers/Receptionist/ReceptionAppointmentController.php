<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceptionAppointmentController extends Controller
{
    /**
     * Display appointment check-in queue for reception desk.
     */
    public function index(Request $request): View
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
            ->whereDate('appointment_date', $date)
            ->latest('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('appointment_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('patientProfile', function ($pq) use ($search) {
                      $pq->where('full_name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%")
                         ->orWhere('patient_code', 'LIKE', "%{$search}%");
                  });
            });
        }

        $appointments = $query->paginate(15)->withQueryString();

        return view('receptionist.appointments.index', compact('appointments', 'date', 'status', 'search'));
    }

    /**
     * Receptionist checks in a patient when they arrive at hospital.
     */
    public function checkin(Appointment $appointment): RedirectResponse
    {
        if ($appointment->status === Appointment::STATUS_CANCELLED) {
            return back()->with('error', 'Không thể check-in lịch hẹn đã bị hủy.');
        }

        $appointment->update([
            'status' => Appointment::STATUS_CONFIRMED,
        ]);

        return back()->with('success', "Đã Check-in thành công cho bệnh nhân {$appointment->patientProfile->full_name} (Mã hẹn: {$appointment->appointment_code}). Bệnh nhân đã được chuyển vào danh sách chờ khám của bác sĩ.");
    }

    /**
     * Receptionist cancels an appointment with reason.
     */
    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ], [
            'cancellation_reason.required' => 'Vui lòng cung cấp lý do hủy ca khám.',
        ]);

        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'cancellation_reason' => 'Lễ tân hủy: ' . $request->input('cancellation_reason'),
        ]);

        return back()->with('success', "Đã hủy lịch hẹn {$appointment->appointment_code}.");
    }
}
