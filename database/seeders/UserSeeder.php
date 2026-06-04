<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@clinicsystem.com',
            'phone' => '1234567890',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        
        // Create Doctors
        $departments = Department::all();
        
        $doctorNames = [
            ['name' => 'Dr. John Smith', 'email' => 'john.smith@clinic.com', 'specialization' => 'Cardiologist'],
            ['name' => 'Dr. Sarah Johnson', 'email' => 'sarah.johnson@clinic.com', 'specialization' => 'Neurologist'],
            ['name' => 'Dr. Michael Brown', 'email' => 'michael.brown@clinic.com', 'specialization' => 'Pediatrician'],
            ['name' => 'Dr. Emily Davis', 'email' => 'emily.davis@clinic.com', 'specialization' => 'Orthopedic'],
            ['name' => 'Dr. David Wilson', 'email' => 'david.wilson@clinic.com', 'specialization' => 'Dermatologist'],
        ];
        
        foreach ($doctorNames as $index => $docData) {
            $user = User::create([
                'name' => $docData['name'],
                'email' => $docData['email'],
                'phone' => '987654321' . $index,
                'role' => 'doctor',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            
            Doctor::create([
                'user_id' => $user->id,
                'department_id' => $departments[$index % $departments->count()]->id,
                'specialization' => $docData['specialization'],
                'qualification' => 'MD, PhD',
                'experience_years' => rand(5, 20),
                'consultation_fee' => rand(50, 200),
                'license_number' => 'LIC' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'bio' => 'Experienced and dedicated doctor committed to providing the best healthcare.',
            ]);
        }
        
        // Create Sample Patients
        $patientNames = [
            'Alice Wonderland', 'Bob Marley', 'Charlie Chaplin', 'Diana Prince',
            'Ethan Hunt', 'Fiona Gallagher', 'George Clooney', 'Hannah Montana'
        ];
        
        foreach ($patientNames as $index => $name) {
            $user = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'phone' => '555' . str_pad($index, 7, '0', STR_PAD_LEFT),
                'role' => 'patient',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            
            Patient::create([
                'user_id' => $user->id,
                'blood_group' => ['A+', 'B+', 'O+', 'AB+'][rand(0, 3)],
                'allergies' => rand(0, 1) ? 'None' : 'Penicillin',
                'emergency_contact' => '5551112222',
                'emergency_contact_name' => 'Emergency Contact',
            ]);
        }
    }
}