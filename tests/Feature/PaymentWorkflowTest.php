<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private function makeConfirmedAppointment(): array
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create([
            'available_days' => null,
            'consultation_fee' => 1000,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'confirmed',
        ]);

        return [$patient, $appointment];
    }

    public function test_patient_can_pay_for_confirmed_appointment(): void
    {
        [$patient, $appointment] = $this->makeConfirmedAppointment();

        $this->actingAs($patient->user)
            ->post(route('patient.payments.store'), [
                'appointment_id' => $appointment->id,
                'payment_method' => 'cash',
            ])
            ->assertSessionHas('success');

        $payment = Payment::where('appointment_id', $appointment->id)->first();

        $this->assertNotNull($payment);
        $this->assertEquals(1000, $payment->amount);
        $this->assertEquals('completed', $payment->status);
        $this->assertDatabaseHas('invoices', [
            'payment_id' => $payment->id,
            'patient_id' => $patient->id,
            'total' => 1000,
            'status' => 'paid',
        ]);
    }

    public function test_cannot_pay_for_pending_appointment(): void
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'pending',
        ]);

        $this->actingAs($patient->user)
            ->from(route('patient.appointments.show', $appointment))
            ->post(route('patient.payments.store'), [
                'appointment_id' => $appointment->id,
                'payment_method' => 'cash',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('payments', [
            'appointment_id' => $appointment->id,
        ]);
    }

    public function test_cannot_pay_twice_for_same_appointment(): void
    {
        [$patient, $appointment] = $this->makeConfirmedAppointment();

        Payment::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'amount' => 1000,
            'payment_method' => 'cash',
            'status' => 'completed',
            'transaction_id' => 'TXN-EXISTING',
        ]);

        $this->actingAs($patient->user)
            ->post(route('patient.payments.store'), [
                'appointment_id' => $appointment->id,
                'payment_method' => 'card',
            ])
            ->assertSessionHas('error');

        $this->assertEquals(1, Payment::where('appointment_id', $appointment->id)->count());
    }

    public function test_patient_cannot_pay_for_another_patients_appointment(): void
    {
        [$patient, $appointment] = $this->makeConfirmedAppointment();
        $other = Patient::factory()->create();

        $this->actingAs($other->user)
            ->post(route('patient.payments.store'), [
                'appointment_id' => $appointment->id,
                'payment_method' => 'cash',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('payments', [
            'appointment_id' => $appointment->id,
        ]);
    }

    public function test_only_admin_can_refund(): void
    {
        [$patient, $appointment] = $this->makeConfirmedAppointment();

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'amount' => 1000,
            'payment_method' => 'cash',
            'status' => 'completed',
            'transaction_id' => 'TXN-1234567890',
        ]);

        Invoice::create([
            'payment_id' => $payment->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-1234567890',
            'subtotal' => 1000,
            'tax' => 0,
            'total' => 1000,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'paid',
        ]);

        $this->actingAs($patient->user)
            ->post(route('admin.payments.refund', $payment))
            ->assertForbidden();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'completed']);

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)
            ->post(route('admin.payments.refund', $payment))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'refunded']);
        $this->assertDatabaseHas('invoices', ['payment_id' => $payment->id, 'status' => 'refunded']);
    }
}