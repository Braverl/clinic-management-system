<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'patient', 'is_active' => true]),
            'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            'allergies' => fake()->optional()->sentence(3),
            'medical_history' => fake()->optional()->sentence(5),
            'emergency_contact' => fake()->optional()->phoneNumber(),
            'emergency_contact_name' => fake()->optional()->name(),
        ];
    }
}