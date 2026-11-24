<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rubrik Standar Bab IV — EduTech Festival BTIKP
    |--------------------------------------------------------------------------
    | Total: 12 subindikator, skor maksimal 60, total bobot: 55% + 30% + 15% = 100%.
    | Skala penilaian: Likert 1 - 5.
    */

    'scale' => [
        1 => 'Sangat Kurang',
        2 => 'Kurang',
        3 => 'Cukup',
        4 => 'Baik',
        5 => 'Sangat Baik',
    ],

    'roles' => [
        1 => [
            'role' => 1,
            'name' => 'Juri 1',
            'aspect' => 'Aspek Pedagogis dan Kualitas Teknis',
            'weight' => 55,
            'max_score' => 25, // 5 subindikator x 5
            'subindicators' => [
                1 => 'Kesesuaian tujuan pembelajaran dengan fungsi dan karakteristik media/gim yang dikembangkan.',
                2 => 'Kejelasan alur penyajian informasi, materi, dan aktivitas pembelajaran.',
                3 => 'Kemudahan navigasi serta kejelasan petunjuk penggunaan media/gim.',
                4 => 'Kelancaran fungsi, responsivitas, kompatibilitas, dan kestabilan teknis produk.',
                5 => 'Kualitas interaktivitas, umpan balik, serta keterlibatan pengguna dalam proses pembelajaran.',
            ],
        ],
        2 => [
            'role' => 2,
            'name' => 'Juri 2',
            'aspect' => 'Aspek Kreativitas dan Desain Visual',
            'weight' => 30,
            'max_score' => 20, // 4 subindikator x 5
            'subindicators' => [
                1 => 'Orisinalitas ide, kreativitas, dan keunikan konsep karya.',
                2 => 'Kualitas komposisi visual, tata letak, dan konsistensi desain.',
                3 => 'Ketepatan pemilihan warna, tipografi, ilustrasi, dan elemen grafis.',
                4 => 'Kualitas desain antarmuka (UI/UX), karakter, serta daya tarik estetika yang mendukung pengalaman belajar.',
            ],
        ],
        3 => [
            'role' => 3,
            'name' => 'Juri 3',
            'aspect' => 'Aspek Presentasi dan Dampak Penerapan',
            'weight' => 15,
            'max_score' => 15, // 3 subindikator x 5
            'subindicators' => [
                1 => 'Penguasaan konsep, fitur, dan kemampuan menjelaskan keunggulan serta inovasi karya.',
                2 => 'Kemampuan mempertahankan argumen, menjawab pertanyaan juri, serta melakukan demonstrasi produk secara jujur dan meyakinkan.',
                3 => 'Potensi kebermanfaatan, kemudahan implementasi, dan keberlanjutan penggunaan karya dalam pembelajaran.',
            ],
        ],
    ],
];
