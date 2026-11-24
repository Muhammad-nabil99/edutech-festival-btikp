<?php

namespace Tests\Feature;

use App\Models\JudgeScore;
use App\Models\JudgeSuggestion;
use App\Models\Registration;
use App\Models\RegistrationSetting;
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
     * Test dewan juri tidak dibatasi kuota saat memberikan saran (dapat menyarankan lebih dari 10 karya).
     */
    public function test_judge_can_suggest_unlimited_works_without_category_quota_restriction(): void
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

        // Juri menyarankan karya gim ke-11 (tidak dibatasi kuota 10)
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

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_suggested' => true,
            ]);

        $this->assertDatabaseHas('judge_suggestions', [
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
        $this->assertEquals(48.25, $score->total_score);
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

    /**
     * Test Juri 2 dan Juri 3 dapat membuka dashboard dengan benar tanpa kode JS bocor ke HTML
     * saat Juri 1 telah memberikan saran dengan checklist alasan.
     */
    public function test_judge2_and_judge3_render_dashboard_cleanly_when_other_judges_suggested_with_reasons(): void
    {
        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0100',
            'full_name' => 'Karya Gim Edukasi Keren',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'peserta100@example.com',
            'whatsapp_number' => '6281234567100',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://drive.google.com/sample',
            'is_curated_top10' => false,
        ]);

        JudgeSuggestion::create([
            'user_id' => $this->judge->id,
            'registration_id' => $work->id,
            'reasons' => [
                'Karya layak secara teknis dan berfungsi dengan baik (tanpa bug/crash)',
                'Ide karya orisinal',
                'Materi pembelajaran benar dan akurat',
            ],
        ]);

        // Juri 2 mengakses dashboard
        $resJuri2 = $this->actingAs($this->judge2)->get(route('judge.dashboard', ['tab' => 'all']));
        $resJuri2->assertStatus(200);
        $resJuri2->assertDontSee('x-data="{');
        $resJuri2->assertSee('x-data="judgeDashboard()"', false);
        $resJuri2->assertSee('Dewan Juri 2');

        // Juri 3 mengakses dashboard
        $resJuri3 = $this->actingAs($this->judge3)->get(route('judge.dashboard', ['tab' => 'all']));
        $resJuri3->assertStatus(200);
        $resJuri3->assertDontSee('x-data="{');
        $resJuri3->assertSee('x-data="judgeDashboard()"', false);
        $resJuri3->assertSee('Dewan Juri 3');
    }

    /**
     * Test kode juri J1, J2, J3 selalu konsisten dan tidak tertukar,
     * serta kedua ikon juri muncul dengan benar pada card peserta saat disarankan.
     */
    public function test_judge_codes_are_correct_and_not_confused_between_judge1_and_judge2(): void
    {
        $this->assertEquals('J1', $this->judge->judge_code);
        $this->assertEquals('J2', $this->judge2->judge_code);
        $this->assertEquals('J3', $this->judge3->judge_code);

        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0200',
            'full_name' => 'Karya Bersama Dua Juri',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'peserta200@example.com',
            'whatsapp_number' => '6281234567200',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://drive.google.com/sample',
            'is_curated_top10' => false,
        ]);

        // Juri 1 menyarankan
        $this->actingAs($this->judge)->postJson(route('judge.works.toggle-suggest', $work->id), [
            'reasons' => ['Ide karya orisinal'],
        ])->assertJson([
            'success' => true,
            'is_suggested' => true,
        ]);

        // Juri 2 menyarankan
        $resJuri2 = $this->actingAs($this->judge2)->postJson(route('judge.works.toggle-suggest', $work->id), [
            'reasons' => ['Materi pembelajaran benar dan akurat'],
        ]);
        $resJuri2->assertJson([
            'success' => true,
            'is_suggested' => true,
        ]);

        // Verifikasi response suggestions memuat kedua juri: J1 dan J2
        $suggestions = $resJuri2->json('suggestions');
        $this->assertCount(2, $suggestions);
        $codes = array_column($suggestions, 'judge_code');
        $this->assertContains('J1', $codes);
        $this->assertContains('J2', $codes);
    }

    /**
     * Test pembatasan kategori juri di sisi backend dan query:
     * Juri GIM hanya melihat dan menilai GIM, Juri MPI hanya melihat dan menilai MPI.
     */
    public function test_judge_category_enforcement_in_backend_query_and_actions(): void
    {
        // Pastikan juri 1 berkategori gim_edukasi dan juri 3 berkategori media_pembelajaran_interaktif
        $this->judge->update(['category' => 'gim_edukasi']);
        $this->judge3->update(['category' => 'media_pembelajaran_interaktif']);

        $gimWork = Registration::create([
            'registration_number' => 'EDUTECH-2026-0301',
            'full_name' => 'Peserta Karya Gim Edukasi',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'gim301@example.com',
            'whatsapp_number' => '6281234567301',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://drive.google.com/sample',
            'is_curated_top10' => true,
        ]);

        $mpiWork = Registration::create([
            'registration_number' => 'EDUTECH-2026-0302',
            'full_name' => 'Peserta Karya MPI Interaktif',
            'school_name' => 'SMAN 2 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'mpi302@example.com',
            'whatsapp_number' => '6281234567302',
            'learning_category' => 'media_pembelajaran_interaktif',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://drive.google.com/sample',
            'is_curated_top10' => true,
        ]);

        // Juri 1 (GIM) di dashboard hanya melihat karya GIM
        $resJ1 = $this->actingAs($this->judge)->get(route('judge.dashboard', ['tab' => 'all']));
        $resJ1->assertStatus(200);
        $resJ1->assertSee('Peserta Karya Gim Edukasi');
        $resJ1->assertDontSee('Peserta Karya MPI Interaktif');

        // Juri 1 tidak dapat menyarankan karya MPI (403)
        $this->actingAs($this->judge)->postJson(route('judge.works.toggle-suggest', $mpiWork->id), [
            'reasons' => ['Ide karya orisinal'],
        ])->assertStatus(403);

        // Juri 1 tidak dapat menilai karya MPI (403)
        $this->actingAs($this->judge)->postJson(route('judge.works.score', $mpiWork->id), [
            'pedagogical_score' => 90,
            'creativity_score' => 85,
            'technical_score' => 88,
            'presentation_score' => 92,
        ])->assertStatus(403);

        // Juri 3 (MPI) di dashboard hanya melihat karya MPI
        $resJ3 = $this->actingAs($this->judge3)->get(route('judge.dashboard', ['tab' => 'all']));
        $resJ3->assertStatus(200);
        $resJ3->assertSee('Peserta Karya MPI Interaktif');
        $resJ3->assertDontSee('Peserta Karya Gim Edukasi');

        // Juri 3 tidak dapat menyarankan karya GIM (403)
        $this->actingAs($this->judge3)->postJson(route('judge.works.toggle-suggest', $gimWork->id), [
            'reasons' => ['Ide karya orisinal'],
        ])->assertStatus(403);
    }

    /**
     * Test validasi formulir tertutup:
     * Tombol disabled dan request saran Top 10 ditolak dengan error 422.
     */
    public function test_judge_cannot_suggest_or_withdraw_when_registration_form_is_closed(): void
    {
        $setting = RegistrationSetting::current();
        $setting->update([
            'mode' => 'manual',
            'is_open' => false,
        ]);

        $this->judge->update(['category' => 'gim_edukasi']);

        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0401',
            'full_name' => 'Peserta Uji Form Tutup',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'tutup401@example.com',
            'whatsapp_number' => '6281234567401',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://drive.google.com/sample',
        ]);

        // Request menyarankan saat form ditutup ditolak dengan status 422
        $res = $this->actingAs($this->judge)->postJson(route('judge.works.toggle-suggest', $work->id), [
            'reasons' => ['Ide karya orisinal'],
        ]);
        $res->assertStatus(422);
        $res->assertJsonFragment(['success' => false]);
        $this->assertStringContainsString('Formulir telah ditutup', $res->json('message'));

        // Di UI dashboard juri, menampilkan penanda formulir ditutup
        $dash = $this->actingAs($this->judge)->get(route('judge.dashboard', ['tab' => 'all']));
        $dash->assertStatus(200);
        $dash->assertSee('isFormOpen: false', false);
    }

    /**
     * Test admin dapat membuat akun juri baru dan menghapusnya,
     * serta akun juri inti (Juri 1, 2, 3) dilindungi dari penghapusan.
     */
    public function test_admin_judge_management_and_core_judge_protection(): void
    {
        // 1. Admin membuat akun juri baru
        $storeRes = $this->actingAs($this->admin)->post(route('admin.judges.store'), [
            'name' => 'Dewan Juri Tambahan MPI',
            'username' => 'juri_tambahan_mpi',
            'email' => 'juritambahan@edutech.btikp.id',
            'password' => 'Password123!',
            'category' => 'media_pembelajaran_interaktif',
        ]);
        $storeRes->assertRedirect(route('admin.dashboard', ['tab' => 'judges']));
        $storeRes->assertSessionHas('status');

        $newJudge = User::where('username', 'juri_tambahan_mpi')->first();
        $this->assertNotNull($newJudge);
        $this->assertEquals('media_pembelajaran_interaktif', $newJudge->category);
        $this->assertFalse($newJudge->isCoreJudge());

        // 2. Validasi email & username unik
        $duplicateRes = $this->actingAs($this->admin)->post(route('admin.judges.store'), [
            'name' => 'Dewan Juri Kembar',
            'username' => 'juri_tambahan_mpi',
            'email' => 'juritambahan@edutech.btikp.id',
            'password' => 'Password123!',
            'category' => 'gim_edukasi',
        ]);
        $duplicateRes->assertSessionHasErrors(['username', 'email']);

        // 3. Admin TIDAK BISA menghapus juri inti (Juri 1, Juri 2, Juri 3)
        $deleteCoreRes = $this->actingAs($this->admin)->delete(route('admin.judges.destroy', $this->judge->id));
        $deleteCoreRes->assertStatus(403);
        $this->assertNotNull(User::find($this->judge->id));

        // 4. Admin DAPAT menghapus akun juri baru
        $deleteNewRes = $this->actingAs($this->admin)->delete(route('admin.judges.destroy', $newJudge->id));
        $deleteNewRes->assertRedirect(route('admin.dashboard', ['tab' => 'judges']));
        $deleteNewRes->assertSessionHas('status');
        $this->assertNull(User::find($newJudge->id));
    }

    /**
     * Test admin dashboard renders judge management tab and modals cleanly with proper Alpine bindings.
     */
    public function test_admin_dashboard_renders_judge_management_modals_cleanly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', ['tab' => 'judges']));
        $response->assertStatus(200);

        // Pastikan Alpine states untuk manajemen juri terpasang dengan aman tanpa resiko null pointer exception
        $response->assertSee('showCreateJudgeModal: false', false);
        $response->assertSee('showEditJudgeModal: false', false);
        $response->assertSee('editingJudge:', false);
        $response->assertSee('openJudgeEdit', false);
        $response->assertSee('closeJudgeEdit', false);
        $response->assertSee('deleteJudge', false);
        $response->assertSee('closeDeleteJudge', false);

        // Pastikan elemen tombol dan modal ada di HTML
        $response->assertSee('Tambah Akun Juri');
        $response->assertSee('Edit Akun / Password');
        $response->assertSee(route('admin.judges.store'));
        $response->assertSee('showCreateJudgeModal', false);
        $response->assertSee('showEditJudgeModal', false);
        $response->assertSee('deleteJudgeTarget', false);
    }

    /**
     * Test pembagian kewenangan multi-juri:
     * - Juri 1 hanya menilai Poin 1 (Pedagogis) dan Poin 3 (Teknis)
     * - Juri 2 hanya menilai Poin 2 (Kreativitas)
     * - Juri 3 hanya menilai Poin 4 (Presentasi)
     */
    public function test_multi_judge_rubric_point_assignment(): void
    {
        $this->judge->update(['category' => 'gim_edukasi', 'judge_role' => 1]);
        $this->judge2->update(['category' => 'gim_edukasi', 'judge_role' => 2]);
        $this->judge3->update(['category' => 'gim_edukasi', 'judge_role' => 3]);

        $finalist = Registration::create([
            'registration_number' => 'EDUTECH-2026-0777',
            'full_name' => 'Karya Finalis Multi Juri',
            'school_name' => 'SMAN 1 RAHA',
            'regency_city' => 'Kabupaten Muna',
            'email' => 'finalis777@example.com',
            'whatsapp_number' => '628117777777',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test-finalist',
            'is_curated_top10' => true,
        ]);

        // 1. Juri 1: wajib Poin 1 & 3
        $resJ1 = $this->actingAs($this->judge)->postJson(route('judge.works.score', $finalist), [
            'pedagogical_score' => 90,
            'technical_score' => 80,
        ]);
        $resJ1->assertStatus(200);

        // Juri 1 submit skor di bawah 50 ditolak (422)
        $resJ1Invalid = $this->actingAs($this->judge)->postJson(route('judge.works.score', $finalist), [
            'pedagogical_score' => 40,
            'technical_score' => 80,
        ]);
        $resJ1Invalid->assertStatus(422)
            ->assertJsonValidationErrors(['pedagogical_score']);

        // Juri 1 submit skor di atas 100 ditolak (422)
        $resJ1Over = $this->actingAs($this->judge)->postJson(route('judge.works.score', $finalist), [
            'pedagogical_score' => 110,
            'technical_score' => 80,
        ]);
        $resJ1Over->assertStatus(422)
            ->assertJsonValidationErrors(['pedagogical_score']);

        // 2. Juri 2: wajib Poin 2
        $resJ2 = $this->actingAs($this->judge2)->postJson(route('judge.works.score', $finalist), [
            'creativity_score' => 85,
        ]);
        $resJ2->assertStatus(200);

        // 3. Juri 3: wajib Poin 4
        $resJ3 = $this->actingAs($this->judge3)->postJson(route('judge.works.score', $finalist), [
            'presentation_score' => 90,
        ]);
        $resJ3->assertStatus(200);

        // Periksa rekap multi-juri pada karya
        $finalist->refresh();
        $recap = $finalist->multi_judge_recap;

        $this->assertTrue($recap['is_complete']);
        $this->assertEquals(90, $recap['p1']);
        $this->assertEquals(85, $recap['p2']);
        $this->assertEquals(80, $recap['p3']);
        $this->assertEquals(90, $recap['p4']);

        // Weighted total: 90*0.30 (27.0) + 85*0.30 (25.5) + 80*0.25 (20.0) + 90*0.15 (13.5) = 86.0
        $this->assertEquals(86.0, $recap['weighted_total']);
        // Average: (90 + 85 + 80 + 90) / 4 = 86.25
        $this->assertEquals(86.25, $recap['average_score']);
    }

    /**
     * Test reset nilai oleh Juri dan oleh Admin:
     * - Juri mereset nilai miliknya pada karya tertentu
     * - Admin dapat mereset nilai juri tertentu dari panel admin
     * - Rekap otomatis kembali berstatus tidak lengkap (Menunggu penilaian)
     */
    public function test_judge_and_admin_can_reset_score_and_recap_reverts_to_incomplete(): void
    {
        $this->judge->update(['category' => 'gim_edukasi', 'judge_role' => 1]);
        $this->judge2->update(['category' => 'gim_edukasi', 'judge_role' => 2]);
        $this->judge3->update(['category' => 'gim_edukasi', 'judge_role' => 3]);

        $finalist = Registration::create([
            'registration_number' => 'EDUTECH-2026-0888',
            'full_name' => 'Karya Reset Test',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'reset888@example.com',
            'whatsapp_number' => '628118888888',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test-reset',
            'is_curated_top10' => true,
        ]);

        // Juri 1 & Juri 2 beri nilai
        $this->actingAs($this->judge)->postJson(route('judge.works.score', $finalist), [
            'pedagogical_score' => 85,
            'technical_score' => 85,
        ]);
        $this->actingAs($this->judge2)->postJson(route('judge.works.score', $finalist), [
            'creativity_score' => 90,
        ]);

        // Saat ini baru 2 juri -> rekap belum lengkap
        $finalist->refresh();
        $recapPartial = $finalist->multi_judge_recap;
        $this->assertFalse($recapPartial['is_complete']);
        $this->assertContains('Juri 3', $recapPartial['missing_judges']);

        // 1. Juri 2 mereset nilainya sendiri
        $resetJ2 = $this->actingAs($this->judge2)->deleteJson(route('judge.works.score.reset', $finalist));
        $resetJ2->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('judge_scores', [
            'user_id' => $this->judge2->id,
            'registration_id' => $finalist->id,
        ]);

        // Nilai Juri 1 tetap ada
        $this->assertDatabaseHas('judge_scores', [
            'user_id' => $this->judge->id,
            'registration_id' => $finalist->id,
        ]);

        // 2. Admin mereset nilai Juri 1 dari panel admin
        $adminReset = $this->actingAs($this->admin)->delete(route('admin.works.judge-score.reset', [
            'registration' => $finalist->id,
            'judge' => $this->judge->id,
        ]));
        $adminReset->assertRedirect();

        $this->assertDatabaseMissing('judge_scores', [
            'user_id' => $this->judge->id,
            'registration_id' => $finalist->id,
        ]);
    }

    /**
     * Test Rubrik Standar Bab IV: Skala Likert 1-5 dan kalkulasi contoh terbobot:
     * J1: 22/25 * 55 = 48.40
     * J2: 17/20 * 30 = 25.50
     * J3: 13/15 * 15 = 13.00
     * Nilai Akhir = 86.90
     */
    public function test_rubric_likert_1_to_5_calculation_example(): void
    {
        $this->judge->update(['category' => 'gim_edukasi', 'judge_role' => 1]);
        $this->judge2->update(['category' => 'gim_edukasi', 'judge_role' => 2]);
        $this->judge3->update(['category' => 'gim_edukasi', 'judge_role' => 3]);

        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0999',
            'full_name' => 'Karya Rubrik Standar Bab IV',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'rubrik999@example.com',
            'whatsapp_number' => '628119999999',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test-rubrik',
            'is_curated_top10' => true,
        ]);

        // Juri 1: 5 subindikator, total skor 22 (5, 4, 5, 4, 4) -> 22/25*55 = 48.40
        $resJ1 = $this->actingAs($this->judge)->postJson(route('judge.works.score', $work), [
            'subindicators' => [5, 4, 5, 4, 4],
        ]);
        $resJ1->assertStatus(200);
        $recapJ1 = $resJ1->json('recap');
        $this->assertFalse($recapJ1['is_complete']);
        $this->assertEquals(48.40, $recapJ1['judges'][1]['weighted_score']);
        $this->assertEquals(22, $recapJ1['judges'][1]['total_score']);
        $this->assertEquals(48.40, $recapJ1['interim_score']);

        // Juri 2: 4 subindikator, total skor 17 (5, 4, 4, 4) -> 17/20*30 = 25.50
        $resJ2 = $this->actingAs($this->judge2)->postJson(route('judge.works.score', $work), [
            'subindicators' => [5, 4, 4, 4],
        ]);
        $resJ2->assertStatus(200);
        $recapJ2 = $resJ2->json('recap');
        $this->assertFalse($recapJ2['is_complete']);
        $this->assertEquals(25.50, $recapJ2['judges'][2]['weighted_score']);
        $this->assertEquals(17, $recapJ2['judges'][2]['total_score']);
        $this->assertEquals(73.90, $recapJ2['interim_score']); // 48.40 + 25.50

        // Juri 3: 3 subindikator, total skor 13 (5, 4, 4) -> 13/15*15 = 13.00
        $resJ3 = $this->actingAs($this->judge3)->postJson(route('judge.works.score', $work), [
            'subindicators' => [5, 4, 4],
        ]);
        $resJ3->assertStatus(200);
        $recapJ3 = $resJ3->json('recap');
        $this->assertTrue($recapJ3['is_complete']);
        $this->assertEquals(13.00, $recapJ3['judges'][3]['weighted_score']);
        $this->assertEquals(13, $recapJ3['judges'][3]['total_score']);

        // Nilai Akhir = 48.40 + 25.50 + 13.00 = 86.90
        $this->assertEquals(86.90, $recapJ3['final_score']);
        $this->assertEquals('86.90', $recapJ3['display_score']);

        // Database assertions: 12 subindicators saved in rubric_scores
        $this->assertDatabaseCount('rubric_scores', 12);
    }

    /**
     * Test validasi skala Likert 1-5: input di bawah 1 atau di atas 5 ditolak.
     */
    public function test_subindicator_score_below_1_or_above_5_is_rejected(): void
    {
        $this->judge->update(['category' => 'gim_edukasi', 'judge_role' => 1]);

        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-0998',
            'full_name' => 'Karya Validasi Nilai',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'validasi998@example.com',
            'whatsapp_number' => '628119999998',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/test-validasi',
            'is_curated_top10' => true,
        ]);

        // Input nilai 0 (kurang dari 1) pada subindikator pertama
        $resZero = $this->actingAs($this->judge)->postJson(route('judge.works.score', $work), [
            'subindicators' => [0, 4, 5, 4, 4],
        ]);
        $resZero->assertStatus(422)
            ->assertJsonValidationErrors(['subindicators.1']);

        // Input nilai 6 (lebih dari 5) pada subindikator kedua
        $resSix = $this->actingAs($this->judge)->postJson(route('judge.works.score', $work), [
            'subindicators' => [5, 6, 5, 4, 4],
        ]);
        $resSix->assertStatus(422)
            ->assertJsonValidationErrors(['subindicators.2']);
    }

    /**
     * Test pemeringkatan dipisah per kategori (MPI vs Gim) dan hanya karya lengkap yang mendapat peringkat.
     */
    public function test_category_rankings_separated_and_only_ranked_when_complete(): void
    {
        $this->judge->update(['category' => 'gim_edukasi', 'judge_role' => 1]);
        $this->judge2->update(['category' => 'gim_edukasi', 'judge_role' => 2]);
        $this->judge3->update(['category' => 'gim_edukasi', 'judge_role' => 3]);

        $gim1 = Registration::create([
            'registration_number' => 'EDUTECH-GIM-01',
            'full_name' => 'Gim Pemenang 1',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'gim1@example.com',
            'whatsapp_number' => '628110000001',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/gim1',
            'is_curated_top10' => true,
        ]);

        $gim2 = Registration::create([
            'registration_number' => 'EDUTECH-GIM-02',
            'full_name' => 'Gim Pemenang 2',
            'school_name' => 'SMKN 2 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'gim2@example.com',
            'whatsapp_number' => '628110000002',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/gim2',
            'is_curated_top10' => true,
        ]);

        $mpiIncomplete = Registration::create([
            'registration_number' => 'EDUTECH-MPI-01',
            'full_name' => 'MPI Belum Lengkap',
            'school_name' => 'SMKN 3 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'mpi1@example.com',
            'whatsapp_number' => '628110000003',
            'learning_category' => 'media_pembelajaran_interaktif',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://canva.com/mpi1',
            'is_curated_top10' => true,
        ]);

        // Beri skor lengkap untuk gim1: skor tinggi (semua 5 -> 100)
        $this->actingAs($this->judge)->postJson(route('judge.works.score', $gim1), ['subindicators' => [5, 5, 5, 5, 5]]);
        $this->actingAs($this->judge2)->postJson(route('judge.works.score', $gim1), ['subindicators' => [5, 5, 5, 5]]);
        $this->actingAs($this->judge3)->postJson(route('judge.works.score', $gim1), ['subindicators' => [5, 5, 5]]);

        // Beri skor lengkap untuk gim2: skor sedang (semua 4 -> 80)
        $this->actingAs($this->judge)->postJson(route('judge.works.score', $gim2), ['subindicators' => [4, 4, 4, 4, 4]]);
        $this->actingAs($this->judge2)->postJson(route('judge.works.score', $gim2), ['subindicators' => [4, 4, 4, 4]]);
        $this->actingAs($this->judge3)->postJson(route('judge.works.score', $gim2), ['subindicators' => [4, 4, 4]]);

        // Beri skor hanya juri 1 pada MPI (belum lengkap)
        $mpiJudge1 = User::create([
            'name' => 'Juri MPI 1',
            'username' => 'jurimpi1',
            'email' => 'jurimpi1@example.com',
            'role' => 'judge',
            'category' => 'media_pembelajaran_interaktif',
            'judge_role' => 1,
            'password' => Hash::make('password123'),
        ]);
        $this->actingAs($mpiJudge1)->postJson(route('judge.works.score', $mpiIncomplete), ['subindicators' => [5, 5, 5, 5, 5]]);

        // Hitung pemeringkatan via RubricService
        $rankedGim = \App\Services\RubricService::getRankedWorks('gim_edukasi');
        $this->assertCount(2, $rankedGim);
        $this->assertEquals($gim1->id, $rankedGim[0]->id);
        $this->assertEquals(1, $rankedGim[0]->category_rank);
        $this->assertEquals($gim2->id, $rankedGim[1]->id);
        $this->assertEquals(2, $rankedGim[1]->category_rank);

        $rankedMpi = \App\Services\RubricService::getRankedWorks('media_pembelajaran_interaktif');
        $this->assertCount(1, $rankedMpi);
        $this->assertNull($rankedMpi[0]->category_rank); // Incomplete work does not get category rank
    }

    /**
     * Test endpoint realtime rekap nilai dan kategori.
     */
    public function test_realtime_score_recap_and_category_endpoints(): void
    {
        $this->judge->update(['category' => 'gim_edukasi', 'judge_role' => 1]);

        $work = Registration::create([
            'registration_number' => 'EDUTECH-2026-RT01',
            'full_name' => 'Karya Realtime API',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'rt01@example.com',
            'whatsapp_number' => '628117777777',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/rt01',
            'is_curated_top10' => true,
        ]);

        $resSingle = $this->actingAs($this->judge)->getJson(route('realtime.work.score-recap', $work));
        $resSingle->assertStatus(200)
            ->assertJsonStructure([
                'registration_id',
                'category',
                'is_complete',
                'judges',
            ]);

        $resCat = $this->actingAs($this->admin)->getJson(route('realtime.category-recap', ['category' => 'gim_edukasi']));
        $resCat->assertStatus(200)
            ->assertJsonStructure([
                'category',
                'works',
            ]);
    }

    /**
     * Test isolasi ketat kategori: Juri MPI hanya melihat peserta MPI dan Juri Gim hanya melihat peserta Gim.
     */
    public function test_judge_category_strict_isolation_between_mpi_and_gim(): void
    {
        $judgeGim = User::create([
            'name' => 'Juri Khusus Gim',
            'username' => 'juri_gim_khusus',
            'email' => 'jurigim@example.com',
            'role' => 'judge',
            'category' => 'gim_edukasi',
            'judge_role' => 1,
            'password' => Hash::make('password123'),
        ]);

        $judgeMpi = User::create([
            'name' => 'Juri Khusus MPI',
            'username' => 'juri_mpi_khusus',
            'email' => 'jurimpi@example.com',
            'role' => 'judge',
            'category' => 'media_pembelajaran_interaktif',
            'judge_role' => 1,
            'password' => Hash::make('password123'),
        ]);

        $workGim = Registration::create([
            'registration_number' => 'EDUTECH-2026-GIM99',
            'full_name' => 'Peserta Eksklusif Gim',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'gim99@example.com',
            'whatsapp_number' => '628119999991',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/gim99',
            'is_curated_top10' => true,
        ]);

        $workMpi = Registration::create([
            'registration_number' => 'EDUTECH-2026-MPI99',
            'full_name' => 'Peserta Eksklusif MPI',
            'school_name' => 'SMAN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'mpi99@example.com',
            'whatsapp_number' => '628119999992',
            'learning_category' => 'media_pembelajaran_interaktif',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://drive.google.com/mpi99',
            'is_curated_top10' => true,
        ]);

        // 1. Juri Gim mengakses dashboard
        $resGim = $this->actingAs($judgeGim)->get(route('judge.dashboard', ['tab' => 'finalists']));
        $resGim->assertStatus(200);
        $resGim->assertSee('Peserta Eksklusif Gim');
        $resGim->assertDontSee('Peserta Eksklusif MPI');
        $resGim->assertSee('Kategori Tugas: Gim Edukasi');

        // Juri Gim tidak dapat mereset nilai karya MPI (403)
        $this->actingAs($judgeGim)->delete(route('judge.works.score.reset', $workMpi))->assertStatus(403);

        // 2. Juri MPI mengakses dashboard
        $resMpi = $this->actingAs($judgeMpi)->get(route('judge.dashboard', ['tab' => 'finalists']));
        $resMpi->assertStatus(200);
        $resMpi->assertSee('Peserta Eksklusif MPI');
        $resMpi->assertDontSee('Peserta Eksklusif Gim');
        $resMpi->assertSee('Kategori Tugas: Media Pembelajaran Interaktif (MPI)');

        // Juri MPI tidak dapat mereset nilai karya Gim (403)
        $this->actingAs($judgeMpi)->delete(route('judge.works.score.reset', $workGim))->assertStatus(403);

        // 3. Juri MPI mengakses tab 'all'
        $resMpiAll = $this->actingAs($judgeMpi)->get(route('judge.dashboard', ['tab' => 'all']));
        $resMpiAll->assertStatus(200);
        $resMpiAll->assertSee('Peserta Eksklusif MPI');
        $resMpiAll->assertDontSee('Peserta Eksklusif Gim');
    }

    /**
     * Test ketiga juri dapat melihat rincian nilai yang telah diisi oleh juri lain sama seperti admin.
     */
    public function test_all_three_judges_can_view_scores_filled_by_other_judges_like_admin(): void
    {
        $this->judge->update(['category' => 'gim_edukasi', 'judge_role' => 1]);
        $this->judge2->update(['category' => 'gim_edukasi', 'judge_role' => 2]);
        $this->judge3->update(['category' => 'gim_edukasi', 'judge_role' => 3]);

        $finalist = Registration::create([
            'registration_number' => 'EDUTECH-2026-VIEW01',
            'full_name' => 'Karya Finalis Multi Juri Terpadu',
            'school_name' => 'SMKN 1 KENDARI',
            'regency_city' => 'Kota Kendari',
            'email' => 'view01@example.com',
            'whatsapp_number' => '628113333333',
            'learning_category' => 'gim_edukasi',
            'profile_photo_path' => 'profile-photos/sample.jpg',
            'media_link' => 'https://itch.io/view01',
            'is_curated_top10' => true,
        ]);

        // Juri 1 menilai (skor maksimal 25/25 -> 55%)
        $this->actingAs($this->judge)->postJson(route('judge.works.score', $finalist), [
            'subindicators' => [1 => 5, 2 => 5, 3 => 5, 4 => 5, 5 => 5],
        ])->assertStatus(200);

        // Juri 2 mengakses dashboard dan dapat melihat nilai Juri 1
        $resJ2 = $this->actingAs($this->judge2)->get(route('judge.dashboard', ['tab' => 'finalists']));
        $resJ2->assertStatus(200);
        $resJ2->assertSee('Karya Finalis Multi Juri Terpadu');
        $resJ2->assertSee('Juri 1: Pedagogis &amp; Teknis (55%)', false);
        $resJ2->assertSee('55.00'); // Nilai dari Juri 1 terlihat oleh Juri 2
        $resJ2->assertSee('judge-recap-initial-data', false);

        // Juri 3 mengakses dashboard dan juga dapat melihat nilai Juri 1
        $resJ3 = $this->actingAs($this->judge3)->get(route('judge.dashboard', ['tab' => 'finalists']));
        $resJ3->assertStatus(200);
        $resJ3->assertSee('55.00');
        $resJ3->assertSee('Juri 2: Kreativitas &amp; Desain (30%)', false);
        $resJ3->assertSee('Juri 3: Presentasi &amp; Dampak (15%)', false);
    }
}
