<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminDataProtectionTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function makeDoctor(): Doctor
    {
        return Doctor::factory()->create();
    }

    private function makePatient(): Patient
    {
        return Patient::factory()->create();
    }

    public function test_admin_cannot_delete_appointment_with_medical_record(): void
    {
        $admin = $this->makeAdmin();
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'pending',
        ]);

        MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'diagnosis' => 'Migraine',
            'symptoms' => 'Headache',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.appointments.index'))
            ->delete(route('admin.appointments.destroy', $appointment))
            ->assertRedirect(route('admin.appointments.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
        $this->assertDatabaseHas('medical_records', ['appointment_id' => $appointment->id]);
    }

    public function test_admin_cannot_delete_appointment_without_data(): void
    {
        $admin = $this->makeAdmin();
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.appointments.destroy', $appointment))
            ->assertRedirect(route('admin.appointments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }

    public function test_admin_cannot_delete_doctor_with_payment_records(): void
    {
        $admin = $this->makeAdmin();
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->subDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'completed',
        ]);

        Payment::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'amount' => 100.00,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.doctors.index'))
            ->delete(route('admin.doctors.destroy', $doctor))
            ->assertRedirect(route('admin.doctors.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('doctors', ['id' => $doctor->id]);
        $this->assertDatabaseHas('payments', ['appointment_id' => $appointment->id]);
    }

    public function test_admin_can_delete_doctor_without_activity(): void
    {
        $admin = $this->makeAdmin();
        $doctor = $this->makeDoctor();

        $this->actingAs($admin)
            ->delete(route('admin.doctors.destroy', $doctor))
            ->assertRedirect(route('admin.doctors.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('doctors', ['id' => $doctor->id]);
    }
}