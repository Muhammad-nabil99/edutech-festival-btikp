<?php

namespace Tests\Feature;

use App\Models\JudgeScore;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JudgeFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $judge;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator BTIKP',
            'username' => 'admin_btikp',
            'email' => 'admin@btikp.sultra.go.id',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $this->judge = User::create([
            'name' => 'Dewan Juri 1',
            'username' => 'juri1_sultra',
            'email' => 'juri1@edutech.btikp.id',
            'role' => 'judge',
            'password' => Hash::make('password123'),
        ]);
    }

    /**
     * Test juri dapat login menggunakan USERNAME maupun EMAIL.
     */
    public function test_judge_can_login_with_username_or_email(): void
    {
        // 1. Login menggunakan username
        $responseUsername = $this->post(route('admin.login.post'), [
            'login' => 'juri1_sultra',
            'password' => 'password123',
        ]);

        $responseUsername->assertRedirect(route('judge.dashboard'));
        $this->assertAuthenticatedAs($this->judge);

        // Logout
        $this->post(route('admin.logout'));

        // 2. Login menggunakan email
        $responseEmail = $this->post(route('admin.login.post'), [
            'login' => 'juri1@edutech.btikp.id',
            'password' => 'password123',
        ]);

        $responseEmail->assertRedirect(route('judge.dashboard'));
        $this->assertAuthenticatedAs($this->judge);
    }

    /**
     * Test pembatasan akses hak: Juri tidak boleh akses dashboard admin, Admin diarahkan ke dashboard admin.
     */
    public function test_role_access_boundaries_between_admin_and_judge(): void
    {
        // Juri mencoba akses dashboard admin
        $judgeToAdmin = $this->actingAs($this->judge)->get(route('admin.dashboard'));
        $judgeToAdmin->assertRedirect(route('judge.dashboard'));

        // Admin mencoba akses dashboard juri
        $adminToJudge = $this->actingAs($this->admin)->get(route('judge.dashboard'));
        $adminToJudge->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test dewan juri hanya dapat melihat karya yang masuk nominasi Top 10.
     */
    public function test_judge_dashboard_only_displays_top10_works(): void
    {
        // Karya 1: Masuk Top 10
        $top10Work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0001',
            'full_name' => 'Karya Unggulan Top 10',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'top10@example.com',
            'whatsapp_number' => '628111111111',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test',
            'is_curated_top10' => true,
            'curated_at' => now(),
        ]);

        // Karya 2: Belum masuk Top 10
        $regularWork = Registration::create([
            'registration_number' => 'EDUTECH-2026-0002',
            'full_name' => 'Karya Pendaftar Reguler',
            'school_name' => 'SMKN 1 BAUBAU',
            'regency_city' => 'Kota Baubau',
            'email' => 'regular@example.com',
            'whatsapp_number' => '628222222222',
            'learning_category' => 'media_pembelajaran_interaktif',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://canva.com/test',
            'is_curated_top10' => false,
        ]);

        $response = $this->actingAs($this->judge)->get(route('judge.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Karya Unggulan Top 10');
        $response->assertDontSee('Karya Pendaftar Reguler');
    }

    /**
     * Test dewan juri dapat memberikan nilai dengan bobot 4 aspek rubrik Bab IV.
     */
    public function test_judge_can_submit_score_with_weighted_aspects(): void
    {
        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0001',
            'full_name' => 'Karya Media Interaktif Terpilih',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'peserta@example.com',
            'whatsapp_number' => '6281234567890',
            'learning_category' => 'media_pembelajaran_interaktif',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://canva.com/view',
            'is_curated_top10' => true,
        ]);

        // Nilai Aspek:
        // Pedagogis (30%) = 90 -> 27.0
        // Kreativitas (30%) = 80 -> 24.0
        // Teknis (25%) = 85 -> 21.25
        // Presentasi (15%) = 90 -> 13.5
        // Total = 27.0 + 24.0 + 21.25 + 13.5 = 85.75
        $payload = [
            'pedagogical_score' => 90,
            'creativity_score' => 80,
            'technical_score' => 85,
            'presentation_score' => 90,
            'notes' => 'Karya sangat inovatif dan materi kurikulum terstruktur rapi.',
        ];

        $response = $this->actingAs($this->judge)
            ->postJson(route('judge.works.score', $work), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $score = JudgeScore::where('user_id', $this->judge->id)
            ->where('registration_id', $work->id)
            ->first();

        $this->assertNotNull($score);
        $this->assertEquals(85.75, $score->total_score);
        $this->assertEquals('Karya sangat inovatif dan materi kurikulum terstruktur rapi.', $score->notes);
    }

    /**
     * Test juri dapat memperbarui profil dan kredensialnya sendiri secara mandiri.
     */
    public function test_judge_can_update_own_credentials(): void
    {
        $response = $this->actingAs($this->judge)
            ->put(route('judge.profile.update'), [
                'name' => 'Dr. H. Juri Baru, M.Pd.',
                'username' => 'juri1_updated',
                'email' => 'juri1.updated@edutech.btikp.id',
                'password' => 'NewSecretPassword123!',
                'password_confirmation' => 'NewSecretPassword123!',
            ]);

        $response->assertSessionHas('status');

        $this->judge->refresh();
        $this->assertEquals('Dr. H. Juri Baru, M.Pd.', $this->judge->name);
        $this->assertEquals('juri1_updated', $this->judge->username);
        $this->assertEquals('juri1.updated@edutech.btikp.id', $this->judge->email);
        $this->assertTrue(Hash::check('NewSecretPassword123!', $this->judge->password));
    }
}
