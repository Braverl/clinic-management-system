<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $doctor = auth()->user()->doctor;
        $medicalRecords = MedicalRecord::where('doctor_id', $doctor->id)
            ->with(['patient.user'])
            ->latest()
            ->paginate(20);
        
        return view('doctor.medical-records.index', compact('medicalRecords'));
    }

    public function show(MedicalRecord $medicalRecord)
    {
        if ($medicalRecord->doctor_id != auth()->user()->doctor->id) {
            abort(403);
        }
        
        $medicalRecord->load(['patient.user', 'prescriptions']);
        
        return view('doctor.medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        if ($medicalRecord->doctor_id != auth()->user()->doctor->id) {
            abort(403);
        }
        
        return view('doctor.medical-records.edit', compact('medicalRecord'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        if ($medicalRecord->doctor_id != auth()->user()->doctor->id) {
            abort(403);
        }
        
        $request->validate([
            'diagnosis' => 'required|string',
            'symptoms' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string|max:20',
            'temperature' => 'nullable|string|max:20',
        ]);
        
        $medicalRecord->update($request->only([
            'diagnosis', 'symptoms', 'treatment_plan', 'notes',
            'weight', 'height', 'blood_pressure', 'temperature',
        ]));
        
        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Medical record updated successfully.');
    }
}