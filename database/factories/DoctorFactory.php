<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'doctor', 'is_active' => true]),
            'department_id' => Department::factory(),
            'specialization' => fake()->word(),
            'qualification' => fake()->sentence(3),
            'experience_years' => fake()->numberBetween(1, 20),
            'consultation_fee' => fake()->numberBetween(500, 5000),
            'available_days' => 'monday,tuesday,wednesday,thursday,friday',
            'available_from' => '09:00:00',
            'available_to' => '17:00:00',
            'license_number' => fake()->unique()->numerify('LIC-####'),
        ];
    }
}