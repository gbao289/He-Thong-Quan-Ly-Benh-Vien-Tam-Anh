<?php

namespace App\Services;

use App\Mail\WaitlistPromotedMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\PatientProfile;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WaitlistEngineService
{
    /**
     * Add a patient to the waitlist directly with profile ID.
     */
    public function registerWaitlist(
        int $patientProfileId,
        int $doctorId,
        int $specialtyId,
        string $appointmentDate,
        string $shift,
        ?string $symptomDescription = null
    ): Appointment {
        $nextPos = (Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $appointmentDate)
            ->where('shift', $shift)
            ->where('status', 'waitlist')
            ->max('waitlist_position') ?? 0) + 1;

        return Appointment::create([
            'appointment_code' => Appointment::generateAppointmentCode(),
            'patient_profile_id' => $patientProfileId,
            'doctor_id' => $doctorId,
            'specialty_id' => $specialtyId,
            'appointment_date' => $appointmentDate,
            'shift' => $shift,
            'symptom_description' => $symptomDescription ?? 'Đăng ký hàng đợi chờ tự động',
            'status' => 'waitlist',
            'waitlist_position' => $nextPos,
            'waitlisted_at' => now(),
            'created_by_user_id' => auth()->id() ?? null,
        ]);
    }

    /**
     * Add a patient to the waitlist for a specific doctor, date, and shift.
     */
    public function addToWaitlist(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $doctor = Doctor::findOrFail($data['doctor_id']);
            $date = Carbon::parse($data['appointment_date'])->toDateString();
            $shift = $data['shift'];

            // Find current waitlist count to determine position
            $currentWaitlistCount = Appointment::where('doctor_id', $doctor->id)
                ->where('appointment_date', $date)
                ->where('shift', $shift)
                ->where('status', 'waitlist')
                ->count();

            $nextPosition = $currentWaitlistCount + 1;

            // Find or create patient profile
            $patient = PatientProfile::firstOrCreate(
                ['phone' => $data['phone']],
                [
                    'patient_code' => PatientProfile::generatePatientCode(),
                    'full_name' => $data['full_name'],
                    'dob' => $data['dob'],
                    'gender' => $data['gender'],
                    'email' => $data['email'] ?? null,
                    'address' => $data['address'] ?? null,
                ]
            );

            $appointment = Appointment::create([
                'appointment_code' => 'WL-' . Carbon::now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4)),
                'patient_profile_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'specialty_id' => $doctor->specialty_id,
                'appointment_date' => $date,
                'shift' => $shift,
                'symptom_description' => $data['symptom_description'] ?? 'Đăng ký danh sách chờ khám dự bị',
                'status' => 'waitlist',
                'waitlist_position' => $nextPosition,
                'waitlisted_at' => now(),
                'created_by_user_id' => auth()->id() ?? null,
            ]);

            return $appointment;
        });
    }

    /**
     * Check and promote the next candidate in the waitlist when a slot is freed.
     */
    public function promoteNextCandidate(int $doctorId, string $date, string $shift): ?Appointment
    {
        return DB::transaction(function () use ($doctorId, $date, $shift) {
            // Find earliest candidate on waitlist (FIFO)
            $nextCandidate = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $date)
                ->where('shift', $shift)
                ->where('status', 'waitlist')
                ->orderBy('waitlist_position', 'asc')
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$nextCandidate) {
                return null;
            }

            // Promote candidate to pending official appointment
            $officialCode = Appointment::generateAppointmentCode();
            $nextCandidate->update([
                'appointment_code' => $officialCode,
                'status' => Appointment::STATUS_PENDING,
                'waitlist_position' => null,
                'promoted_at' => now(),
            ]);

            // Re-order remaining waitlist positions
            $remaining = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $date)
                ->where('shift', $shift)
                ->where('status', 'waitlist')
                ->orderBy('waitlist_position', 'asc')
                ->get();

            $pos = 1;
            foreach ($remaining as $app) {
                $app->update(['waitlist_position' => $pos++]);
            }

            // Send notification email to the promoted patient
            if ($nextCandidate->patientProfile && $nextCandidate->patientProfile->email) {
                try {
                    Mail::to($nextCandidate->patientProfile->email)->send(new WaitlistPromotedMail($nextCandidate));
                } catch (\Throwable $e) {
                    Log::warning('Không thể gửi email thăng hạng danh sách chờ: ' . $e->getMessage());
                }
            }

            return $nextCandidate;
        });
    }
}
