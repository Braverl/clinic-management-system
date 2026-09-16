<?php

namespace App\Models;

use App\Concerns\GeneratesBusinessNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, GeneratesBusinessNumber;

    protected static $businessNumberPrefix = 'PAY';
    protected static $businessNumberColumn = 'payment_number';

    protected $fillable = [
        'payment_number', 'appointment_id', 'patient_id', 'amount',
        'payment_method', 'status', 'transaction_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isRefunded()
    {
        return $this->status === 'refunded';
    }
}