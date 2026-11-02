<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test halaman pendaftaran dapat diakses dengan sukses.
     */
    public function test_registration_form_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Formulir Pendaftaran Karya Digital EduTech Festival');
        $response->assertSee('Nama Lengkap');
        $response->assertSee('Asal Sekolah');
        $response->assertSee('Kota Kendari');
        $response->assertSee('Gim Edukasi');
        $response->assertSee('Media Pembelajaran Interaktif');
    }

    /**
     * Test validasi formulir saat data tidak lengkap.
     */
    public function test_registration_fails_validation_with_missing_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors([
            'full_name',
            'school_name',
            'regency_city',
            'email',
            'whatsapp_number',
            'learning_category',
            'profile_photo',
            'media_link',
        ]);
    }

    /**
     * Test pendaftaran berhasil dengan data valid.
     */
    public function test_participant_can_submit_valid_registration(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $payload = [
            'full_name' => 'Ahmad Fauzan Pratama, S.Pd.',
            'school_name' => 'SMAN 4 Kendari',
            'regency_city' => 'Kota Kendari',
            'email' => 'ahmad.fauzan@gmail.com',
            'whatsapp_number' => '085211223344',
            'learning_category' => 'gim_edukasi',
            'profile_photo' => $photo,
            'media_link' => 'https://drive.google.com/file/d/sample-id/view',
        ];

        $response = $this->post('/register', $payload);

        $registration = Registration::where('email', 'ahmad.fauzan@gmail.com')->first();
        $this->assertNotNull($registration);
        $this->assertStringStartsWith('EDUTECH-' . date('Y') . '-', $registration->registration_number);
        $this->assertEquals('6285211223344', $registration->whatsapp_number);

        Storage::disk('public')->assertExists($registration->profile_photo_path);

        $response->assertRedirect(route('registration.success', ['registration_number' => $registration->registration_number]));

        // Cek halaman sukses
        $successResponse = $this->get(route('registration.success', ['registration_number' => $registration->registration_number]));
        $successResponse->assertStatus(200);
        $successResponse->assertSee($registration->registration_number);
        $successResponse->assertSee('Ahmad Fauzan Pratama, S.Pd.');

        // Cek foto profil dapat diakses melalui route
        $photoResponse = $this->get(route('registration.photo', $registration->id));
        $photoResponse->assertStatus(200);
        $photoResponse->assertHeader('Content-Type', 'image/jpeg');
    }

    /**
     * Test akses dashboard admin membutuhkan login.
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test admin yang sudah login dapat melihat dashboard dan statistik.
     */
    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Data Pendaftar EduTech Festival');
        $response->assertSee('Ekspor CSV / Excel');
    }

    /**
     * Test export CSV pendaftar oleh admin.
     */
    public function test_admin_can_export_registrations_csv(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/admin/registrations/export');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }
}
