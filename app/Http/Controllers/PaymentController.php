<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $payments = Payment::with(['patient.user', 'appointment.doctor.user', 'invoice'])
                ->latest()
                ->paginate(20);

            $stats = [
                'total_collected' => Payment::where('status', 'completed')->sum('amount'),
                'total_refunded' => Payment::where('status', 'refunded')->sum('amount'),
                'monthly_revenue' => Payment::where('status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'total_count' => Payment::count(),
            ];

            return view('payments.index', compact('payments', 'stats'));
        }

        $patient = $user->patient;

        if (!$patient) {
            abort(403);
        }

        $payments = Payment::where('patient_id', $patient->id)
            ->with(['appointment.doctor.user', 'invoice'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_paid' => Payment::where('patient_id', $patient->id)->where('status', 'completed')->sum('amount'),
            'total_refunded' => Payment::where('patient_id', $patient->id)->where('status', 'refunded')->sum('amount'),
            'total_count' => Payment::where('patient_id', $patient->id)->count(),
        ];

        return view('payments.index', compact('payments', 'stats'));
    }

    public function create(Appointment $appointment)
    {
        $user = auth()->user();

        if (!$user->isPatient() || $appointment->patient_id != $user->patient->id) {
            abort(403);
        }

        if ($appointment->payment) {
            return redirect()->route('patient.payments.show', $appointment->payment)
                ->with('info', 'Payment already exists for this appointment.');
        }

        if (!in_array($appointment->status, ['confirmed', 'completed'])) {
            return redirect()->route('patient.appointments.show', $appointment)
                ->with('error', 'You can only pay after appointment is confirmed or completed.');
        }

        $amount = $appointment->doctor->consultation_fee;

        return view('payments.create', compact('appointment', 'amount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'payment_method' => 'required|in:cash,card,insurance,online',
        ]);

        $appointment = Appointment::with('doctor')->findOrFail($request->appointment_id);

        if (auth()->user()->patient->id != $appointment->patient_id) {
            abort(403);
        }

        if (!in_array($appointment->status, ['confirmed', 'completed'])) {
            return redirect()->back()->with('error', 'You can only pay after appointment is confirmed or completed.');
        }

        if ($appointment->payment) {
            return redirect()->back()->with('error', 'Payment already exists for this appointment.');
        }

        $amount = $appointment->doctor->consultation_fee;

        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'amount' => $amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            ]);

            Invoice::create([
                'payment_id' => $payment->id,
                'patient_id' => $appointment->patient_id,
                'subtotal' => $amount,
                'tax' => 0,
                'total' => $amount,
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'paid',
            ]);

            auth()->user()->notifications()->create([
                'title' => 'Payment Successful',
                'message' => "Payment of \${$amount} completed for appointment {$appointment->appointment_number}.",
                'type' => 'success',
                'link' => route('patient.payments.show', $payment),
            ]);

            DB::commit();

            return redirect()->route('patient.payments.show', $payment)
                ->with('success', 'Payment completed successfully! Invoice generated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function show(Payment $payment)
    {
        $user = auth()->user();

        if ($user->isPatient() && $payment->patient_id != $user->patient->id) {
            abort(403);
        }

        $payment->load(['patient.user', 'appointment.doctor.user', 'appointment.medicalRecord.prescriptions', 'invoice']);

        if ($user->isAdmin()) {
            return view('payments.show', compact('payment'));
        }

        return view('payments.show', compact('payment'));
    }

    public function invoice(Payment $payment)
    {
        $user = auth()->user();

        if ($user->isPatient() && $payment->patient_id != $user->patient->id) {
            abort(403);
        }

        $payment->load(['patient.user', 'appointment.doctor.user', 'invoice']);

        $pdf = Pdf::loadView('payments.invoice-pdf', compact('payment'));

        return $pdf->download('invoice-' . $payment->invoice->invoice_number . '.pdf');
    }

    public function refund(Payment $payment)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($payment->status !== 'completed') {
            return redirect()->back()->with('error', 'Only completed payments can be refunded.');
        }

        DB::beginTransaction();

        try {
            $payment->update([
                'status' => 'refunded',
            ]);

            if ($payment->invoice) {
                $payment->invoice->update([
                    'status' => 'refunded',
                    'due_date' => now(),
                ]);
            }

            $payment->patient->user->notifications()->create([
                'title' => 'Payment Refunded',
                'message' => "Payment of \${$payment->amount} ({$payment->payment_number}) has been refunded.",
                'type' => 'warning',
                'link' => route('patient.payments.show', $payment),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Payment refunded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Refund processing failed. Please try again.');
        }
    }
}
