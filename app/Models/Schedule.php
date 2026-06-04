<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'date', 'start_time', 'end_time',
        'slot_duration', 'max_patients', 'is_available'
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getAvailableSlotsAttribute()
    {
        $bookedSlots = $this->appointments()->pluck('appointment_time')->toArray();
        $slots = [];
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        
        while ($start < $end) {
            $time = date('H:i:s', $start);
            if (!in_array($time, $bookedSlots)) {
                $slots[] = $time;
            }
            $start += $this->slot_duration * 60;
        }
        
        return $slots;
    }

    public function isFullyBooked()
    {
        return $this->appointments()->count() >= $this->max_patients;
    }
}