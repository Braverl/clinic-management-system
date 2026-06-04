<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'payment_id', 'patient_id', 'subtotal',
        'tax', 'total', 'invoice_date', 'due_date', 'status'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            $invoice->invoice_number = 'INV-' . strtoupper(uniqid());
        });
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isOverdue()
    {
        return !$this->isPaid() && now()->gt($this->due_date);
    }
}