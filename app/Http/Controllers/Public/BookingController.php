<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\AppointmentCreatedMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Show booking form.
     */
    public function create(Request $request): View
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        $selectedSpecialtyId = $request->query('specialty_id');
        $selectedDoctorId = $request->query('doctor_id');

        // Pre-fill patient information if patient is logged in
        $patientProfile = null;
        if (Auth::check() && Auth::user()->isPatient()) {
            $patientProfile = PatientProfile::where('user_id', Auth::id())->first();
        }

        $doctors = collect();
        if ($selectedSpecialtyId) {
            $doctors = Doctor::with('user')
                ->where('specialty_id', $selectedSpecialtyId)
                ->where('is_available', true)
                ->get();
        }

        return view('public.booking', compact(
            'specialties',
            'doctors',
            'selectedSpecialtyId',
            'selectedDoctorId',
            'patientProfile'
        ));
    }

    /**
     * Handle appointment booking submission.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $appointment = DB::transaction(function () use ($validated) {
                // 1. Resolve or create Patient Profile
                $patientProfile = null;

                if (Auth::check()) {
                    $patientProfile = PatientProfile::where('user_id', Auth::id())->first();
                }

                if (!$patientProfile) {
                    $patientProfile = PatientProfile::where('phone', $validated['phone'])->first();
                }

                if (!$patientProfile) {
                    $patientProfile = PatientProfile::create([
                        'user_id' => Auth::check() ? Auth::id() : null,
                        'patient_code' => PatientProfile::generatePatientCode(),
                        'full_name' => $validated['full_name'],
                        'dob' => $validated['dob'],
                        'gender' => $validated['gender'],
                        'phone' => $validated['phone'],
                        'email' => $validated['email'] ?? null,
                        'address' => $validated['address'] ?? null,
                        'identity_card' => $validated['identity_card'] ?? null,
                    ]);
                } else {
                    // Update latest contact info if missing
                    $updateData = [];
                    if (empty($patientProfile->user_id) && Auth::check()) {
                        $updateData['user_id'] = Auth::id();
                    }
                    if (!empty($validated['email']) && empty($patientProfile->email)) {
                        $updateData['email'] = $validated['email'];
                    }
                    if (!empty($validated['address']) && empty($patientProfile->address)) {
                        $updateData['address'] = $validated['address'];
                    }
                    if (!empty($updateData)) {
                        $patientProfile->update($updateData);
                    }
                }

                // 2. Create Appointment record
                $appointmentCode = Appointment::generateAppointmentCode();

                return Appointment::create([
                    'appointment_code' => $appointmentCode,
                    'patient_profile_id' => $patientProfile->id,
                    'doctor_id' => $validated['doctor_id'],
                    'specialty_id' => $validated['specialty_id'],
                    'appointment_date' => $validated['appointment_date'],
                    'shift' => $validated['shift'],
                    'appointment_time' => $validated['appointment_time'] ?? null,
                    'symptom_description' => $validated['symptom_description'],
                    'status' => Appointment::STATUS_PENDING,
                    'created_by_user_id' => Auth::id(),
                ]);
            });

            // 3. Send confirmation email (with exception protection)
            $recipientEmail = $validated['email'] ?? ($appointment->patientProfile->email ?? null);
            if ($recipientEmail) {
                try {
                    $appointment->load(['patientProfile', 'doctor.user', 'specialty']);
                    Mail::to($recipientEmail)->send(new AppointmentCreatedMail($appointment));
                } catch (\Throwable $e) {
                    Log::error("Failed to send booking confirmation email to {$recipientEmail}: " . $e->getMessage());
                }
            }

            return redirect()->route('booking.success', ['code' => $appointment->appointment_code])
                ->with('success', 'Đăng ký đặt lịch khám thành công! Quý khách vui lòng lưu lại mã tra cứu.');

        } catch (\Throwable $e) {
            Log::error("Booking store error: " . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra trong quá trình ghi nhận lịch khám: ' . $e->getMessage());
        }
    }

    /**
     * Show booking success page.
     */
    public function success(string $code): View|RedirectResponse
    {
        $appointment = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
            ->where('appointment_code', $code)
            ->first();

        if (!$appointment) {
            return redirect()->route('booking.create')->with('error', 'Không tìm thấy thông tin lịch hẹn yêu cầu.');
        }

        return view('public.booking-success', compact('appointment'));
    }

    /**
     * Public appointment lookup.
     */
    public function lookup(Request $request): View
    {
        $keyword = $request->query('keyword');
        $appointment = null;
        $appointments = collect();

        if ($keyword) {
            $keyword = trim($keyword);
            // Search by exact appointment code or phone number
            if (str_starts_with(strtoupper($keyword), 'AP-')) {
                $appointment = Appointment::with(['patientProfile', 'doctor.user', 'specialty', 'medicalRecord.prescriptions'])
                    ->where('appointment_code', strtoupper($keyword))
                    ->first();
            } else {
                $appointments = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
                    ->whereHas('patientProfile', function ($q) use ($keyword) {
                        $q->where('phone', $keyword)
                          ->orWhere('patient_code', strtoupper($keyword));
                    })
                    ->latest()
                    ->get();
            }
        }

        return view('public.booking-lookup', compact('keyword', 'appointment', 'appointments'));
    }

    /**
     * AJAX API: Get doctors by specialty.
     */
    public function getDoctorsBySpecialty(int $specialtyId): JsonResponse
    {
        $doctors = Doctor::with(['user', 'schedules' => function ($q) {
            $q->where('is_active', true);
        }])
            ->where('specialty_id', $specialtyId)
            ->where('is_available', true)
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->full_name_with_title,
                    'qualification' => $doc->qualification,
                    'room_number' => $doc->room_number,
                    'consultation_fee' => number_format($doc->consultation_fee, 0, ',', '.') . ' VNĐ',
                    'available_days' => $doc->schedules->pluck('day_name')->unique()->values(),
                ];
            });

        return response()->json($doctors);
    }

    /**
     * AJAX API: Check slot availability for doctor on date.
     */
    public function getDoctorSlots(Request $request): JsonResponse
    {
        $doctorId = $request->query('doctor_id');
        $dateStr = $request->query('date');

        if (!$doctorId || !$dateStr) {
            return response()->json(['error' => 'Thiếu thông tin bác sĩ hoặc ngày khám'], 422);
        }

        $date = Carbon::parse($dateStr);
        $dayOfWeek = strtolower($date->englishDayOfWeek);

        $schedules = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        $slots = [];
        foreach ($schedules as $sched) {
            $bookedCount = Appointment::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', $date->toDateString())
                ->where('shift', $sched->shift)
                ->where('status', '!=', Appointment::STATUS_CANCELLED)
                ->count();

            $slots[] = [
                'shift' => $sched->shift,
                'shift_name' => $sched->shift_name,
                'start_time' => substr($sched->start_time, 0, 5),
                'end_time' => substr($sched->end_time, 0, 5),
                'max_patients' => $sched->max_patients,
                'booked_count' => $bookedCount,
                'available' => $bookedCount < $sched->max_patients,
                'remaining' => max(0, $sched->max_patients - $bookedCount),
            ];
        }

        return response()->json([
            'date' => $date->format('d/m/Y'),
            'day_name' => $date->locale('vi')->dayName,
            'slots' => $slots,
        ]);
    }
}
