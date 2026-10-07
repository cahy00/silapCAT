<?php

return [
    /*
    | Passing grade default skor CAT bila jenis pengadaan tidak menentukan sendiri.
    */
    'default_passing_grade' => (float) env('SCORING_DEFAULT_PASSING_GRADE', 250),

    /*
    | Batas bawah kategori skor CAT di atas passing grade (urut menurun).
    */
    'bands' => [
        ['min' => 400, 'label' => 'Sangat Memuaskan', 'icon' => '💎', 'color' => '#1e3a8a'],
        ['min' => 350, 'label' => 'Memuaskan', 'icon' => '⭐', 'color' => '#0284c7'],
        ['min' => 300, 'label' => 'Standar / Cukup', 'icon' => '✔️', 'color' => '#059669'],
    ],
];
