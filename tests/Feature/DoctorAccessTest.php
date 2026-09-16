<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DoctorAccessTest extends TestCase
{
    use DatabaseTransactions;

    private function makeDoctorUser(): User
    {
        return User::factory()->create(['role' => 'doctor', 'is_active' => true]);
    }

    private function makeDoctorWithUser(): Doctor
    {
        $user = $this->makeDoctorUser();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        return $doctor;
    }

    public function test_doctor_cannot_view_another_doctors_appointment(): void
    {
        $doctorA = $this->makeDoctorWithUser();
        $doctorB = $this->makeDoctorWithUser();
        $patient = Patient::factory()->create();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctorB->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'confirmed',
        ]);

        $this->actingAs($doctorA->user)
            ->get(route('doctor.appointments.show', $appointment))
            ->assertForbidden();
    }

    public function test_doctor_cannot_view_unrelated_patient_history(): void
    {
        $doctor = $this->makeDoctorWithUser();
        $otherDoctor = $this->makeDoctorWithUser();
        $patient = Patient::factory()->create();

        $otherAppointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $otherDoctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'confirmed',
        ]);

        MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $otherDoctor->id,
            'appointment_id' => $otherAppointment->id,
            'diagnosis' => 'Flu',
            'symptoms' => 'Fever, cough',
        ]);

        $this->actingAs($doctor->user)
            ->get(route('doctor.patients.history', $patient->id))
            ->assertForbidden();
    }

    public function test_doctor_can_view_own_patient_history(): void
    {
        $doctor = $this->makeDoctorWithUser();
        $patient = Patient::factory()->create();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'confirmed',
        ]);

        MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'diagnosis' => 'Flu',
            'symptoms' => 'Fever, cough',
        ]);

        $this->actingAs($doctor->user)
            ->get(route('doctor.patients.history', $patient->id))
            ->assertOk();
    }

    public function test_prescription_must_belong_to_appointments_medical_record(): void
    {
        $doctor = $this->makeDoctorWithUser();
        $patient = Patient::factory()->create();
        $otherPatient = Patient::factory()->create();

        $otherAppointment = Appointment::create([
            'patient_id' => $otherPatient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '11:00',
            'status' => 'confirmed',
        ]);

        $foreignRecord = MedicalRecord::create([
            'patient_id' => $otherPatient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $otherAppointment->id,
            'diagnosis' => 'Other',
            'symptoms' => 'Other',
        ]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'confirmed',
        ]);

        $this->actingAs($doctor->user)
            ->from(route('doctor.appointments.show', $appointment))
            ->post(route('doctor.appointments.add-prescription', $appointment), [
                'medical_record_id' => $foreignRecord->id,
                'medication_name' => 'Aspirin',
                'dosage' => '500mg',
                'frequency' => 'Once daily',
                'duration' => '5 days',
            ])
            ->assertSessionHas('error');

        $this->assertEquals(0, Prescription::where('medical_record_id', $foreignRecord->id)->count());
    }

    public function test_medical_record_does_not_auto_complete_pending_appointment(): void
    {
        $doctor = $this->makeDoctorWithUser();
        $patient = Patient::factory()->create();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'pending',
        ]);

        $this->actingAs($doctor->user)
            ->post(route('doctor.appointments.add-medical-record', $appointment), [
                'diagnosis' => 'Flu',
                'symptoms' => 'Fever',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'pending',
        ]);
    }

    public function test_medical_record_auto_completes_confirmed_appointment(): void
    {
        $doctor = $this->makeDoctorWithUser();
        $patient = Patient::factory()->create();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'confirmed',
        ]);

        $this->actingAs($doctor->user)
            ->post(route('doctor.appointments.add-medical-record', $appointment), [
                'diagnosis' => 'Flu',
                'symptoms' => 'Fever',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }
}