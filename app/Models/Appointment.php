<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number', 'patient_id', 'doctor_id', 'schedule_id',
        'appointment_date', 'appointment_time', 'symptoms', 'status',
        'cancellation_reason', 'is_emergency'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'is_emergency' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($appointment) {
            $appointment->appointment_number = 'APT-' . strtoupper(uniqid());
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}