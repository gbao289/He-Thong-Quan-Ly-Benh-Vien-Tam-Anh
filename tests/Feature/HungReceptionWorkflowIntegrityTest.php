<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\PatientProfile;
use App\Models\Specialty;
use App\Models\User;
use Tests\TestCase;

class HungReceptionWorkflowIntegrityTest extends TestCase
{
    protected User $receptionist;
    protected User $doctor;
    protected User $admin;
    protected PatientProfile $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->receptionist = User::where('role', User::ROLE_RECEPTIONIST)->first()
            ?? User::factory()->create([
                'email' => 'rec_integrity_' . uniqid() . '@tamanh.vn',
                'role' => User::ROLE_RECEPTIONIST,
            ]);

        $this->admin = User::where('role', User::ROLE_ADMIN)->first()
            ?? User::factory()->create([
                'email' => 'admin_integrity_' . uniqid() . '@tamanh.vn',
                'role' => User::ROLE_ADMIN,
            ]);

        $this->patient = PatientProfile::first() ?? PatientProfile::create([
            'user_id' => $this->receptionist->id,
            'patient_code' => 'BN-' . rand(100000, 999999),
            'full_name' => 'Lê Hoàng Nam',
            'dob' => '1988-12-25',
            'gender' => 'male',
            'phone' => '0988776655',
        ]);
    }

    /**
     * Test 1: Verify the updated sidebar contains the smart search bar,
     * keyboard shortcut hints, and ZERO personal tribute modals or git PR links.
     */
    public function test_sidebar_contains_search_and_zero_personal_tribute(): void
    {
        $response = $this->actingAs($this->receptionist)->get(route('reception.appointments.index'));
        $response->assertStatus(200);

        // Must have smart search box and shortcut
        $response->assertSee('id="sidebarSearchInput"', false);
        $response->assertSee('sidebarSearchBadge', false);
        $response->assertSee('removeVietnameseTones', false);

        // Must NOT contain personal praises or student tribute modal
        $response->assertDontSee('projectContributionModal');
        $response->assertDontSee('ĐỒ ÁN XUẤT SẮC');
        $response->assertDontSee('https://github.com/quy0358/He-Thong-Quan-Ly-Benh-Vien-Tam-Anh/pull/6');
    }

    /**
     * Test 2: Smoke test all essential Receptionist modules to ensure HTTP 200 and zero errors.
     */
    public function test_all_hung_core_receptionist_routes_return_ok(): void
    {
        $routes = [
            'reception.appointments.index',
            'reception.patients.index',
            'reception.day-beds.index',
            'reception.mobility.index',
            'reception.wayfinding.index',
            'reception.invoices.index',
            'reception.wallets.index',
            'reception.insurance-transfers.index',
            'reception.private-insurance.index',
            'reception.certificates.index',
            'reception.consents.index',
            'reception.pharmacy.index',
            'reception.pharmacy.inventory.index',
            'reception.narcotics.index',
            'reception.cold-chain.index',
            'reception.vaccinations.index',
            'reception.deliveries.index',
            'reception.code-red.index',
            'reception.ambulances.index',
            'reception.news2-rrt.index',
            'reception.risk-assessments.index',
            'reception.medical-incidents.index',
            'reception.emergency-custody.index',
            'reception.visitor-passes.index',
            'reception.occupational-exposure.index',
            'reception.lab-orders.index',
            'reception.blood-transfusions.index',
            'reception.bed-board.index',
            'reception.hemodialysis.index',
            'reception.cssd.index',
            'reception.nutrition.index',
            'reception.social-work.index',
            'reception.living-will.index',
            'reception.corporate-screening.index',
            'reception.day-surgeries.index',
            'reception.chronic-recalls.index',
            'reception.analytics.index',
            'reception.surveys.index',
            'reception.notifications.index',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($this->receptionist)->get(route($routeName));
            $this->assertEquals(200, $response->status(), "Route {$routeName} failed with HTTP status {$response->status()}");
        }
    }

    /**
     * Test 3: Public Kiosk Self-service renders and operates cleanly.
     */
    public function test_public_kiosk_renders_and_operates(): void
    {
        $response = $this->get('/kiosk');
        $response->assertStatus(200);
        $response->assertSee('Kiosk Tự Phục Vụ');
    }
}
