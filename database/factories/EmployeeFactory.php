<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = $this->faker->randomElement(['Koordinator', 'IT', 'Pengawas']);

        $position = match ($role) {
            'Koordinator' => $this->faker->randomElement([
                'Kepala Bagian Umum & Kepegawaian',
                'Analis SDM Aparatur Ahli Madya',
                'Koordinator Tim Pelaksana CAT',
                'Pranata Komputer Ahli Madya',
            ]),
            'IT' => $this->faker->randomElement([
                'Pranata Komputer Ahli Muda',
                'Pranata Komputer Ahli Pertama',
                'Pranata Komputer Terampil',
                'Pengelola Jaringan & Server',
                'Staff IT Infrastruktur CAT',
            ]),
            'Pengawas' => $this->faker->randomElement([
                'Analis Kepegawaian Ahli Pertama',
                'Pengawas Ruang Ujian CAT',
                'Auditor Kepegawaian',
                'Pengadministrasi Ujian',
                'Penyusun Rencana Kegiatan',
            ]),
        };

        return [
            'employee_number' => $this->faker->unique()->numerify('199#######202#####'),
            'name' => $this->faker->name(),
            'position' => $position,
            'status' => [$role],
        ];
    }

    /**
     * State specifically for Koordinator.
     */
    public function koordinator(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'Koordinator Tim Pelaksana CAT',
            'status' => ['Koordinator'],
        ]);
    }

    /**
     * State specifically for IT.
     */
    public function it(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'Pranata Komputer Ahli Pertama',
            'status' => ['IT'],
        ]);
    }

    /**
     * State specifically for Pengawas.
     */
    public function pengawas(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'Pengawas Ruang Ujian CAT',
            'status' => ['Pengawas'],
        ]);
    }
}
