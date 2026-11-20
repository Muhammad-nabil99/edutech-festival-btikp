<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Top10SelectionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    /**
     * Test admin dapat memasukkan karya ke Top 10 dan membatalkannya kembali.
     */
    public function test_admin_can_toggle_registration_into_and_out_of_top10(): void
    {
        $reg = Registration::create([
            'registration_number' => 'EDUTECH-2026-0001',
            'full_name' => 'Peserta Uji Gim',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'peserta.gim@gmail.com',
            'whatsapp_number' => '6281234567890',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test-game',
            'is_curated_top10' => false,
        ]);

        // 1. Klik pertama: pilih masuk ke Top 10
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.registrations.toggle-top10', $reg));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_curated' => true,
            ]);

        $this->assertTrue($reg->fresh()->is_curated_top10);
        $this->assertNotNull($reg->fresh()->curated_at);

        // 2. Klik kedua: batalkan dari Top 10 (kembali ke unselected)
        $response2 = $this->actingAs($this->admin)
            ->postJson(route('admin.registrations.toggle-top10', $reg));

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_curated' => false,
            ]);

        $this->assertFalse($reg->fresh()->is_curated_top10);
        $this->assertNull($reg->fresh()->curated_at);
    }

    /**
     * Test kuota maksimal 10 slot per kategori ditegakkan (tidak bisa melebihi 10).
     */
    public function test_enforces_quota_of_maximum_10_works_per_category(): void
    {
        // Buat 10 karya Gim Edukasi yang sudah masuk Top 10
        for ($i = 1; $i <= 10; $i++) {
            Registration::create([
                'registration_number' => sprintf('EDUTECH-2026-%04d', $i),
                'full_name' => "Peserta Gim {$i}",
                'school_name' => 'SMAN 1 KENDARI',
                'regency_city' => 'Kota Kendari',
                'email' => "gim{$i}@example.com",
                'whatsapp_number' => "62812345678{$i}",
                'learning_category' => 'gim_edukasi',
                'profile_photo_path' => 'profile-photos/sample.jpg',
                'media_link' => 'https://itch.io/test',
                'is_curated_top10' => true,
                'curated_at' => now(),
            ]);
        }

        // Buat karya ke-11
        $eleventhReg = Registration::create([
            'registration_number' => 'EDUTECH-2026-0011',
            'full_name' => 'Peserta Gim 11',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'gim11@example.com',
            'whatsapp_number' => '6281234567899',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test',
            'is_curated_top10' => false,
        ]);

        // Coba masukkan karya ke-11 ke Gim Edukasi (harus ditolak karena sudah 10/10)
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.registrations.toggle-top10', $eleventhReg));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertStringContainsString('sudah penuh', $response->json('message'));
        $this->assertFalse($eleventhReg->fresh()->is_curated_top10);

        // Kategori Media Interaktif masih memiliki slot kosong, harus bisa dipilih
        $mediaReg = Registration::create([
            'registration_number' => 'EDUTECH-2026-0012',
            'full_name' => 'Peserta Media 1',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'media1@example.com',
            'whatsapp_number' => '6281234567898',
            'learning_category' => 'media_pembelajaran_interaktif',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://canva.com/test',
            'is_curated_top10' => false,
        ]);

        $mediaResponse = $this->actingAs($this->admin)
            ->postJson(route('admin.registrations.toggle-top10', $mediaReg));

        $mediaResponse->assertStatus(200);
        $this->assertTrue($mediaReg->fresh()->is_curated_top10);
    }
}
