<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AppointmentBookingTest extends TestCase
{
    use DatabaseTransactions;

    private function makePatient(): Patient
    {
        return Patient::factory()->create();
    }

    private function makeDoctor(): Doctor
    {
        return Doctor::factory()->create([
            'available_days' => null,
            'available_from' => '09:00:00',
            'available_to' => '17:00:00',
        ]);
    }

    public function test_patient_can_book_appointment(): void
    {
        $patient = $this->makePatient();
        $doctor = $this->makeDoctor();

        $this->actingAs($patient->user)
            ->post(route('patient.appointments.store'), [
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->addDay()->format('Y-m-d'),
                'appointment_time' => '10:00',
                'symptoms' => 'Headache',
            ])
            ->assertRedirect(route('patient.appointments.index'));

        $appointment = Appointment::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->first();

        $this->assertNotNull($appointment);
        $this->assertEquals('pending', $appointment->status);
        $this->assertStringStartsWith('APT', $appointment->appointment_number);
    }

    public function test_double_booking_is_blocked(): void
    {
        $patient = $this->makePatient();
        $doctor = $this->makeDoctor();
        $date = now()->addDay()->format('Y-m-d');

        $existing = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'appointment_time' => '10:00',
            'status' => 'pending',
        ]);

        $this->actingAs($patient->user)
            ->from(route('patient.appointments.book'))
            ->post(route('patient.appointments.store'), [
                'doctor_id' => $doctor->id,
                'appointment_date' => $date,
                'appointment_time' => '10:00',
            ])
            ->assertRedirect(route('patient.appointments.book'))
            ->assertSessionHas('error');

        $this->assertEquals(1, Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $date)
            ->where('appointment_time', '10:00')
            ->where('status', '!=', 'cancelled')
            ->count());
    }

    public function test_inactive_doctor_cannot_receive_bookings(): void
    {
        $patient = $this->makePatient();
        $doctor = $this->makeDoctor();
        $doctor->user->update(['is_active' => false]);

        $this->actingAs($patient->user)
            ->from(route('patient.appointments.book'))
            ->post(route('patient.appointments.store'), [
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->addDay()->format('Y-m-d'),
                'appointment_time' => '10:00',
            ])
            ->assertRedirect(route('patient.appointments.book'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
        ]);
    }

    public function test_time_outside_working_hours_is_rejected(): void
    {
        $patient = $this->makePatient();
        $doctor = $this->makeDoctor();

        $this->actingAs($patient->user)
            ->from(route('patient.appointments.book'))
            ->post(route('patient.appointments.store'), [
                'doctor_id' => $doctor->id,
                'appointment_date' => now()->addDay()->format('Y-m-d'),
                'appointment_time' => '18:00',
            ])
            ->assertRedirect(route('patient.appointments.book'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
        ]);
    }

    public function test_patient_cannot_book_for_another_patient(): void
    {
        $patient = $this->makePatient();
        $otherPatient = $this->makePatient();
        $doctor = $this->makeDoctor();

        $otherAppointment = Appointment::create([
            'patient_id' => $otherPatient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'pending',
        ]);

        $this->actingAs($patient->user)
            ->get(route('patient.appointments.show', $otherAppointment))
            ->assertForbidden();
    }
}