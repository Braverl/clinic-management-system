<?php

namespace App\Models;

use App\Concerns\GeneratesBusinessNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory, GeneratesBusinessNumber;

    protected static $businessNumberPrefix = 'RX';
    protected static $businessNumberColumn = 'prescription_number';

    protected $fillable = [
        'prescription_number', 'medical_record_id', 'medication_name',
        'dosage', 'frequency', 'duration', 'instructions'
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function getFullDescriptionAttribute()
    {
        return "{$this->medication_name} - {$this->dosage}, {$this->frequency} for {$this->duration}";
    }
}