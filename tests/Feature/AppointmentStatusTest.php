<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AppointmentStatusTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAppointment(string $status = 'pending'): array
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();
        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => $status,
        ]);
        return [$patient, $appointment];
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_valid_transition_pending_to_confirmed(): void
    {
        [, $appointment] = $this->makeAppointment('pending');

        $this->actingAs($this->makeAdmin())
            ->post(route('admin.appointments.update-status', $appointment), [
                'status' => 'confirmed',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_illegal_transition_pending_to_completed_blocked(): void
    {
        [, $appointment] = $this->makeAppointment('pending');

        $this->actingAs($this->makeAdmin())
            ->from(route('admin.appointments.index'))
            ->post(route('admin.appointments.update-status', $appointment), [
                'status' => 'completed',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'pending',
        ]);
    }

    public function test_completed_appointment_cannot_transition(): void
    {
        [, $appointment] = $this->makeAppointment('completed');

        $this->actingAs($this->makeAdmin())
            ->from(route('admin.appointments.index'))
            ->post(route('admin.appointments.update-status', $appointment), [
                'status' => 'pending',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_completed_appointment_cannot_be_deleted(): void
    {
        [, $appointment] = $this->makeAppointment('completed');

        $this->actingAs($this->makeAdmin())
            ->from(route('admin.appointments.index'))
            ->delete(route('admin.appointments.destroy', $appointment))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
    }

    public function test_paid_appointment_cannot_be_deleted(): void
    {
        [$patient, $appointment] = $this->makeAppointment('confirmed');

        Payment::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'amount' => 500,
            'payment_method' => 'cash',
            'status' => 'completed',
            'transaction_id' => 'TXN-1234567890',
        ]);

        $this->actingAs($this->makeAdmin())
            ->from(route('admin.appointments.index'))
            ->delete(route('admin.appointments.destroy', $appointment))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
    }

    public function test_pending_appointment_can_be_deleted(): void
    {
        [, $appointment] = $this->makeAppointment('pending');

        $this->actingAs($this->makeAdmin())
            ->delete(route('admin.appointments.destroy', $appointment))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }

    public function test_active_doctor_cannot_be_deleted_with_appointments(): void
    {
        [, $appointment] = $this->makeAppointment('pending');

        $doctor = $appointment->doctor;

        $this->actingAs($this->makeAdmin())
            ->from(route('admin.doctors.index'))
            ->delete(route('admin.doctors.destroy', $doctor))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('doctors', ['id' => $doctor->id]);
    }

    public function test_patient_with_active_appointments_cannot_be_deleted(): void
    {
        [$patient] = $this->makeAppointment('pending');

        $this->actingAs($this->makeAdmin())
            ->from(route('admin.patients.index'))
            ->delete(route('admin.patients.destroy', $patient))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('patients', ['id' => $patient->id]);
    }
}