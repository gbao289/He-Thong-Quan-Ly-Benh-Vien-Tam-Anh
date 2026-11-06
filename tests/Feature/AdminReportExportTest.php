<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PatientProfile;
use Carbon\Carbon;
use Tests\TestCase;

class AdminReportExportTest extends TestCase
{
    public function test_admin_can_export_an_excel_report(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'report-admin@example.test'],
            [
                'name' => 'Admin Báo cáo',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        $response = $this->actingAs($admin)->get(route('admin.reports.export', [
            'report' => 'examination-status',
            'format' => 'excel',
            'date_from' => '2026-11-01',
            'date_to' => '2026-11-30',
        ]));

        $response->assertDownload('examination-status_20261101_20261130.xlsx');
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('content-type')
        );
    }

    public function test_admin_can_open_a_printable_pdf_report(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'report-pdf-admin@example.test'],
            [
                'name' => 'Admin Báo cáo',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        $patient = PatientProfile::firstOrCreate(
            ['patient_code' => 'BN-REPORT-0001'],
            [
                'full_name' => 'Nguyễn Văn Báo Cáo',
                'dob' => '1990-01-15',
                'gender' => 'male',
                'phone' => '0900000001',
                'email' => 'patient-report@example.test',
                'created_at' => '2026-11-10 09:00:00',
            ]
        );
        $patient->update(['created_at' => '2026-11-10 09:00:00']);

        $expectedCount = PatientProfile::whereBetween('created_at', [
            Carbon::parse('2026-11-01')->startOfDay(),
            Carbon::parse('2026-11-30')->endOfDay(),
        ])->count();

        $this->actingAs($admin)
            ->get(route('admin.reports.export', [
                'report' => 'patients',
                'format' => 'pdf',
                'date_from' => '2026-11-01',
                'date_to' => '2026-11-30',
            ]))
            ->assertOk()
            ->assertSee('Danh sách bệnh nhân - Bệnh viện Tâm Anh')
            ->assertSee('window.print()')
            ->assertSee('Nguyễn Văn Báo Cáo')
            ->assertSee("Tổng hồ sơ bệnh nhân trong kỳ: {$expectedCount}");
    }

    public function test_non_admin_cannot_export_reports(): void
    {
        $doctor = User::firstOrCreate(
            ['email' => 'report-doctor@example.test'],
            [
                'name' => 'Bác sĩ',
                'password' => bcrypt('password'),
                'role' => User::ROLE_DOCTOR,
            ]
        );

        $this->actingAs($doctor)
            ->get(route('admin.reports.export', [
                'report' => 'patients',
                'format' => 'excel',
                'date_from' => '2026-11-01',
                'date_to' => '2026-11-30',
            ]))
            ->assertForbidden();
    }
}
