<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_patient_can_login(): void
    {
        $password = bcrypt('password');
        $user = User::factory()->create([
            'email' => 'patient@test.com',
            'password' => $password,
            'role' => 'patient',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'patient@test.com',
            'password' => 'password',
            'role' => 'patient',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_wrong_role_selected(): void
    {
        User::factory()->create([
            'email' => 'patient@test.com',
            'password' => bcrypt('password'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'patient@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }

    public function test_inactive_patient_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'unverified@test.com',
            'password' => bcrypt('password'),
            'role' => 'patient',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'unverified@test.com',
            'password' => 'password',
            'role' => 'patient',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_dashboard_requires_admin_role(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
            'is_active' => true,
        ]);

        $response = $this->actingAs($patient)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_doctor_dashboard_requires_doctor_role(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
            'is_active' => true,
        ]);

        $response = $this->actingAs($patient)->get(route('doctor.dashboard'));

        $response->assertForbidden();
    }

    public function test_unauthourized_user_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }
}