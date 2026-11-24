<?php

namespace App\Http\Controllers;

use App\Models\JudgeScore;
use App\Models\Registration;
use App\Models\RegistrationSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard statistik dan manajemen data pendaftar.
     */
    public function index(Request $request): View
    {
        $query = Registration::query();

        // Filter Tab Cepat: 'all', 'top10', 'top10_gim', 'top10_media'
        $tab = $request->input('tab', 'all');

        if ($tab === 'top10') {
            $query->where('is_curated_top10', true);
        } elseif ($tab === 'top10_gim') {
            $query->where('is_curated_top10', true)
                  ->where('learning_category', 'gim_edukasi');
        } elseif ($tab === 'top10_media') {
            $query->where('is_curated_top10', true)
                  ->where('learning_category', 'media_pembelajaran_interaktif');
        }

        // Filter Status Kurasi Top 10 via select box
        if ($curatedFilter = $request->input('curated')) {
            if ($curatedFilter === 'top10_only') {
                $query->where('is_curated_top10', true);
            } elseif ($curatedFilter === 'regular_only') {
                $query->where('is_curated_top10', false);
            }
        }

        // Filter Pencarian kata kunci
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('school_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        // Filter Kategori Pembelajaran
        if ($category = $request->input('category')) {
            if (in_array($category, ['gim_edukasi', 'media_pembelajaran_interaktif'])) {
                $query->where('learning_category', $category);
            }
        }

        // Filter Kabupaten / Kota
        if ($regency = $request->input('regency')) {
            if (in_array($regency, RegistrationController::REGION_LIST)) {
                $query->where('regency_city', $regency);
            }
        }

        // Filter Rekomendasi / Saran Dewan Juri
        if ($suggestionFilter = $request->input('suggestion_filter')) {
            if ($suggestionFilter === 'suggested_3') {
                $query->has('judgeSuggestions', '=', 3);
            } elseif ($suggestionFilter === 'suggested_any') {
                $query->has('judgeSuggestions', '>=', 1);
            } elseif ($suggestionFilter === 'no_suggestion') {
                $query->doesntHave('judgeSuggestions');
            }
        }

        // Pengurutan (Sorting)
        $sortBy = $request->input('sort_by', 'latest');
        if ($sortBy === 'most_suggested') {
            $query->withCount('judgeSuggestions')
                  ->orderByDesc('judge_suggestions_count')
                  ->latest('id');
        } elseif ($sortBy === 'oldest') {
            $query->withCount('judgeSuggestions')->oldest('id');
        } else {
            $query->withCount('judgeSuggestions')->latest('id');
        }

        $registrations = $query->with(['judgeScores.judge', 'judgeSuggestions.user'])
            ->paginate(12)
            ->withQueryString();

        // Data Statistik Utama
        $stats = [
            'total' => Registration::count(),
            'gim_edukasi' => Registration::where('learning_category', 'gim_edukasi')->count(),
            'media_interaktif' => Registration::where('learning_category', 'media_pembelajaran_interaktif')->count(),
            'distinct_regions' => Registration::distinct('regency_city')->count('regency_city'),
            'top10_gim' => Registration::where('learning_category', 'gim_edukasi')->where('is_curated_top10', true)->count(),
            'top10_media' => Registration::where('learning_category', 'media_pembelajaran_interaktif')->where('is_curated_top10', true)->count(),
            'top10_total' => Registration::where('is_curated_top10', true)->count(),
        ];

        // Daftar Karya Top 10 Terpilih beserta Rekap Penilaian Juri & Peringkat per Kategori
        $top10Works = Registration::where('is_curated_top10', true)
            ->with(['rubricScores.user', 'judgeScores.judge'])
            ->get();

        $rankedGim = \App\Services\RubricService::getRankedWorks('gim_edukasi');
        $rankedMpi = \App\Services\RubricService::getRankedWorks('media_pembelajaran_interaktif');

        $gimRankMap = collect($rankedGim)->pluck('category_rank', 'id')->all();
        $mpiRankMap = collect($rankedMpi)->pluck('category_rank', 'id')->all();

        $top10Works = $top10Works->map(function ($work) use ($gimRankMap, $mpiRankMap) {
            $recap = \App\Services\RubricService::calculateRecap($work);
            $work->recap = $recap;
            $work->multi_recap = $recap;
            $work->category_rank = ($work->learning_category === 'gim_edukasi')
                ? ($gimRankMap[$work->id] ?? null)
                : ($mpiRankMap[$work->id] ?? null);
            $work->calculated_avg_score = $recap['is_complete'] ? $recap['final_score'] : ($recap['interim_score'] ?? 0.0);
            return $work;
        })
        ->sortBy([
            fn ($a, $b) => ($a->category_rank === null) <=> ($b->category_rank === null),
            ['category_rank', 'asc'],
            ['calculated_avg_score', 'desc'],
        ])
        ->values();

        // Pengaturan Pendaftaran & Daftar Dewan Juri
        $registrationSetting = RegistrationSetting::current();
        $judges = User::where('role', 'judge')->orderBy('id', 'asc')->get();

        return view('admin.dashboard', [
            'registrations' => $registrations,
            'stats' => $stats,
            'top10Works' => $top10Works,
            'registrationSetting' => $registrationSetting,
            'judges' => $judges,
            'regions' => RegistrationController::REGION_LIST,
            'filters' => [
                'tab' => $tab,
                'search' => $request->input('search', ''),
                'category' => $request->input('category', ''),
                'regency' => $request->input('regency', ''),
                'curated' => $request->input('curated', ''),
                'suggestion_filter' => $request->input('suggestion_filter', ''),
                'sort_by' => $sortBy,
            ],
        ]);
    }

    /**
     * Toggle status seleksi Top 10 karya terbaik (Maksimal 10 karya per kategori).
     */
    public function toggleTop10(Request $request, Registration $registration): JsonResponse|RedirectResponse
    {
        $category = $registration->learning_category;
        $categoryLabel = $registration->category_label;

        // Jika saat ini belum terpilih dan akan dimasukkan ke Top 10
        if (!$registration->is_curated_top10) {
            $currentCount = Registration::where('learning_category', $category)
                ->where('is_curated_top10', true)
                ->count();

            if ($currentCount >= Registration::MAX_TOP10_PER_CATEGORY) {
                $errorMsg = "Slot Top 10 untuk kategori {$categoryLabel} sudah penuh (maksimal " . Registration::MAX_TOP10_PER_CATEGORY . " karya).";

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg,
                    ], 422);
                }

                return back()->with('error', $errorMsg);
            }

            $registration->is_curated_top10 = true;
            $registration->curated_at = now();
            $registration->save();

            $successMsg = "Karya '{$registration->full_name}' berhasil dipilih masuk ke Top 10 {$categoryLabel}!";
        } else {
            // Batalkan dari Top 10 (kembali ke pendaftar reguler)
            $registration->is_curated_top10 = false;
            $registration->curated_at = null;
            $registration->save();

            $successMsg = "Karya '{$registration->full_name}' dikeluarkan dari Top 10 {$categoryLabel}.";
        }

        // Hitung slot terkini
        $slotStats = [
            'gim_count' => Registration::where('learning_category', 'gim_edukasi')->where('is_curated_top10', true)->count(),
            'media_count' => Registration::where('learning_category', 'media_pembelajaran_interaktif')->where('is_curated_top10', true)->count(),
            'total_count' => Registration::where('is_curated_top10', true)->count(),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'is_curated' => $registration->is_curated_top10,
                'slots' => $slotStats,
            ]);
        }

        return back()->with('status', $successMsg);
    }

    /**
     * Simpan pembaruan pengaturan status pendaftaran (Manual / Terjadwal).
     */
    public function updateRegistrationSetting(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:manual,scheduled'],
            'is_open' => ['nullable', 'boolean'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date'],
            'closed_message' => ['nullable', 'string', 'max:500'],
        ]);

        $setting = RegistrationSetting::current();
        $setting->mode = $validated['mode'];
        $setting->is_open = $request->boolean('is_open');
        $setting->start_time = !empty($validated['start_time']) ? $validated['start_time'] : null;
        $setting->end_time = !empty($validated['end_time']) ? $validated['end_time'] : null;
        $setting->closed_message = $validated['closed_message'] ?? 'Pendaftaran ditutup';
        $setting->save();

        $message = 'Pengaturan jadwal pendaftaran berhasil diperbarui!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $setting->getStatus(),
                'status_message' => $setting->getStatusMessage(),
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Administrator menambahkan akun dewan juri baru.
     */
    public function storeJudge(Request $request): RedirectResponse|JsonResponse
    {
        // Bersihkan dan format username serta email sebelum validasi
        if ($request->filled('username')) {
            $request->merge([
                'username' => strtolower(trim(str_replace(' ', '', (string) $request->input('username')))),
            ]);
        }
        if ($request->filled('email')) {
            $request->merge([
                'email' => strtolower(trim((string) $request->input('email'))),
            ]);
        }
        if (!$request->filled('judge_role')) {
            $category = $request->input('category');
            $existingRoles = User::where('role', 'judge')
                ->where('category', $category)
                ->whereNotNull('judge_role')
                ->pluck('judge_role')
                ->toArray();
            $availableRole = 1;
            for ($r = 1; $r <= 3; $r++) {
                if (!in_array($r, $existingRoles)) {
                    $availableRole = $r;
                    break;
                }
            }
            $request->merge(['judge_role' => $availableRole]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'category' => ['required', 'string', 'in:gim_edukasi,media_pembelajaran_interaktif'],
            'judge_role' => [
                'required',
                'integer',
                'in:1,2,3',
                Rule::unique('users', 'judge_role')->where(function ($q) use ($request) {
                    return $q->where('role', 'judge')
                             ->where('category', $request->input('category'));
                }),
            ],
        ], [
            'name.required' => 'Nama dewan juri wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), atau garis bawah (_).',
            'username.unique' => 'Username tersebut sudah digunakan oleh akun lain.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email tersebut sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'category.required' => 'Pilih kategori penilaian juri (GIM atau MPI).',
            'category.in' => 'Kategori penilaian yang dipilih tidak valid.',
            'judge_role.required' => 'Pilih peran juri (Juri 1, Juri 2, atau Juri 3).',
            'judge_role.in' => 'Peran juri yang dipilih tidak valid.',
            'judge_role.unique' => 'Peran juri tersebut sudah digunakan oleh akun lain dalam kategori ini.',
        ]);

        $judge = User::create([
            'name' => trim($validated['name']),
            'username' => trim($validated['username']),
            'email' => strtolower(trim($validated['email'])),
            'role' => 'judge',
            'category' => $validated['category'],
            'judge_role' => (int) $validated['judge_role'],
            'password' => Hash::make($validated['password']),
        ]);

        $message = "Akun Dewan Juri '{$judge->name}' berhasil ditambahkan!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'judge' => $judge,
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'judges'])->with('status', $message);
    }

    /**
     * Administrator memperbarui akun juri (Username, Email, Kategori, Password).
     */
    public function updateJudge(Request $request, User $judge): RedirectResponse|JsonResponse
    {
        if ($request->filled('username')) {
            $request->merge([
                'username' => strtolower(trim(str_replace(' ', '', (string) $request->input('username')))),
            ]);
        }
        if ($request->filled('email')) {
            $request->merge([
                'email' => strtolower(trim((string) $request->input('email'))),
            ]);
        }
        if (!$request->filled('judge_role')) {
            $request->merge(['judge_role' => $judge->judge_role ?? 1]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($judge->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($judge->id)],
            'category' => ['nullable', 'string', 'in:gim_edukasi,media_pembelajaran_interaktif'],
            'judge_role' => [
                'nullable',
                'integer',
                'in:1,2,3',
                Rule::unique('users', 'judge_role')->where(function ($q) use ($request, $judge) {
                    $cat = $request->input('category', $judge->category);
                    return $q->where('role', 'judge')->where('category', $cat);
                })->ignore($judge->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip (-), atau garis bawah (_).',
            'username.unique' => 'Username tersebut sudah digunakan oleh akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email tersebut sudah terdaftar untuk akun lain.',
            'judge_role.in' => 'Peran juri yang dipilih tidak valid.',
            'judge_role.unique' => 'Peran juri tersebut sudah digunakan oleh akun lain dalam kategori ini.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'category.in' => 'Kategori penilaian yang dipilih tidak valid.',
        ]);

        $judge->name = trim($validated['name']);
        $judge->username = trim($validated['username']);
        $judge->email = strtolower(trim($validated['email']));

        if (!empty($validated['category'])) {
            $judge->category = $validated['category'];
        }

        if (isset($validated['judge_role']) && !empty($validated['judge_role'])) {
            $judge->judge_role = (int) $validated['judge_role'];
        }

        if (!empty($validated['password'])) {
            $judge->password = Hash::make($validated['password']);
        }

        $judge->save();

        $message = "Akun Dewan Juri '{$judge->name}' berhasil diperbarui!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'judge' => [
                    'id' => $judge->id,
                    'name' => $judge->name,
                    'username' => $judge->username,
                    'email' => $judge->email,
                    'category' => $judge->category,
                    'category_label' => $judge->category_label,
                    'judge_role' => $judge->judge_role,
                    'judge_role_label' => $judge->judge_role_label,
                ],
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'judges'])->with('status', $message);
    }

    /**
     * Administrator mereset penilaian dari juri tertentu pada karya finalis.
     */
    public function resetJudgeScore(Request $request, Registration $registration, User $judge): RedirectResponse|JsonResponse
    {
        \App\Models\RubricScore::where('user_id', $judge->id)
            ->where('registration_id', $registration->id)
            ->delete();

        JudgeScore::where('user_id', $judge->id)
            ->where('registration_id', $registration->id)
            ->delete();

        $message = "Nilai dari {$judge->name} untuk karya '{$registration->full_name}' berhasil direset.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'scores'])->with('status', $message);
    }

    /**
     * Administrator menghapus akun dewan juri (Akun inti Juri 1, 2, 3 dilindungi).
     */
    public function destroyJudge(Request $request, User $judge): RedirectResponse|JsonResponse
    {
        if ($judge->isCoreJudge()) {
            abort(403, 'Akun Dewan Juri inti (Juri 1, 2, 3) dilindungi dan tidak dapat dihapus.');
        }

        $judgeName = $judge->name;

        // Hapus relasi penilaian dan saran yang pernah diberikan juri ini
        $judge->scores()->delete();
        $judge->suggestions()->delete();
        $judge->delete();

        $message = "Akun Dewan Juri '{$judgeName}' berhasil dihapus.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'judges'])->with('status', $message);
    }

    /**
     * Ekspor data pendaftar ke file CSV (dapat langsung dibuka di Excel).
     */
    public function export(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'all'); // 'all' atau 'top10'
        $fileName = ($type === 'top10' ? 'top10_karya_terbaik_edutech_' : 'rekap_pendaftar_edutech_btikp_') . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($type) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk kompatibilitas Microsoft Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header Kolom CSV
            fputcsv($handle, [
                'No. Registrasi',
                'Status Top 10',
                'Nama Lengkap',
                'Asal Sekolah',
                'Kabupaten / Kota',
                'Email',
                'Nomor WhatsApp',
                'Kategori Karya',
                'Nilai Rata-rata Juri',
                'Link Media Karya',
                'Tanggal Pendaftaran',
            ], ';');

            $query = Registration::with('judgeScores')->orderBy('id', 'asc');
            if ($type === 'top10') {
                $query->where('is_curated_top10', true);
            }

            $query->chunk(200, function ($batch) use ($handle) {
                foreach ($batch as $row) {
                    $scores = $row->judgeScores;
                    $avgScore = $scores->count() > 0 ? round($scores->avg('total_score'), 2) : '-';

                    fputcsv($handle, [
                        $row->registration_number,
                        $row->is_curated_top10 ? 'TOP 10 TERPILIH' : 'Pendaftar Reguler',
                        $row->full_name,
                        $row->school_name,
                        $row->regency_city,
                        $row->email,
                        "'" . $row->whatsapp_number,
                        $row->category_label,
                        $avgScore,
                        $row->media_link,
                        $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '-',
                    ], ';');
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Hapus data pendaftaran.
     */
    public function destroy(Request $request, Registration $registration): \Symfony\Component\HttpFoundation\Response
    {
        if ($registration->profile_photo_path && Storage::disk('public')->exists($registration->profile_photo_path)) {
            Storage::disk('public')->delete($registration->profile_photo_path);
        }

        $regNum = $registration->registration_number;
        $registration->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data pendaftaran {$regNum} berhasil dihapus.",
            ]);
        }

        return back()->with('status', "Data pendaftaran {$regNum} berhasil dihapus.");
    }
}
