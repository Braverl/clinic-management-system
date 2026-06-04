<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_number', 'medical_record_id', 'medication_name',
        'dosage', 'frequency', 'duration', 'instructions'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($prescription) {
            $prescription->prescription_number = 'RX-' . strtoupper(uniqid());
        });
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function getFullDescriptionAttribute()
    {
        return "{$this->medication_name} - {$this->dosage}, {$this->frequency} for {$this->duration}";
    }
}