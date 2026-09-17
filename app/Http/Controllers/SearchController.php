<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function globalSearch(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:60',
        ]);

        $query = $request->input('query');
        $user = auth()->user();
        $results = [];

        if ($user->isAdmin()) {
            $results['patients'] = Patient::with('user')
                ->whereHas('user', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->limit(10)
                ->get()
                ->map(fn ($patient) => [
                    'id' => $patient->id,
                    'name' => $patient->user->name,
                    'email' => $patient->user->email,
                    'type' => 'patient',
                ]);

            $results['doctors'] = Doctor::with('user', 'department')
                ->whereHas('user', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%");
                })
                ->orWhere('specialization', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get()
                ->map(fn ($doctor) => [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name,
                    'specialization' => $doctor->specialization,
                    'department' => $doctor->department->name ?? null,
                    'type' => 'doctor',
                ]);

            $results['appointments'] = Appointment::with(['patient.user', 'doctor.user'])
                ->where(function ($q) use ($query) {
                    $q->whereHas('patient.user', function ($uq) use ($query) {
                        $uq->where('name', 'LIKE', "%{$query}%");
                    })
                    ->orWhereHas('doctor.user', function ($uq) use ($query) {
                        $uq->where('name', 'LIKE', "%{$query}%");
                    })
                    ->orWhere('appointment_number', 'LIKE', "%{$query}%");
                })
                ->limit(10)
                ->get()
                ->map(fn ($appointment) => [
                    'id' => $appointment->id,
                    'number' => $appointment->appointment_number,
                    'patient' => $appointment->patient->user->name ?? null,
                    'doctor' => $appointment->doctor->user->name ?? null,
                    'date' => $appointment->appointment_date->format('Y-m-d'),
                    'status' => $appointment->status,
                    'type' => 'appointment',
                ]);
        } elseif ($user->isDoctor()) {
            $doctor = $user->doctor;

            if ($doctor) {
                $patientIds = $doctor->appointments()->pluck('patient_id');

                $results['patients'] = Patient::with('user')
                    ->whereIn('id', $patientIds)
                    ->whereHas('user', function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('email', 'LIKE', "%{$query}%");
                    })
                    ->limit(10)
                    ->get()
                    ->map(fn ($patient) => [
                        'id' => $patient->id,
                        'name' => $patient->user->name,
                        'email' => $patient->user->email,
                        'type' => 'patient',
                    ]);

                $results['appointments'] = Appointment::with('patient.user')
                    ->where('doctor_id', $doctor->id)
                    ->where(function ($q) use ($query) {
                        $q->whereHas('patient.user', function ($uq) use ($query) {
                            $uq->where('name', 'LIKE', "%{$query}%")
                                ->orWhere('email', 'LIKE', "%{$query}%");
                        })
                        ->orWhere('appointment_number', 'LIKE', "%{$query}%");
                    })
                    ->limit(10)
                    ->get()
                    ->map(fn ($appointment) => [
                        'id' => $appointment->id,
                        'number' => $appointment->appointment_number,
                        'patient' => $appointment->patient->user->name ?? null,
                        'date' => $appointment->appointment_date->format('Y-m-d'),
                        'status' => $appointment->status,
                        'type' => 'appointment',
                    ]);
            }
        } else {
            $patient = $user->patient;

            if ($patient) {
                $results['doctors'] = Doctor::with('user', 'department')
                    ->whereHas('appointments', function ($q) use ($patient) {
                        $q->where('patient_id', $patient->id);
                    })
                    ->whereHas('user', function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('email', 'LIKE', "%{$query}%");
                    })
                    ->limit(10)
                    ->get()
                    ->map(fn ($doctor) => [
                        'id' => $doctor->id,
                        'name' => $doctor->user->name,
                        'specialization' => $doctor->specialization,
                        'department' => $doctor->department->name ?? null,
                        'type' => 'doctor',
                    ]);

                $results['appointments'] = Appointment::with('doctor.user')
                    ->where('patient_id', $patient->id)
                    ->where(function ($q) use ($query) {
                        $q->whereHas('doctor.user', function ($uq) use ($query) {
                            $uq->where('name', 'LIKE', "%{$query}%");
                        })
                        ->orWhere('appointment_number', 'LIKE', "%{$query}%");
                    })
                    ->limit(10)
                    ->get()
                    ->map(fn ($appointment) => [
                        'id' => $appointment->id,
                        'number' => $appointment->appointment_number,
                        'doctor' => $appointment->doctor->user->name ?? null,
                        'date' => $appointment->appointment_date->format('Y-m-d'),
                        'status' => $appointment->status,
                        'type' => 'appointment',
                    ]);
            }
        }

        return response()->json(['results' => $results]);
    }

    public function index(Request $request)
    {
        $query = $request->input('query', '');
        $results = [];

        if ($query) {
            $response = $this->globalSearch($request);
            $results = json_decode($response->getContent(), true)['results'] ?? [];
        }

        return view('search.results', compact('query', 'results'));
    }
}
