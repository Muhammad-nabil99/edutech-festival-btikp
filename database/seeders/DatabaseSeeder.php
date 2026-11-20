<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\RegistrationSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Administrator BTIKP
        User::updateOrCreate(
            ['email' => 'admin@btikp.sultra.go.id'],
            [
                'name' => 'Administrator BTIKP',
                'username' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('adminbtikp2026!'),
            ]
        );

        // 2. Tiga Akun Dewan Juri (Role: judge)
        $judges = [
            [
                'name' => 'Dewan Juri 1 (Gim & Multimedia)',
                'username' => 'juri1',
                'email' => 'juri1@edutech.btikp.id',
                'role' => 'judge',
                'password' => 'Juri1#Edutech92kQ',
            ],
            [
                'name' => 'Dewan Juri 2 (Pedagogik & Kurikulum)',
                'username' => 'juri2',
                'email' => 'juri2@edutech.btikp.id',
                'role' => 'judge',
                'password' => 'Juri2#Btikp74vLm',
            ],
            [
                'name' => 'Dewan Juri 3 (Desain Visual & IT)',
                'username' => 'juri3',
                'email' => 'juri3@edutech.btikp.id',
                'role' => 'judge',
                'password' => 'Juri3#Sultra85xPz',
            ],
        ];

        foreach ($judges as $judge) {
            User::updateOrCreate(
                ['username' => $judge['username']],
                [
                    'name' => $judge['name'],
                    'email' => $judge['email'],
                    'role' => $judge['role'],
                    'password' => Hash::make($judge['password']),
                ]
            );
        }

        // 3. Pengaturan Registrasi Awal (Default: Buka)
        RegistrationSetting::firstOrCreate(
            ['id' => 1],
            [
                'mode' => 'manual',
                'is_open' => true,
                'start_time' => null,
                'end_time' => null,
                'closed_message' => 'Pendaftaran ditutup',
            ]
        );

        // 4. Data Pendaftar Awal untuk Demo/Preview
        if (Registration::count() === 0) {
            Registration::create([
                'registration_number' => 'EDUTECH-2026-0001',
                'full_name' => 'Dr. La Ode Muhammad Rizal, S.Pd., M.Pd.',
                'school_name' => 'SMAN 1 Kendari',
                'regency_city' => 'Kota Kendari',
                'email' => 'laode.rizal@sman1kendari.sch.id',
                'whatsapp_number' => '081234567890',
                'learning_category' => 'media_pembelajaran_interaktif',
                'profile_photo_path' => 'profile-photos/sample-avatar.png',
                'media_link' => 'https://canva.com/design/DAFexample/view',
                'is_curated_top10' => true,
                'curated_at' => now(),
            ]);

            Registration::create([
                'registration_number' => 'EDUTECH-2026-0002',
                'full_name' => 'Wa Ode Sitti Aminah, S.Kom.',
                'school_name' => 'SMKN 1 Baubau',
                'regency_city' => 'Kota Baubau',
                'email' => 'aminah.waode@gmail.com',
                'whatsapp_number' => '082198765432',
                'learning_category' => 'gim_edukasi',
                'profile_photo_path' => 'profile-photos/sample-avatar2.png',
                'media_link' => 'https://itch.io/sample-game-edutech',
                'is_curated_top10' => true,
                'curated_at' => now(),
            ]);
        }
    }
}
