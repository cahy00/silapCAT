<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamScore;

class ExamScoreSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Ujian Dinas Tk I (UD I)
            [
                'employee_number' => '198503122010121001',
                'name' => 'Ahmad Fauzi, A.Md.',
                'position' => 'Pengatur Tingkat I',
                'institution' => 'Dinas Pendidikan',
                'exam_type' => 'UD_I',
                'exam_date' => '2026-05-15',
                'cat_score' => 380.00, // Scaled: 76.00
                'interview_score' => null,
                'notes' => 'Ujian dinas pertama berjalan lancar.',
            ],
            [
                'employee_number' => '199008202014022003',
                'name' => 'Siti Aminah, A.Md.',
                'position' => 'Pengatur',
                'institution' => 'Badan Pendapatan Daerah',
                'exam_type' => 'UD_I',
                'exam_date' => '2026-05-15',
                'cat_score' => 330.00, // Scaled: 66.00 -> Tidak Lulus
                'interview_score' => null,
                'notes' => 'Butuh peningkatan di materi TWK.',
            ],
            [
                'employee_number' => '198811052012031002',
                'name' => 'Bambang Triyono, A.Md.',
                'position' => 'Pengatur Tingkat I',
                'institution' => 'Dinas Kesehatan',
                'exam_type' => 'UD_I',
                'exam_date' => '2026-05-15',
                'cat_score' => 420.00, // Scaled: 84.00
                'interview_score' => null,
                'notes' => 'Hasil CAT sangat memuaskan.',
            ],

            // Ujian Dinas Tk II (UD II)
            [
                'employee_number' => '197904182005011002',
                'name' => 'Drs. H. Mulyadi, M.Si.',
                'position' => 'Penata Tingkat I',
                'institution' => 'Sekretariat Daerah',
                'exam_type' => 'UD_II',
                'exam_date' => '2026-05-16',
                'cat_score' => 375.00, // Scaled: 75 -> 75 * 0.6 = 45.00
                'interview_score' => 82.50, // 82.5 * 0.4 = 33.00. Total = 78.00
                'notes' => 'Makalah tentang digitalisasi arsip sangat inovatif.',
            ],
            [
                'employee_number' => '198205242008122004',
                'name' => 'Dr. Ratna Sari',
                'position' => 'Penata',
                'institution' => 'RSUD Kota',
                'exam_type' => 'UD_II',
                'exam_date' => '2026-05-16',
                'cat_score' => 300.00, // Scaled: 60 -> 60 * 0.6 = 36.00
                'interview_score' => 65.00, // 65 * 0.4 = 26.00. Total = 62.00 -> Tidak Lulus
                'notes' => 'Penguasaan materi substansi instansi kurang.',
            ],

            // UPKP (Ujian Penyesuaian Kenaikan Pangkat)
            [
                'employee_number' => '199201302018012001',
                'name' => 'Rini Handayani, S.Kom.',
                'position' => 'Pengatur',
                'institution' => 'Dinas Kominfo',
                'exam_type' => 'UPKP',
                'exam_date' => '2026-05-17',
                'cat_score' => 410.00, // Scaled: 82.00 -> 82 * 0.5 = 41.00
                'interview_score' => 88.00, // 88 * 0.5 = 44.00. Total = 85.00
                'notes' => 'Sesuai dengan ijazah S1 Teknik Informatika.',
            ],
            [
                'employee_number' => '199507112020031005',
                'name' => 'Dodi Hermawan, S.E.',
                'position' => 'Pengatur Muda',
                'institution' => 'Badan Kepegawaian Daerah',
                'exam_type' => 'UPKP',
                'exam_date' => '2026-05-17',
                'cat_score' => 350.00, // Scaled: 70.00 -> 70 * 0.5 = 35.00
                'interview_score' => 70.00, // 70 * 0.5 = 35.00. Total = 70.00 -> Lulus Pas-pasan
                'notes' => 'Penyesuaian pangkat ke golongan III/a.',
            ],
            [
                'employee_number' => '199312152019022002',
                'name' => 'Fani Rahmawati, S.E.',
                'position' => 'Pengatur Muda',
                'institution' => 'Dinas Pekerjaan Umum',
                'exam_type' => 'UPKP',
                'exam_date' => '2026-05-17',
                'cat_score' => 320.00, // Scaled: 64.00 -> 64 * 0.5 = 32.00
                'interview_score' => 60.00, // 60 * 0.5 = 30.00. Total = 62.00 -> Tidak Lulus
                'notes' => 'Nilai wawancara belum mencapai standar kompetensi.',
            ],
        ];

        foreach ($data as $row) {
            ExamScore::create($row);
        }
    }
}
