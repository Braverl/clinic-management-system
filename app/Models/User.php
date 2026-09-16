<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   // Add 'is_active' to fillable if not already there
protected $fillable = [
    'name', 'email', 'phone', 'address', 'dob', 'gender',
    'role', 'profile_image', 'is_active', 'password',
    'theme', 'notifications_enabled'
];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'is_active' => 'boolean',
        'notifications_enabled' => 'boolean',
    ];

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function conversationsAsUserOne()
    {
        return $this->hasMany(Conversation::class, 'user_one_id');
    }

    public function conversationsAsUserTwo()
    {
        return $this->hasMany(Conversation::class, 'user_two_id');
    }

    public function conversations()
    {
        return $this->hasManyThrough(Conversation::class, Conversation::class, 'user_one_id', 'user_two_id');
    }

    public function allConversations()
    {
        return Conversation::where('user_one_id', $this->id)
            ->orWhere('user_two_id', $this->id)
            ->with('userOne', 'userTwo')
            ->orderBy('last_message_at', 'desc')
            ->get();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isPatient()
    {
        return $this->role === 'patient';
    }
}

