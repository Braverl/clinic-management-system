<?php

namespace App\Models;

use App\Concerns\GeneratesBusinessNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory, GeneratesBusinessNumber;

    protected static $businessNumberPrefix = 'APT';
    protected static $businessNumberColumn = 'appointment_number';

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

    public const STATUS_ALLOWED_TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled', 'rejected'],
        'confirmed' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
        'rejected'  => [],
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::STATUS_ALLOWED_TRANSITIONS[$this->status] ?? [], true);
    }

    public function isActive()
    {
        return in_array($this->status, ['pending', 'confirmed'], true);
    }
}