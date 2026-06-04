<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Cardiology', 'description' => 'Heart and cardiovascular diseases'],
            ['name' => 'Neurology', 'description' => 'Brain and nervous system disorders'],
            ['name' => 'Pediatrics', 'description' => 'Children\'s health and diseases'],
            ['name' => 'Orthopedics', 'description' => 'Bones, joints and muscles'],
            ['name' => 'Dermatology', 'description' => 'Skin, hair and nail conditions'],
            ['name' => 'Ophthalmology', 'description' => 'Eye care and vision'],
            ['name' => 'ENT', 'description' => 'Ear, nose and throat'],
            ['name' => 'Gynecology', 'description' => 'Women\'s reproductive health'],
            ['name' => 'Psychiatry', 'description' => 'Mental health and disorders'],
            ['name' => 'Dentistry', 'description' => 'Teeth and oral health'],
        ];
        
        foreach ($departments as $dept) {
            Department::create([
                'name' => $dept['name'],
                'slug' => Str::slug($dept['name']),
                'description' => $dept['description'],
                'is_active' => true,
            ]);
        }
    }
}