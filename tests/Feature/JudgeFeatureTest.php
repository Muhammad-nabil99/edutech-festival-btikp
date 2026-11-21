<?php

namespace Tests\Feature;

use App\Models\JudgeScore;
use App\Models\JudgeSuggestion;
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
    private User $judge2;
    private User $judge3;

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

        $this->judge2 = User::create([
            'name' => 'Dewan Juri 2',
            'username' => 'juri2_sultra',
            'email' => 'juri2@edutech.btikp.id',
            'role' => 'judge',
            'password' => Hash::make('password123'),
        ]);

        $this->judge3 = User::create([
            'name' => 'Dewan Juri 3',
            'username' => 'juri3_sultra',
            'email' => 'juri3@edutech.btikp.id',
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
        $judgeToAdmin = $this->actingAs($this->judge)->get(route('admin.dashboard'));
        $judgeToAdmin->assertRedirect(route('judge.dashboard'));

        $adminToJudge = $this->actingAs($this->admin)->get(route('judge.dashboard'));
        $adminToJudge->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test dewan juri memiliki 3 tab pemisah di dashboard.
     */
    public function test_judge_dashboard_tabs_structure(): void
    {
        // Karya 1: Finalis (Top 10 disetujui Admin)
        $top10Work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0001',
            'full_name' => 'Karya Unggulan Finalis',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'finalis@example.com',
            'whatsapp_number' => '628111111111',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test',
            'is_curated_top10' => true,
            'curated_at' => now(),
        ]);

        // Karya 2: Pendaftar Reguler
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

        // Juri menyarankan karya reguler
        JudgeSuggestion::create([
            'user_id' => $this->judge->id,
            'registration_id' => $regularWork->id,
        ]);

        // 1. Tab Finalis (Default): hanya menampilkan karya yang disetujui Admin
        $responseFinalists = $this->actingAs($this->judge)->get(route('judge.dashboard', ['tab' => 'finalists']));
        $responseFinalists->assertStatus(200);
        $responseFinalists->assertSee('Karya Unggulan Finalis');

        // 2. Tab Semua Peserta: menampilkan seluruh peserta
        $responseAll = $this->actingAs($this->judge)->get(route('judge.dashboard', ['tab' => 'all']));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Karya Unggulan Finalis');
        $responseAll->assertSee('Karya Pendaftar Reguler');

        // 3. Tab Saran Saya: menampilkan karya yang disarankan oleh juri ini
        $responseMySuggestions = $this->actingAs($this->judge)->get(route('judge.dashboard', ['tab' => 'my_suggestions']));
        $responseMySuggestions->assertStatus(200);
        $responseMySuggestions->assertSee('Karya Pendaftar Reguler');
    }

    /**
     * Test dewan juri wajib memilih minimal 1 alasan rekomendasi saat menyarankan Top 10.
     */
    public function test_judge_must_select_at_least_one_reason_to_suggest(): void
    {
        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0009',
            'full_name' => 'Karya Uji Alasan',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'ujialasan@example.com',
            'whatsapp_number' => '628123456780',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test-game',
            'is_curated_top10' => false,
        ]);

        // Coba sarankan tanpa checklist alasan
        $res = $this->actingAs($this->judge)
            ->postJson(route('judge.works.toggle-suggest', $work), [
                'reasons' => [],
            ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['reasons']);

        $this->assertDatabaseMissing('judge_suggestions', [
            'user_id' => $this->judge->id,
            'registration_id' => $work->id,
        ]);
    }

    /**
     * Test dewan juri dapat menyarankan dengan checklist alasan, memperbarui alasan, dan menarik saran.
     */
    public function test_judge_can_toggle_suggestion_for_top10(): void
    {
        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0010',
            'full_name' => 'Karya Calon Top 10',
            'school_name' => 'SMAN 2 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'calon@example.com',
            'whatsapp_number' => '628123456789',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test-game',
            'is_curated_top10' => false,
        ]);

        $initialReasons = [
            'Karya layak secara teknis dan berfungsi dengan baik (tanpa bug/crash)',
            'Ide karya orisinal',
        ];

        // 1. Juri 1 memberi saran dengan checklist alasan
        $res1 = $this->actingAs($this->judge)
            ->postJson(route('judge.works.toggle-suggest', $work), [
                'reasons' => $initialReasons,
            ]);

        $res1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_suggested' => true,
                'suggestions_count' => 1,
            ]);

        $this->assertDatabaseHas('judge_suggestions', [
            'user_id' => $this->judge->id,
            'registration_id' => $work->id,
        ]);

        $suggestion = JudgeSuggestion::where('user_id', $this->judge->id)
            ->where('registration_id', $work->id)
            ->first();
        $this->assertEquals($initialReasons, $suggestion->reasons);

        // 2. Juri 1 memperbarui alasan checklist
        $updatedReasons = [
            'Karya layak secara teknis dan berfungsi dengan baik (tanpa bug/crash)',
            'Ide karya orisinal',
            'Materi pembelajaran benar dan akurat',
        ];

        $resUpdate = $this->actingAs($this->judge)
            ->postJson(route('judge.works.toggle-suggest', $work), [
                'reasons' => $updatedReasons,
            ]);

        $resUpdate->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_suggested' => true,
            ]);

        $suggestion->refresh();
        $this->assertEquals($updatedReasons, $suggestion->reasons);

        // 3. Juri 1 membatalkan / menarik saran
        $resWithdraw = $this->actingAs($this->judge)
            ->postJson(route('judge.works.toggle-suggest', $work), [
                'action' => 'withdraw',
            ]);

        $resWithdraw->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_suggested' => false,
                'suggestions_count' => 0,
            ]);

        $this->assertDatabaseMissing('judge_suggestions', [
            'user_id' => $this->judge->id,
            'registration_id' => $work->id,
        ]);
    }

    /**
     * Test kuota saran juri dibatasi maksimal 10 karya per kategori.
     */
    public function test_judge_suggestion_quota_enforced_at_10_per_category(): void
    {
        // Buat 10 karya gim edukasi dan sarankan semuanya
        for ($i = 1; $i <= 10; $i++) {
            $w = Registration::create([
                'registration_number' => sprintf('EDUTECH-2026-%04d', $i),
                'full_name' => "Peserta Gim {$i}",
                'school_name' => 'SMA CONTOH',
                'regency_city' => 'Kota Kendari',
                'email' => "gim{$i}@example.com",
                'whatsapp_number' => '62812345678',
                'learning_category' => 'gim_edukasi',
                'profile_photo_path' => 'profile-photos/sample.jpg',
                'media_link' => 'https://itch.io/sample',
            ]);

            JudgeSuggestion::create([
                'user_id' => $this->judge->id,
                'registration_id' => $w->id,
                'reasons' => ['Ide karya orisinal'],
            ]);
        }

        // Coba sarankan karya gim ke-11
        $work11 = Registration::create([
            'registration_number' => 'EDUTECH-2026-0011',
            'full_name' => 'Peserta Gim 11',
            'school_name' => 'SMA CONTOH',
            'regency_city' => 'Kota Kendari',
            'email' => 'gim11@example.com',
            'whatsapp_number' => '62812345678',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/sample',
        ]);

        $response = $this->actingAs($this->judge)
            ->postJson(route('judge.works.toggle-suggest', $work11), [
                'reasons' => ['Ide karya orisinal'],
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseMissing('judge_suggestions', [
            'user_id' => $this->judge->id,
            'registration_id' => $work11->id,
        ]);
    }

    /**
     * Test dewan juri hanya dapat memberi nilai pada karya yang masuk Top 10 (Finalis).
     */
    public function test_judge_cannot_score_non_finalist_work(): void
    {
        $nonFinalist = Registration::create([
            'registration_number' => 'EDUTECH-2026-0099',
            'full_name' => 'Karya Non Finalis',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'bukanfinalis@example.com',
            'whatsapp_number' => '6281234567890',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://canva.com/view',
            'is_curated_top10' => false,
        ]);

        $payload = [
            'pedagogical_score' => 90,
            'creativity_score' => 80,
            'technical_score' => 85,
            'presentation_score' => 90,
        ];

        $response = $this->actingAs($this->judge)
            ->postJson(route('judge.works.score', $nonFinalist), $payload);

        $response->assertStatus(403);
    }

    /**
     * Test dewan juri dapat memberikan nilai dengan bobot 4 aspek rubrik Bab IV pada Finalis.
     */
    public function test_judge_can_submit_score_with_weighted_aspects_on_finalist(): void
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
     * Test jika admin membatalkan karya dari Top 10, nilai juri tidak terhapus dari database.
     */
    public function test_unselecting_top10_retains_scores_in_database(): void
    {
        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0001',
            'full_name' => 'Karya Media Interaktif',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'peserta@example.com',
            'whatsapp_number' => '6281234567890',
            'learning_category' => 'media_pembelajaran_interaktif',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://canva.com/view',
            'is_curated_top10' => true,
        ]);

        // Juri memberi nilai
        JudgeScore::create([
            'user_id' => $this->judge->id,
            'registration_id' => $work->id,
            'pedagogical_score' => 80,
            'creativity_score' => 80,
            'technical_score' => 80,
            'presentation_score' => 80,
            'total_score' => 80.0,
        ]);

        // Admin uncurate
        $this->actingAs($this->admin)
            ->postJson(route('admin.registrations.toggle-top10', $work));

        $work->refresh();
        $this->assertFalse($work->is_curated_top10);

        // Nilai tetap ada di database
        $this->assertDatabaseHas('judge_scores', [
            'user_id' => $this->judge->id,
            'registration_id' => $work->id,
            'total_score' => 80.0,
        ]);
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

    /**
     * Test tampilan dashboard admin dan juri memuat fitur view switcher dan modal saran dengan checklist.
     */
    public function test_dashboards_render_checklist_reasons_and_view_switcher(): void
    {
        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0099',
            'full_name' => 'Karya Media Interaktif Sultra',
            'school_name' => 'SMAN 3 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'peserta99@example.com',
            'whatsapp_number' => '6281234567899',
            'learning_category' => 'media_pembelajaran_interaktif',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://canva.com/view99',
            'is_curated_top10' => false,
        ]);

        JudgeSuggestion::create([
            'user_id' => $this->judge->id,
            'registration_id' => $work->id,
            'reasons' => [
                'Karya layak secara teknis dan berfungsi dengan baik (tanpa bug/crash)',
                'Ide karya orisinal',
            ],
        ]);

        // 1. Dashboard Juri: Memuat view switcher dan checklist alasan
        $judgeDash = $this->actingAs($this->judge)->get(route('judge.dashboard', ['tab' => 'all']));
        $judgeDash->assertStatus(200);
        $judgeDash->assertSee('viewMode');
        $judgeDash->assertSee('reasonsList');
        $judgeDash->assertSee('openSuggestionModal');
        $judgeDash->assertSee('Karya layak secara teknis dan berfungsi dengan baik');

        // 2. Dashboard Admin: Memuat data saran juri dan tampilan checklist alasan
        $adminDash = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminDash->assertStatus(200);
        $adminDash->assertSee('Alasan Rekomendasi Top 10');
        $adminDash->assertSee('Karya layak secara teknis dan berfungsi dengan baik');
    }
}
