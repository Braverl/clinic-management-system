<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::all();
        $patients = Patient::all();
        
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        
        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->subDays(rand(0, 30))->addDays(rand(0, 20));
            
            Appointment::create([
                'appointment_number' => 'APT-' . strtoupper(uniqid()),
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctors->random()->id,
                'appointment_date' => $date,
                'appointment_time' => Carbon::createFromTime(rand(9, 16), rand(0, 1) * 30),
                'symptoms' => 'Sample symptoms for testing.',
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}