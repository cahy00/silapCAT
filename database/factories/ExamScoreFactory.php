<?php

namespace Database\Factories;

use App\Models\ExamScore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamScore>
 */
class ExamScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $catScore = fake()->randomFloat(2, 200, 500); // Max 500
        $interviewScore = fake()->optional()->randomFloat(2, 40, 100);

        return [
            'employee_number' => fake()->unique()->numerify('##################'),
            'name' => fake()->name(),
            'position' => fake()->jobTitle(),
            'institution' => fake()->company(),
            'exam_type' => fake()->randomElement(['UD_I', 'UD_II', 'UPKP']),
            'exam_date' => fake()->date(),
            'cat_score' => $catScore,
            'interview_score' => $interviewScore,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
