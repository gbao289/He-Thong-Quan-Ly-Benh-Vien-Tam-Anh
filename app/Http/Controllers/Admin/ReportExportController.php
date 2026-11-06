<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\PatientProfile;
use App\Support\XlsxReportWriter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportExportController extends Controller
{
    private const REPORTS = [
        'patients' => 'Danh sách bệnh nhân',
        'appointments' => 'Báo cáo lịch khám',
        'doctors' => 'Danh sách bác sĩ',
        'examination-status' => 'Tình trạng khám bệnh',
    ];

    public function export(Request $request, XlsxReportWriter $xlsxWriter): Response|BinaryFileResponse
    {
        $validated = $request->validate([
            'report' => ['required', Rule::in(array_keys(self::REPORTS))],
            'format' => ['required', Rule::in(['pdf', 'excel'])],
            'date_from' => ['required', 'date', 'before_or_equal:date_to'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
        ]);

        $from = Carbon::parse($validated['date_from'])->startOfDay();
        $to = Carbon::parse($validated['date_to'])->endOfDay();
        [$headers, $rows] = $this->reportData($validated['report'], $from, $to);
        $reportTitle = self::REPORTS[$validated['report']];
        $summary = match ($validated['report']) {
            'patients' => 'Tổng hồ sơ bệnh nhân trong kỳ: ' . $rows->count(),
            'appointments' => 'Tổng lịch khám trong kỳ: ' . $rows->count(),
            'doctors' => 'Tổng số bác sĩ: ' . $rows->count(),
            'examination-status' => 'Tổng lượt khám trong kỳ: ' . (int) ($rows->last()[1] ?? 0),
        };
        $filename = sprintf(
            '%s_%s_%s',
            $validated['report'],
            $from->format('Ymd'),
            $to->format('Ymd')
        );

        if ($validated['format'] === 'excel') {
            $path = $xlsxWriter->write($reportTitle, $headers, $rows, $summary);

            return response()->download(
                $path,
                "{$filename}.xlsx",
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            )->deleteFileAfterSend(true);
        }

        return response()->view('admin.reports.print', [
            'reportTitle' => $reportTitle,
            'headers' => $headers,
            'rows' => $rows,
            'summary' => $summary,
            'dateFrom' => $from,
            'dateTo' => $to,
        ]);
    }

    /**
     * @return array{0: array<int, string>, 1: Collection<int, array<int, string|int|float>>}
     */
    private function reportData(string $report, Carbon $from, Carbon $to): array
    {
        return match ($report) {
            'patients' => $this->patientReport($from, $to),
            'appointments' => $this->appointmentReport($from, $to),
            'doctors' => $this->doctorReport($from, $to),
            'examination-status' => $this->examinationStatusReport($from, $to),
        };
    }

    private function patientReport(Carbon $from, Carbon $to): array
    {
        $patients = PatientProfile::query()
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('full_name')
            ->get();

        $rows = $patients->map(fn (PatientProfile $patient) => [
            $patient->patient_code,
            $patient->full_name,
            $patient->gender_name,
            $patient->dob?->format('d/m/Y') ?? '',
            $patient->phone,
            $patient->email ?? '',
            $patient->address ?? '',
            $patient->created_at->format('d/m/Y H:i'),
        ]);

        return [[
            'Mã bệnh nhân', 'Họ và tên', 'Giới tính', 'Ngày sinh', 'Điện thoại',
            'Email', 'Địa chỉ', 'Ngày tạo hồ sơ',
        ], $rows];
    }

    private function appointmentReport(Carbon $from, Carbon $to): array
    {
        $appointments = Appointment::with(['patientProfile', 'doctor.user', 'specialty'])
            ->whereBetween('appointment_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $rows = $appointments->map(fn (Appointment $appointment) => [
            $appointment->appointment_code,
            $appointment->appointment_date->format('d/m/Y'),
            $appointment->appointment_time ? substr($appointment->appointment_time, 0, 5) : '',
            $appointment->shift_name,
            $appointment->patientProfile->full_name,
            $appointment->patientProfile->patient_code,
            $appointment->doctor->full_name_with_title,
            $appointment->specialty->name,
            $appointment->status_name,
        ]);

        return [[
            'Mã lịch khám', 'Ngày khám', 'Giờ khám', 'Ca khám', 'Bệnh nhân',
            'Mã bệnh nhân', 'Bác sĩ', 'Chuyên khoa', 'Tình trạng',
        ], $rows];
    }

    private function doctorReport(Carbon $from, Carbon $to): array
    {
        $doctors = Doctor::with(['user', 'specialty'])
            ->withCount(['appointments' => fn ($query) => $query->whereBetween(
                'appointment_date',
                [$from->toDateString(), $to->toDateString()]
            )])
            ->orderBy('id')
            ->get();

        $rows = $doctors->map(fn (Doctor $doctor) => [
            $doctor->full_name_with_title,
            $doctor->user->email,
            $doctor->specialty->name,
            $doctor->room_number,
            number_format((float) $doctor->consultation_fee, 0, ',', '.') . ' VNĐ',
            $doctor->is_available ? 'Đang hoạt động' : 'Tạm ngưng',
            $doctor->appointments_count,
        ]);

        return [[
            'Bác sĩ', 'Email', 'Chuyên khoa', 'Phòng khám',
            'Giá khám tham khảo', 'Tình trạng', 'Lượt khám trong kỳ',
        ], $rows];
    }

    private function examinationStatusReport(Carbon $from, Carbon $to): array
    {
        $counts = Appointment::query()
            ->whereBetween('appointment_date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statuses = [
            Appointment::STATUS_PENDING => 'Chờ tiếp nhận',
            Appointment::STATUS_CONFIRMED => 'Đã tiếp nhận - Chờ khám',
            Appointment::STATUS_COMPLETED => 'Đã khám hoàn tất',
            Appointment::STATUS_CANCELLED => 'Đã hủy',
        ];
        $total = (int) $counts->sum();
        $rows = collect($statuses)->map(fn (string $label, string $status) => [
            $label,
            (int) ($counts[$status] ?? 0),
            $total > 0 ? number_format(($counts[$status] ?? 0) * 100 / $total, 1) . '%' : '0%',
        ])->values();
        $rows->push(['Tổng cộng', $total, '100%']);

        return [['Tình trạng', 'Số lượt khám', 'Tỷ lệ'], $rows];
    }
}
