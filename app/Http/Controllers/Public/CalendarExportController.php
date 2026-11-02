<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\CalendarSyncService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarExportController extends Controller
{
    public function __construct(protected CalendarSyncService $calendarService)
    {
    }

    /**
     * Download .ics iCalendar file for appointment.
     */
    public function downloadIcs(string $code): Response
    {
        $appointment = Appointment::where('appointment_code', $code)
            ->with(['doctor.user', 'specialty', 'patientProfile'])
            ->firstOrFail();

        $icsContent = $this->calendarService->generateIcsContent($appointment);

        $filename = "LichKham_TamAnh_{$appointment->appointment_code}.ics";

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
