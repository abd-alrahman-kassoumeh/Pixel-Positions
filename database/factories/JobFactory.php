<?php

namespace Database\Factories;

use App\Models\Employer;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employer_id' => Employer::factory(),
            'title' => fake()->jobTitle(),
            'salary' => fake()->randomElement(['$50,000' , '$100,000' , '$90,000']),
            'location' => 'Remote',
            'schedule' => 'Full Time',
            'details' => fake()->text(),
            'requirements' => fake()->text(),
            'featured' => false
        ];
    }
}
