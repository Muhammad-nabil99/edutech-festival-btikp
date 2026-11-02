<?php

namespace Database\Seeders;

use App\Models\Registration;
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
        // Akun Administrator BTIKP
        User::updateOrCreate(
            ['email' => 'admin@btikp.sultra.go.id'],
            [
                'name' => 'Administrator BTIKP',
                'password' => Hash::make('adminbtikp2026!'),
            ]
        );

        // Dummy Data Pendaftar Awal untuk Demo/Preview
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
            ]);
        }
    }
}
