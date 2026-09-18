<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Koordinators (10 petugas)
        $koordinators = [
            'Dr. Hendra Gunawan, S.Kom., M.Si.',
            'Budi Santoso, S.Sos., M.AP.',
            'Ir. Agus Wijaya, M.Kom.',
            'Dra. Siti Rahmawati, M.Pd.',
            'Ahmad Fauzi, S.E., M.M.',
            'Drs. Eko Prasetyo, M.Si.',
            'Rina Kartikasari, S.H., M.H.',
            'Bambang Sugiarto, S.Kom., M.T.',
            'Dewi Anggraini, S.IP., M.Si.',
            'Herman Syahputra, S.T., M.Eng.',
        ];

        foreach ($koordinators as $idx => $name) {
            Employee::firstOrCreate(
                ['name' => $name],
                [
                    'employee_number' => sprintf('197%05d200%05d', $idx + 10, $idx + 100),
                    'position' => 'Koordinator Tim Pelaksana CAT',
                    'status' => ['Koordinator'],
                ]
            );
        }

        // 2. Tim IT (15 petugas)
        $itStaffs = [
            'Rizky Pratama, S.Kom.',
            'Fajar Nugroho, S.T.',
            'Bayu Setiawan, S.Kom.',
            'Dimas Ardiansyah, S.Kom.',
            'Ilham Maulana, S.T.',
            'Aditya Kurniawan, S.Kom.',
            'Yusuf Firmansyah, S.Kom.',
            'Arif Wicaksono, S.T.',
            'Galih Permana, S.Kom.',
            'Rio Bagus Saputra, S.Kom.',
            'Wahyu Hidayat, S.T.',
            'Taufik Hidayat, S.Kom.',
            'Rudi Hartono, S.Kom.',
            'Doni Prasetya, S.T.',
            'Kevin Sanjaya, S.Kom.',
        ];

        foreach ($itStaffs as $idx => $name) {
            Employee::firstOrCreate(
                ['name' => $name],
                [
                    'employee_number' => sprintf('199%05d202%05d', $idx + 20, $idx + 200),
                    'position' => 'Pranata Komputer / Tim IT CAT',
                    'status' => ['IT'],
                ]
            );
        }

        // 3. Pengawas Ujian (25 petugas)
        $pengawasStaffs = [
            'Anisa Putri, S.Pd.',
            'Nurul Hidayah, S.Sos.',
            'Maya Indah Sari, S.E.',
            'Tri Wahyuni, S.AP.',
            'Dian Lestari, S.Pd.',
            'Ratna Sari, S.IP.',
            'Fitri Handayani, S.Pd.',
            'Mega Utami, S.E.',
            'Sri Rahayu, S.Pd.',
            'Wulandari, S.Sos.',
            'Nita Anggraeni, S.AP.',
            'Intan Permatasari, S.Pd.',
            'Lina Marlina, S.IP.',
            'Devi Novitasari, S.E.',
            'Putri Ayu Ningtyas, S.Pd.',
            'Rini Sulistiawati, S.AP.',
            'Tari Puspitasari, S.IP.',
            'Irma Suryani, S.Pd.',
            'Citra Dewi, S.E.',
            'Yulia Rahman, S.Sos.',
            'Hesti Pratiwi, S.AP.',
            'Gita Savitri, S.Pd.',
            'Siska Yuliana, S.IP.',
            'Nadia Safitri, S.E.',
            'Melati Kusuma, S.Pd.',
        ];

        foreach ($pengawasStaffs as $idx => $name) {
            Employee::firstOrCreate(
                ['name' => $name],
                [
                    'employee_number' => sprintf('199%05d202%05d', $idx + 50, $idx + 300),
                    'position' => 'Pengawas Ruang Ujian CAT',
                    'status' => ['Pengawas'],
                ]
            );
        }
    }
}
