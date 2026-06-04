<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function appointmentReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->whereBetween('appointment_date', [$request->start_date, $request->end_date])
            ->get();

        $pdf = Pdf::loadView('admin.reports.appointment-pdf', compact('appointments', 'request'));
        
        return $pdf->download('appointment-report-' . date('Y-m-d') . '.pdf');
    }

    public function revenueReport(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:' . date('Y'),
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $query = Payment::where('status', 'completed');
        
        if ($request->month) {
            $query->whereYear('created_at', $request->year)
                  ->whereMonth('created_at', $request->month);
        } else {
            $query->whereYear('created_at', $request->year);
        }
        
        $payments = $query->get();
        $totalRevenue = $payments->sum('amount');
        
        $pdf = Pdf::loadView('admin.reports.revenue-pdf', compact('payments', 'totalRevenue', 'request'));
        
        return $pdf->download('revenue-report-' . date('Y-m-d') . '.pdf');
    }

    public function doctorReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $doctors = Doctor::with(['user', 'appointments' => function($q) use ($request) {
            $q->whereBetween('appointment_date', [$request->start_date, $request->end_date]);
        }])->get();

        $pdf = Pdf::loadView('admin.reports.doctor-pdf', compact('doctors', 'request'));
        
        return $pdf->download('doctor-report-' . date('Y-m-d') . '.pdf');
    }
}