<?php

namespace Tests\Feature;

use App\Models\RegistrationSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationScheduleTest extends TestCase
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
     * Test admin dapat mengubah pengaturan status registrasi manual (On / Off).
     */
    public function test_admin_can_update_registration_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.registration'), [
                'mode' => 'manual',
                'is_open' => '0',
                'closed_message' => 'Pendaftaran resmi ditutup oleh panitia.',
            ]);

        $response->assertSessionHas('status');

        $setting = RegistrationSetting::current();
        $this->assertEquals('manual', $setting->mode);
        $this->assertFalse($setting->isOpen());
        $this->assertEquals('Pendaftaran resmi ditutup oleh panitia.', $setting->getStatusMessage());
    }

    /**
     * Test saat pendaftaran ditutup secara manual, form submit ditolak dan pesan penutupan muncul.
     */
    public function test_submission_blocked_when_registration_is_manually_closed(): void
    {
        Storage::fake('public');

        $setting = RegistrationSetting::current();
        $setting->update([
            'mode' => 'manual',
            'is_open' => false,
            'closed_message' => 'Pendaftaran ditutup',
        ]);

        // Cek halaman publik menampilkan status ditutup
        $viewResponse = $this->get('/');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Pendaftaran ditutup');

        // Coba kirim form pendaftaran
        $photo = UploadedFile::fake()->image('avatar.jpg', 300, 300);
        $payload = [
            'full_name' => 'Budi Santoso',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'budi.santoso@gmail.com',
            'whatsapp_number' => '081234567890',
            'learning_category' => 'gim_edukasi',
            'profile_photo' => $photo,
            'media_link' => 'https://itch.io/sample',
        ];

        $postResponse = $this->post('/register', $payload);
        $postResponse->assertSessionHasErrors('registration');
    }

    /**
     * Test mode terjadwal (scheduled) saat tanggal belum mulai.
     */
    public function test_scheduled_registration_not_yet_open(): void
    {
        $setting = RegistrationSetting::current();
        $setting->update([
            'mode' => 'scheduled',
            'start_time' => now()->addDays(2),
            'end_time' => now()->addDays(10),
        ]);

        $this->assertFalse($setting->isOpen());
        $this->assertEquals('not_yet_open', $setting->getStatus());
        $this->assertNotNull($setting->getCountdownTarget());

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Pendaftaran dibuka pada tanggal');
    }
}
