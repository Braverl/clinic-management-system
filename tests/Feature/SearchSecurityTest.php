<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SearchSecurityTest extends TestCase
{
    use DatabaseTransactions;

    private function makeDoctor(): Doctor
    {
        return Doctor::factory()->create();
    }

    private function makePatient(): Patient
    {
        return Patient::factory()->create();
    }

    private function makeAppointment(Patient $patient, Doctor $doctor, string $time = '10:00'): Appointment
    {
        return Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => $time,
            'status' => 'pending',
        ]);
    }

    public function test_doctor_cannot_see_another_doctors_appointment_by_number(): void
    {
        $doctorA = $this->makeDoctor();
        $doctorB = $this->makeDoctor();
        $patientB = $this->makePatient();

        $otherAppointment = $this->makeAppointment($patientB, $doctorB);

        $response = $this->actingAs($doctorA->user)
            ->getJson(route('search.live', ['query' => $otherAppointment->appointment_number]));

        $response->assertOk();
        $this->assertCount(0, $response->json('results.appointments'));
    }

    public function test_doctor_cannot_see_strangers_patient_by_name(): void
    {
        $doctorA = $this->makeDoctor();
        $doctorB = $this->makeDoctor();
        $patientB = $this->makePatient();

        $this->makeAppointment($patientB, $doctorB);

        $response = $this->actingAs($doctorA->user)
            ->getJson(route('search.live', ['query' => $patientB->user->name]));

        $response->assertOk();
        $this->assertCount(0, $response->json('results.patients'));
    }

    public function test_doctor_can_see_own_appointment_by_number(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        $appointment = $this->makeAppointment($patient, $doctor);

        $response = $this->actingAs($doctor->user)
            ->getJson(route('search.live', ['query' => $appointment->appointment_number]));

        $response->assertOk();
        $this->assertCount(1, $response->json('results.appointments'));
        $this->assertEquals($appointment->appointment_number, $response->json('results.appointments.0.number'));
    }

    public function test_patient_cannot_see_another_patients_appointment_by_number(): void
    {
        $doctor = $this->makeDoctor();
        $patientA = $this->makePatient();
        $patientB = $this->makePatient();

        $otherAppointment = $this->makeAppointment($patientB, $doctor);

        $response = $this->actingAs($patientA->user)
            ->getJson(route('search.live', ['query' => $otherAppointment->appointment_number]));

        $response->assertOk();
        $this->assertCount(0, $response->json('results.appointments'));
    }

    public function test_patient_cannot_see_doctor_outside_their_relationship(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        $response = $this->actingAs($patient->user)
            ->getJson(route('search.live', ['query' => $doctor->user->name]));

        $response->assertOk();
        $this->assertCount(0, $response->json('results.doctors'));
    }

    public function test_patient_can_see_own_appointment_by_number(): void
    {
        $doctor = $this->makeDoctor();
        $patient = $this->makePatient();

        $appointment = $this->makeAppointment($patient, $doctor);

        $response = $this->actingAs($patient->user)
            ->getJson(route('search.live', ['query' => $appointment->appointment_number]));

        $response->assertOk();
        $this->assertCount(1, $response->json('results.appointments'));
        $this->assertEquals($appointment->appointment_number, $response->json('results.appointments.0.number'));
    }
}