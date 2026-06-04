<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Appointment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $payments = Payment::with(['patient.user', 'appointment'])->latest()->paginate(20);
        } elseif (auth()->user()->isPatient()) {
            $payments = Payment::where('patient_id', auth()->user()->patient->id)
                ->with('appointment')
                ->latest()
                ->paginate(10);
        } else {
            abort(403);
        }
        
        return view('payments.index', compact('payments'));
    }

    public function create(Appointment $appointment)
    {
        // Security: only patient can pay for their own appointment
        if ($appointment->patient_id != auth()->user()->patient->id) {
            abort(403);
        }
        
        // Check if payment already exists
        if ($appointment->payment) {
            return redirect()->route('payments.show', $appointment->payment)
                ->with('error', 'Payment already exists for this appointment.');
        }
        
        return view('payments.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        if ($appointment->patient_id != auth()->user()->patient->id) {
            abort(403);
        }
        
        $request->validate([
            'payment_method' => 'required|in:cash,card,online',
            'amount' => 'required|numeric|min:1',
        ]);
        
        $payment = Payment::create([
            'payment_number' => 'PAY-' . strtoupper(uniqid()),
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'completed',
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
        ]);
        
        // Create invoice
        $payment->invoice()->create([
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'patient_id' => $appointment->patient_id,
            'subtotal' => $request->amount,
            'tax' => 0,
            'total' => $request->amount,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'paid',
        ]);
        
        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment completed successfully.');
    }

    public function show(Payment $payment)
    {
        if (auth()->user()->isPatient() && $payment->patient_id != auth()->user()->patient->id) {
            abort(403);
        }
        
        $payment->load(['patient.user', 'appointment.doctor.user', 'invoice']);
        
        return view('payments.show', compact('payment'));
    }

    public function invoice(Payment $payment)
    {
        if (auth()->user()->isPatient() && $payment->patient_id != auth()->user()->patient->id) {
            abort(403);
        }
        
        $payment->load(['patient.user', 'appointment.doctor.user', 'invoice']);
        
        $pdf = PDF::loadView('payments.invoice-pdf', compact('payment'));
        
        return $pdf->download('invoice-' . $payment->payment_number . '.pdf');
    }
}