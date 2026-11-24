<?php

namespace App\Http\Controllers;

use App\Models\JudgeScore;
use App\Models\JudgeSuggestion;
use App\Models\Registration;
use App\Models\RegistrationSetting;
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

        // Pembatasan Kategori Juri:
        // Jika akun dewan juri telah ditugaskan untuk kategori spesifik (GIM atau MPI),
        // kunci parameter kategori secara ketat hanya pada kategori tersebut.
        if (!empty($judge->category)) {
            $category = $judge->category;
        }

        // Base query with relations
        $buildQuery = function () use ($judge, $category, $search) {
            $q = Registration::with([
                'judgeSuggestions.user',
                'judgeScores' => function ($sq) use ($judge) {
                    $sq->where('user_id', $judge->id);
                },
            ]);

            // Pembatasan kategori juri di sisi backend/query:
            // Juri kategori MPI hanya melihat MPI, dan juri kategori GIM hanya melihat GIM.
            if (!empty($judge->category)) {
                $q->where('learning_category', $judge->category);
            } elseif ($category === 'gim_edukasi' || $category === 'media_pembelajaran_interaktif') {
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
        $role = $judge->judge_role ?? ($judge->judge_code === 'J1' ? 1 : ($judge->judge_code === 'J2' ? 2 : ($judge->judge_code === 'J3' ? 3 : 1)));

        $finalists = (clone $buildQuery())
            ->where('is_curated_top10', true)
            ->with(['rubricScores.user', 'judgeScores.user'])
            ->orderBy('learning_category', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $catKey = !empty($judge->category) ? $judge->category : null;
        $rankedWorksList = \App\Services\RubricService::getRankedWorks($catKey);
        $rankMap = collect($rankedWorksList)->pluck('category_rank', 'id')->all();

        $finalists->transform(function ($work) use ($judge, $role, $rankMap) {
            $work->recap = \App\Services\RubricService::calculateRecap($work);
            $work->multi_recap = $work->recap;
            $work->category_rank = $rankMap[$work->id] ?? null;
            $work->my_status = $work->judgeWorkStatus($judge);
            $work->my_subindicators = $work->rubricScores
                ->where('jury_role', $role)
                ->pluck('score', 'subindicator_index')
                ->all();
            return $work;
        });

        // Urutkan finalis berdasarkan peringkat kategori dan skor akhir
        $finalists = $finalists->sortBy([
            fn ($a, $b) => ($a->category_rank === null) <=> ($b->category_rank === null),
            ['category_rank', 'asc'],
            [fn ($w) => $w->recap['is_complete'] ? $w->recap['final_score'] : ($w->recap['interim_score'] ?? 0.0), 'desc'],
            ['id', 'asc'],
        ])->values();

        $initialScoresMap = [];
        $initialRanksMap = [];
        foreach ($finalists as $w) {
            $initialScoresMap[$w->id] = $w->recap;
            $initialRanksMap[$w->id] = $w->category_rank;
        }

        // Hitung jumlah saran juri saat ini per kategori
        $myGimSuggestionsCount = JudgeSuggestion::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('learning_category', 'gim_edukasi'))
            ->count();

        $myMediaSuggestionsCount = JudgeSuggestion::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('learning_category', 'media_pembelajaran_interaktif'))
            ->count();

        // Statistik Penilaian Finalis (Disesuaikan dengan penugasan kategori juri)
        $totalFinalistsQuery = Registration::where('is_curated_top10', true);
        if (!empty($judge->category)) {
            $totalFinalistsQuery->where('learning_category', $judge->category);
        }
        $totalFinalists = $totalFinalistsQuery->count();

        $scoredFinalistsCount = JudgeScore::where('user_id', $judge->id)
            ->whereHas('registration', function ($q) use ($judge) {
                $q->where('is_curated_top10', true);
                if (!empty($judge->category)) {
                    $q->where('learning_category', $judge->category);
                }
            })
            ->count();

        // Seluruh Juri untuk referensi visual badge
        $allJudges = User::where('role', 'judge')->orderBy('id', 'asc')->get();

        // Siapkan Reactive Suggestions Map per karya untuk frontend Alpine.js
        $suggestionsMap = [];
        foreach ($allWorks as $w) {
            $mySug = $w->judgeSuggestions->firstWhere('user_id', $judge->id);
            $suggestionsMap[$w->id] = [
                'isSuggestedByMe' => (bool) $mySug,
                'count' => $w->judgeSuggestions->count(),
                'myReasons' => $mySug?->reasons ?? [],
                'suggestions' => $w->judgeSuggestions->map(function ($s) use ($w) {
                    $colors = $s->judge_color_classes;
                    return [
                        'userId' => $s->user_id,
                        'name' => $s->user->name ?? 'Dewan Juri',
                        'username' => $s->user->username ?? ('juri' . $s->user_id),
                        'email' => $s->user->email ?? '-',
                        'code' => $s->judge_code,
                        'reasons' => $s->reasons ?? [],
                        'focus' => $colors['focus'],
                        'suggested_at' => $s->created_at ? $s->created_at->translatedFormat('d M Y, H:i') . ' WITA' : 'Tercatat di sistem',
                        'participant_name' => $w->full_name,
                        'colorClass' => $colors['colorClass'],
                        'color' => $colors['color'],
                        'badgeColor' => $colors['badgeColor'],
                    ];
                })->values()->all(),
            ];
        }

        return view('judge.dashboard', [
            'judge' => $judge,
            'allJudges' => $allJudges,
            'activeTab' => $activeTab,
            'category' => $category,
            'search' => $search,
            'allWorks' => $allWorks,
            'mySuggestions' => $mySuggestions,
            'finalists' => $finalists,
            'suggestionsMap' => $suggestionsMap,
            'initialScoresMap' => $initialScoresMap,
            'initialRanksMap' => $initialRanksMap,
            'rubricRoles' => \App\Services\RubricService::getRoles(),
            'rubricScale' => \App\Services\RubricService::getScale(),
            'myRoleConfig' => \App\Services\RubricService::getRoleConfig($role),
            'myRole' => $role,
            'isFormOpen' => RegistrationSetting::current()->isOpen(),
            'stats' => [
                'total_all' => !empty($judge->category) 
                    ? Registration::where('learning_category', $judge->category)->count()
                    : Registration::count(),
                'my_suggestions_count' => $myGimSuggestionsCount + $myMediaSuggestionsCount,
                'my_gim_suggestions' => $myGimSuggestionsCount,
                'my_media_suggestions' => $myMediaSuggestionsCount,
                'finalists_count' => $totalFinalists,
                'scored_finalists_count' => $scoredFinalistsCount,
                'remaining_finalists_to_score' => max(0, $totalFinalists - $scoredFinalistsCount),
            ],
        ]);
    }

    /**
     * Simpan saran, perbarui alasan, atau tarik usulan karya untuk Top 10 oleh juri.
     * Tidak ada batasan jumlah saran bagi juri.
     * Dilengkapi checklist alasan kurasi (minimal 1 kriteria dipilih).
     */
    public function toggleSuggestion(Request $request, Registration $registration): JsonResponse
    {
        /** @var User $judge */
        $judge = Auth::user();

        // 1. Validasi Status Formulir Tertutup (Poin 4)
        $setting = RegistrationSetting::current();
        if (!$setting->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Formulir telah ditutup. Anda tidak dapat lagi memberikan, mengubah, atau menarik saran Top 10.',
            ], 422);
        }

        // 2. Pembatasan Kategori Juri (Poin 3)
        if (!empty($judge->category) && $registration->learning_category !== $judge->category) {
            $catLabel = $judge->category_label ?? $judge->category;
            return response()->json([
                'success' => false,
                'message' => "Anda hanya berwenang merekomendasikan karya dalam kategori {$catLabel}.",
            ], 403);
        }

        $existing = JudgeSuggestion::where('user_id', $judge->id)
            ->where('registration_id', $registration->id)
            ->first();

        $isWithdraw = $request->input('action') === 'withdraw' 
            || $request->boolean('withdraw') 
            || ($existing && !$request->has('reasons') && !$request->has('action'));

        if ($isWithdraw) {
            if ($existing) {
                $existing->delete();
            }
            $isSuggested = false;
            $message = "Saran Top 10 untuk karya '{$registration->full_name}' berhasil ditarik.";
        } else {
            // Validasi checklist alasan kurasi
            $validated = $request->validate([
                'reasons' => ['required', 'array', 'min:1'],
                'reasons.*' => ['string', 'max:255'],
            ], [
                'reasons.required' => 'Pilih minimal 1 alasan rekomendasi sebelum menyimpan saran Top 10.',
                'reasons.array' => 'Format alasan tidak valid.',
                'reasons.min' => 'Pilih minimal 1 alasan rekomendasi sebelum menyimpan saran Top 10.',
            ]);

            if ($existing) {
                // Perbarui alasan checklist
                $existing->update([
                    'reasons' => array_values(array_unique($validated['reasons'])),
                ]);
                $isSuggested = true;
                $message = "Alasan saran Top 10 untuk karya '{$registration->full_name}' berhasil diperbarui!";
            } else {
                JudgeSuggestion::create([
                    'user_id' => $judge->id,
                    'registration_id' => $registration->id,
                    'reasons' => array_values(array_unique($validated['reasons'])),
                ]);
                $isSuggested = true;
                $message = "Karya '{$registration->full_name}' berhasil Anda sarankan untuk nominasi Top 10!";
            }
        }

        // Hitung ulang kuota saran juri ini
        $gimCount = JudgeSuggestion::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('learning_category', 'gim_edukasi'))
            ->count();

        $mediaCount = JudgeSuggestion::where('user_id', $judge->id)
            ->whereHas('registration', fn($q) => $q->where('learning_category', 'media_pembelajaran_interaktif'))
            ->count();

        // Ambil daftar seluruh juri yang menyarankan karya ini sekarang beserta alasannya
        $registration->load('judgeSuggestions.user');
        $myReasons = [];

        $suggestions = $registration->judgeSuggestions->map(function ($s) use ($registration, $judge, &$myReasons) {
            $colors = $s->judge_color_classes;
            $reasons = $s->reasons ?? [];
            if ($s->user_id === $judge->id) {
                $myReasons = $reasons;
            }

            return [
                'user_id' => $s->user_id,
                'judge_name' => $s->user->name ?? 'Dewan Juri',
                'judge_username' => $s->user->username ?? ('juri' . $s->user_id),
                'judge_email' => $s->user->email ?? '-',
                'judge_code' => $s->judge_code,
                'reasons' => $reasons,
                'focus' => $colors['focus'],
                'suggested_at' => $s->created_at ? $s->created_at->translatedFormat('d M Y, H:i') . ' WITA' : 'Tercatat di sistem',
                'participant_name' => $registration->full_name,
                'color' => $colors['color'],
                'badgeColor' => $colors['badgeColor'],
                'colorClass' => $colors['colorClass'],
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'is_suggested' => $isSuggested,
            'message' => $message,
            'registration_id' => $registration->id,
            'my_reasons' => $myReasons,
            'suggestions' => $suggestions,
            'suggestions_count' => count($suggestions),
            'stats' => [
                'gim_suggestions' => $gimCount,
                'media_suggestions' => $mediaCount,
                'total_suggestions' => $gimCount + $mediaCount,
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

        // Pembatasan Kategori Juri (Poin 3)
        if (!empty($judge->category) && $registration->learning_category !== $judge->category) {
            $catLabel = $judge->category_label ?? $judge->category;
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Anda hanya berwenang menilai karya dalam kategori {$catLabel}.",
                ], 403);
            }
            abort(403, "Anda hanya berwenang menilai karya dalam kategori {$catLabel}.");
        }

        $role = $judge->judge_role;
        if ($role === null) {
            $code = $judge->judge_code;
            $role = match ($code) {
                'J1' => 1,
                'J2' => 2,
                'J3' => 3,
                default => 0,
            };
        }

        // =========================================================================
        // 1. FORMAT UTAMA: Penilaian 12 Subindikator Rubrik Bab IV (Tombol Likert 1-5)
        // =========================================================================
        if ($request->has('subindicators')) {
            $config = \App\Services\RubricService::getRoleConfig($role);
            $itemCount = $config ? count($config['subindicators']) : 0;

            // Normalisasi indeks jika input berupa array 0-indexed (misal [5, 4, 5, 4, 4])
            $rawSubs = $request->input('subindicators');
            if (is_array($rawSubs) && isset($rawSubs[0]) && !isset($rawSubs[$itemCount])) {
                $normalizedSubs = [];
                foreach (array_values($rawSubs) as $zeroIdx => $val) {
                    $normalizedSubs[$zeroIdx + 1] = $val;
                }
                $request->merge(['subindicators' => $normalizedSubs]);
            }

            $rules = [
                'subindicators' => ['required', 'array'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ];
            $messages = [
                'subindicators.required' => 'Seluruh subindikator wajib dinilai sebelum menyimpan.',
            ];

            for ($i = 1; $i <= $itemCount; $i++) {
                $rules["subindicators.{$i}"] = ['required', 'integer', 'between:1,5'];
                $messages["subindicators.{$i}.required"] = "Subindikator nomor {$i} wajib diisi.";
                $messages["subindicators.{$i}.integer"] = "Nilai subindikator nomor {$i} harus berupa bilangan bulat antara 1 sampai 5.";
                $messages["subindicators.{$i}.between"] = "Nilai subindikator nomor {$i} harus bernilai antara 1 sampai 5.";
            }

            $validated = $request->validate($rules, $messages);
            $subindicators = $validated['subindicators'];

            // Simpan rincian nilai subindikator ke database
            foreach ($subindicators as $idx => $val) {
                \App\Models\RubricScore::updateOrCreate(
                    [
                        'registration_id' => $registration->id,
                        'jury_role' => $role,
                        'subindicator_index' => (int) $idx,
                    ],
                    [
                        'user_id' => $judge->id,
                        'score' => (int) $val,
                    ]
                );
            }

            // Hitung nilai tertimbang juri ini
            $roleScore = \App\Services\RubricService::calculateRoleScore($role, $subindicators);

            // Simpan ringkasan nilai ke tabel judge_scores
            $score = JudgeScore::updateOrCreate(
                [
                    'user_id' => $judge->id,
                    'registration_id' => $registration->id,
                ],
                [
                    'pedagogical_score' => ($role === 1) ? $roleScore['total_score'] : 0,
                    'creativity_score' => ($role === 2) ? $roleScore['total_score'] : 0,
                    'technical_score' => ($role === 1) ? $roleScore['total_score'] : 0,
                    'presentation_score' => ($role === 3) ? $roleScore['total_score'] : 0,
                    'total_score' => $roleScore['weighted_score'],
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            $recap = \App\Services\RubricService::calculateRecap($registration);
            $message = "Nilai karya '{$registration->full_name}' berhasil disimpan! Total skor: {$roleScore['total_score']}/{$roleScore['max_score']} (Nilai Tertimbang: " . number_format($roleScore['weighted_score'], 2) . ")";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'score' => $score,
                    'role_score' => $roleScore,
                    'recap' => $recap,
                ]);
            }

            return back()->with('status', $message);
        }

        // =========================================================================
        // 2. FORMAT FALLBACK / LEGACY: Aspek Rubrik Bab IV (Nilai 50-100)
        // =========================================================================
        if ($role === 1) {
            $rules = [
                'pedagogical_score' => ['required', 'integer', 'min:50', 'max:100'],
                'technical_score' => ['required', 'integer', 'min:50', 'max:100'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ];
            $messages = [
                'pedagogical_score.required' => 'Poin 1 (Pedagogis & Subjek Materi) wajib diisi sebelum menyimpan.',
                'pedagogical_score.integer' => 'Nilai Poin 1 harus berupa bilangan bulat antara 50 sampai 100.',
                'pedagogical_score.min' => 'Nilai Poin 1 tidak boleh kurang dari 50.',
                'pedagogical_score.max' => 'Nilai Poin 1 tidak boleh lebih dari 100.',
                'technical_score.required' => 'Poin 3 (Kualitas Teknis & Interaktivitas) wajib diisi sebelum menyimpan.',
                'technical_score.integer' => 'Nilai Poin 3 harus berupa bilangan bulat antara 50 sampai 100.',
                'technical_score.min' => 'Nilai Poin 3 tidak boleh kurang dari 50.',
                'technical_score.max' => 'Nilai Poin 3 tidak boleh lebih dari 100.',
            ];
        } elseif ($role === 2) {
            $rules = [
                'creativity_score' => ['required', 'integer', 'min:50', 'max:100'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ];
            $messages = [
                'creativity_score.required' => 'Poin 2 (Kreativitas & Desain Visual) wajib diisi sebelum menyimpan.',
                'creativity_score.integer' => 'Nilai Poin 2 harus berupa bilangan bulat antara 50 sampai 100.',
                'creativity_score.min' => 'Nilai Poin 2 tidak boleh kurang dari 50.',
                'creativity_score.max' => 'Nilai Poin 2 tidak boleh lebih dari 100.',
            ];
        } elseif ($role === 3) {
            $rules = [
                'presentation_score' => ['required', 'integer', 'min:50', 'max:100'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ];
            $messages = [
                'presentation_score.required' => 'Poin 4 (Presentasi & Dampak Penerapan) wajib diisi sebelum menyimpan.',
                'presentation_score.integer' => 'Nilai Poin 4 harus berupa bilangan bulat antara 50 sampai 100.',
                'presentation_score.min' => 'Nilai Poin 4 tidak boleh kurang dari 50.',
                'presentation_score.max' => 'Nilai Poin 4 tidak boleh lebih dari 100.',
            ];
        } else {
            $rules = [
                'pedagogical_score' => ['required', 'integer', 'min:50', 'max:100'],
                'creativity_score' => ['required', 'integer', 'min:50', 'max:100'],
                'technical_score' => ['required', 'integer', 'min:50', 'max:100'],
                'presentation_score' => ['required', 'integer', 'min:50', 'max:100'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ];
            $messages = [
                '*.required' => 'Nilai poin wajib diisi sebelum menyimpan.',
                '*.integer' => 'Nilai harus berupa bilangan bulat antara 50 sampai 100.',
                '*.min' => 'Nilai tidak boleh kurang dari 50.',
                '*.max' => 'Nilai tidak boleh lebih dari 100.',
            ];
        }

        $validated = $request->validate($rules, $messages);

        $pedagogical = isset($validated['pedagogical_score']) ? (float) $validated['pedagogical_score'] : 0.0;
        $creativity = isset($validated['creativity_score']) ? (float) $validated['creativity_score'] : 0.0;
        $technical = isset($validated['technical_score']) ? (float) $validated['technical_score'] : 0.0;
        $presentation = isset($validated['presentation_score']) ? (float) $validated['presentation_score'] : 0.0;

        $totalScore = JudgeScore::computeWeightedTotalForJudge(
            $judge,
            $pedagogical,
            $creativity,
            $technical,
            $presentation
        );

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

        $recap = \App\Services\RubricService::calculateRecap($registration);
        $message = "Nilai karya '{$registration->full_name}' berhasil disimpan! Total skor Anda: {$totalScore}";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'score' => $score,
                'recap' => $recap,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Reset / batalkan penilaian dewan juri untuk sebuah karya.
     * Reset hanya menghapus nilai milik juri tersebut pada karya yang dipilih, tanpa memengaruhi nilai juri lain.
     */
    public function resetScore(Request $request, Registration $registration): JsonResponse|RedirectResponse
    {
        /** @var User $judge */
        $judge = Auth::user();

        // Validasi Kategori: Juri hanya boleh mengelola karya yang sesuai kategorinya
        if (!empty($judge->category) && $registration->learning_category !== $judge->category) {
            $catLabel = $judge->category === 'gim_edukasi' ? 'Gim Edukasi' : 'Media Pembelajaran Interaktif';
            abort(403, "Anda hanya berwenang mengelola karya dalam kategori {$catLabel}.");
        }

        // Hapus nilai rincian subindikator milik juri ini
        \App\Models\RubricScore::where('user_id', $judge->id)
            ->where('registration_id', $registration->id)
            ->delete();

        // Hapus nilai ringkasan juri ini
        JudgeScore::where('user_id', $judge->id)
            ->where('registration_id', $registration->id)
            ->delete();

        $recap = \App\Services\RubricService::calculateRecap($registration);
        $message = "Nilai Anda untuk karya '{$registration->full_name}' berhasil direset. Status kembali menjadi Belum dinilai.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'recap' => $recap,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Endpoint API Realtime untuk mengambil pratinjau / rekap nilai terkini sebuah karya.
     */
    public function getScoreRecap(Request $request, Registration $registration): JsonResponse
    {
        return response()->json(\App\Services\RubricService::calculateRecap($registration));
    }

    /**
     * Endpoint API Realtime untuk sinkronisasi pemeringkatan dan rekap seluruh finalis per kategori.
     */
    public function getCategoryRecap(Request $request, string $category = null): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && $user->isJudge() && !empty($user->category)) {
            $categoryKey = $user->category;
        } else {
            $categoryKey = match ($category) {
                'mpi', 'media_pembelajaran_interaktif' => 'media_pembelajaran_interaktif',
                'gim', 'gim_edukasi' => 'gim_edukasi',
                default => null,
            };
        }

        $rankedWorks = \App\Services\RubricService::getRankedWorks($categoryKey);

        $mapped = array_map(function ($work) {
            return [
                'id' => $work->id,
                'full_name' => $work->full_name,
                'school_name' => $work->school_name,
                'regency_city' => $work->regency_city,
                'learning_category' => $work->learning_category,
                'category_label' => $work->category_label,
                'category_rank' => $work->category_rank,
                'calculated_score' => $work->calculated_score,
                'profile_photo_url' => $work->profile_photo_url,
                'media_link' => $work->media_link,
                'multi_recap' => $work->multi_recap ?? \App\Services\RubricService::calculateRecap($work),
                'judge_scores' => $work->judgeScores ? $work->judgeScores->map(fn($js) => [
                    'user_id' => $js->user_id,
                    'user_name' => $js->user?->name ?? $js->judge?->name ?? 'Juri',
                    'role_label' => $js->user?->judge_role_label ?? $js->judge?->judge_role_label ?? 'Juri',
                    'reset_url' => route('admin.works.judge-score.reset', ['registration' => $work->id, 'judge' => $js->user_id]),
                ])->values()->all() : [],
            ];
        }, $rankedWorks);

        return response()->json([
            'category' => $categoryKey ?? 'all',
            'works' => $mapped,
        ]);
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
