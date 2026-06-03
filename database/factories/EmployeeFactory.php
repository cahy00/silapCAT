<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_number' => $this->faker->unique()->numerify('198#######200#####'),
            'name' => $this->faker->name(),
            'position' => $this->faker->randomElement([
                'Analis Kepegawaian Ahli Pertama', 
                'Analis SDM Aparatur', 
                'Pranata Komputer Ahli Muda',
                'Auditor Kepegawaian',
                'Pengelola IT'
            ]),
            'status' => json_encode(['active' => true, 'verified' => $this->faker->boolean()]),
        ];
    }
}
