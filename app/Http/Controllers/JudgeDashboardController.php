<?php

namespace App\Http\Controllers;

use App\Models\JudgeScore;
use App\Models\JudgeSuggestion;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JudgeDashboardController extends Controller
{
    /**
     * Tampilkan portal dewan juri dengan 3 tab:
     * 1. Semua Peserta (Full List - Review & Beri Saran Top 10)
     * 2. Saran Saya (Daftar karya yang telah diusulkan juri ini)
     * 3. Finalis (Top 10 yang telah disetujui Admin - Siap Dinilai dengan Rubrik)
     */
    public function index(Request $request): View
    {
        /** @var User $judge */
        $judge = Auth::user();

        $activeTab = $request->input('tab', 'finalists');
        if (!in_array($activeTab, ['all', 'my_suggestions', 'finalists'])) {
            $activeTab = 'finalists';
        }

        $category = $request->input('category', 'all');
        $search = $request->input('search', '');

        // Base query with relations
        $buildQuery = function () use ($judge, $category, $search) {
            $q = Registration::with([
                'judgeSuggestions.user',
                'judgeScores' => function ($sq) use ($judge) {
                    $sq->where('user_id', $judge->id);
                },
            ]);

            if ($category === 'gim_edukasi' || $category === 'media_pembelajaran_interaktif') {
                $q->where('learning_category', $category);
            }

            if (!empty($search)) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('full_name', 'like', "%{$search}%")
                        ->orWhere('school_name', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%");
                });
            }

            return $q;
        };

        // 1. Tab Semua Peserta
        $allWorks = (clone $buildQuery())
            ->orderBy('learning_category', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 2. Tab Saran Saya
        $mySuggestions = (clone $buildQuery())
            ->whereHas('judgeSuggestions', function ($sq) use ($judge) {
                $sq->where('user_id', $judge->id);
            })
            ->orderBy('learning_category', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 3. Tab Finalis (Hanya yang telah disetujui Admin masuk Top 10)
        $finalists = (clone $buildQuery())
            ->where('is_curated_top10', true)
            ->orderBy('learning_category', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Hitung kuota saran juri saat ini (Maksimal 10 karya per kategori)
        $myGimSuggestionsCount = JudgeSuggestion::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('learning_category', 'gim_edukasi'))
            ->count();

        $myMediaSuggestionsCount = JudgeSuggestion::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('learning_category', 'media_pembelajaran_interaktif'))
            ->count();

        // Statistik Penilaian Finalis
        $totalFinalists = Registration::where('is_curated_top10', true)->count();
        $scoredFinalistsCount = JudgeScore::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('is_curated_top10', true))
            ->count();

        // Seluruh Juri untuk referensi visual badge
        $allJudges = User::where('role', 'judge')->orderBy('id', 'asc')->get();

        return view('judge.dashboard', [
            'judge' => $judge,
            'allJudges' => $allJudges,
            'activeTab' => $activeTab,
            'category' => $category,
            'search' => $search,
            'allWorks' => $allWorks,
            'mySuggestions' => $mySuggestions,
            'finalists' => $finalists,
            'stats' => [
                'total_all' => Registration::count(),
                'my_suggestions_count' => $myGimSuggestionsCount + $myMediaSuggestionsCount,
                'my_gim_suggestions' => $myGimSuggestionsCount,
                'my_media_suggestions' => $myMediaSuggestionsCount,
                'remaining_gim_suggestions' => max(0, JudgeSuggestion::MAX_SUGGESTIONS_PER_CATEGORY - $myGimSuggestionsCount),
                'remaining_media_suggestions' => max(0, JudgeSuggestion::MAX_SUGGESTIONS_PER_CATEGORY - $myMediaSuggestionsCount),
                'finalists_count' => $totalFinalists,
                'scored_finalists_count' => $scoredFinalistsCount,
                'remaining_finalists_to_score' => max(0, $totalFinalists - $scoredFinalistsCount),
            ],
        ]);
    }

    /**
     * Toggle rekomendasi / usulan karya untuk Top 10 oleh juri.
     * Maksimal 10 karya per kategori per juri.
     */
    public function toggleSuggestion(Request $request, Registration $registration): JsonResponse
    {
        /** @var User $judge */
        $judge = Auth::user();

        $existing = JudgeSuggestion::where('user_id', $judge->id)
            ->where('registration_id', $registration->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isSuggested = false;
            $message = "Saran Top 10 untuk karya '{$registration->full_name}' berhasil ditarik.";
        } else {
            // Cek kuota: masing-masing juri hanya bisa menyarankan maksimal 10 karya per kategori
            $currentCategoryCount = JudgeSuggestion::where('user_id', $judge->id)
                ->whereHas('registration', function ($q) use ($registration) {
                    $q->where('learning_category', $registration->learning_category);
                })->count();

            if ($currentCategoryCount >= JudgeSuggestion::MAX_SUGGESTIONS_PER_CATEGORY) {
                return response()->json([
                    'success' => false,
                    'message' => "Kuota saran Top 10 Anda untuk kategori '{$registration->category_label}' sudah penuh (maksimal 10 karya). Batalkan salah satu saran Anda terlebih dahulu.",
                ], 422);
            }

            JudgeSuggestion::create([
                'user_id' => $judge->id,
                'registration_id' => $registration->id,
            ]);
            $isSuggested = true;
            $message = "Karya '{$registration->full_name}' berhasil Anda sarankan untuk nominasi Top 10!";
        }

        // Hitung ulang kuota saran juri ini
        $gimCount = JudgeSuggestion::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('learning_category', 'gim_edukasi'))
            ->count();

        $mediaCount = JudgeSuggestion::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('learning_category', 'media_pembelajaran_interaktif'))
            ->count();

        // Ambil daftar seluruh juri yang menyarankan karya ini sekarang
        $registration->load('judgeSuggestions.user');
        $suggestions = $registration->judgeSuggestions->map(function ($s) use ($registration) {
            $code = $s->user->judge_code ?? ('J' . $s->user_id);
            return [
                'user_id' => $s->user_id,
                'judge_name' => $s->user->name ?? 'Dewan Juri',
                'judge_username' => $s->user->username ?? ('juri' . $s->user_id),
                'judge_email' => $s->user->email ?? '-',
                'judge_code' => $code,
                'focus' => match ($code) {
                    'J1' => 'Gim Edukasi & Interaktivitas Media Digital',
                    'J2' => 'Pedagogik, Kurikulum & Substansi Materi SMA/SMK',
                    'J3' => 'Desain Visual, Estetika & Kualitas UI/UX',
                    default => 'Evaluasi Inovasi Pembelajaran Digital BTIKP',
                },
                'suggested_at' => $s->created_at ? $s->created_at->translatedFormat('d M Y, H:i') . ' WITA' : 'Tercatat di sistem',
                'participant_name' => $registration->full_name,
                'color' => match ($code) {
                    'J1' => 'from-indigo-600 via-indigo-700 to-indigo-900',
                    'J2' => 'from-emerald-600 via-teal-700 to-emerald-900',
                    'J3' => 'from-amber-500 via-orange-600 to-amber-800',
                    default => 'from-purple-600 via-indigo-700 to-purple-900',
                },
                'badgeColor' => match ($code) {
                    'J1' => 'bg-indigo-500 text-white',
                    'J2' => 'bg-emerald-500 text-white',
                    'J3' => 'bg-amber-500 text-white',
                    default => 'bg-purple-500 text-white',
                },
                'colorClass' => match ($code) {
                    'J1' => 'bg-indigo-600 text-white',
                    'J2' => 'bg-emerald-600 text-white',
                    'J3' => 'bg-amber-600 text-white',
                    default => 'bg-purple-600 text-white',
                },
            ];
        });

        return response()->json([
            'success' => true,
            'is_suggested' => $isSuggested,
            'message' => $message,
            'registration_id' => $registration->id,
            'suggestions' => $suggestions,
            'suggestions_count' => $suggestions->count(),
            'stats' => [
                'gim_suggestions' => $gimCount,
                'media_suggestions' => $mediaCount,
                'total_suggestions' => $gimCount + $mediaCount,
                'remaining_gim' => max(0, JudgeSuggestion::MAX_SUGGESTIONS_PER_CATEGORY - $gimCount),
                'remaining_media' => max(0, JudgeSuggestion::MAX_SUGGESTIONS_PER_CATEGORY - $mediaCount),
            ],
        ]);
    }

    /**
     * Simpan atau perbarui nilai penilaian dewan juri untuk sebuah karya.
     * Hanya karya yang masuk Top 10 (disetujui Admin) yang dapat dinilai.
     */
    public function saveScore(Request $request, Registration $registration): JsonResponse|RedirectResponse
    {
        // Pastikan hanya karya yang masuk Top 10 (Finalis) yang dapat dinilai
        if (!$registration->is_curated_top10) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karya ini belum atau tidak masuk dalam nominasi Finalis Top 10 yang disetujui Admin.',
                ], 403);
            }
            abort(403, 'Karya ini belum atau tidak masuk dalam nominasi Finalis Top 10 yang disetujui Admin.');
        }

        /** @var User $judge */
        $judge = Auth::user();

        $validated = $request->validate([
            'pedagogical_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'creativity_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'technical_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'presentation_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            '*.required' => 'Seluruh aspek nilai wajib diisi antara 0 - 100.',
            '*.numeric' => 'Nilai harus berupa angka numerik.',
            '*.min' => 'Nilai minimal adalah 0.',
            '*.max' => 'Nilai maksimal adalah 100.',
        ]);

        $pedagogical = (float) $validated['pedagogical_score'];
        $creativity = (float) $validated['creativity_score'];
        $technical = (float) $validated['technical_score'];
        $presentation = (float) $validated['presentation_score'];

        $totalScore = JudgeScore::computeWeightedTotal($pedagogical, $creativity, $technical, $presentation);

        $score = JudgeScore::updateOrCreate(
            [
                'user_id' => $judge->id,
                'registration_id' => $registration->id,
            ],
            [
                'pedagogical_score' => $pedagogical,
                'creativity_score' => $creativity,
                'technical_score' => $technical,
                'presentation_score' => $presentation,
                'total_score' => $totalScore,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $message = "Nilai karya '{$registration->full_name}' berhasil disimpan! Total skor: {$totalScore}";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'score' => $score,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Memperbarui profil juri (Username, Email, Password secara mandiri).
     */
    public function updateProfile(Request $request): RedirectResponse|JsonResponse
    {
        /** @var User $judge */
        $judge = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($judge->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($judge->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'username.unique' => 'Username tersebut sudah digunakan oleh akun lain.',
            'email.unique' => 'Email tersebut sudah terdaftar untuk akun lain.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $judge->name = trim($validated['name']);
        $judge->username = trim($validated['username']);
        $judge->email = strtolower(trim($validated['email']));

        if (!empty($validated['password'])) {
            $judge->password = Hash::make($validated['password']);
        }

        $judge->save();

        $message = 'Profil dan kredensial akun Anda berhasil diperbarui!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'user' => [
                    'name' => $judge->name,
                    'username' => $judge->username,
                    'email' => $judge->email,
                ],
            ]);
        }

        return back()->with('status', $message);
    }
}
