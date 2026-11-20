<?php

namespace App\Http\Controllers;

use App\Models\JudgeScore;
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
     * Tampilkan daftar karya Top 10 terbaik untuk dinilai oleh dewan juri.
     */
    public function index(Request $request): View
    {
        /** @var User $judge */
        $judge = Auth::user();

        $category = $request->input('category', 'all');

        $query = Registration::where('is_curated_top10', true)
            ->with(['judgeScores' => function ($q) use ($judge) {
                $q->where('user_id', $judge->id);
            }]);

        if ($category === 'gim_edukasi' || $category === 'media_pembelajaran_interaktif') {
            $query->where('learning_category', $category);
        }

        $works = $query->orderBy('learning_category', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Statistik Penilaian Juri Ini
        $totalCurated = Registration::where('is_curated_top10', true)->count();
        $scoredCount = JudgeScore::where('user_id', $judge->id)
            ->whereHas('registration', function ($q) {
                $q->where('is_curated_top10', true);
            })->count();

        $gimCount = Registration::where('is_curated_top10', true)->where('learning_category', 'gim_edukasi')->count();
        $mediaCount = Registration::where('is_curated_top10', true)->where('learning_category', 'media_pembelajaran_interaktif')->count();

        return view('judge.dashboard', [
            'judge' => $judge,
            'works' => $works,
            'category' => $category,
            'stats' => [
                'total_curated' => $totalCurated,
                'scored_count' => $scoredCount,
                'remaining_count' => max(0, $totalCurated - $scoredCount),
                'gim_count' => $gimCount,
                'media_count' => $mediaCount,
            ],
        ]);
    }

    /**
     * Simpan atau perbarui nilai penilaian dewan juri untuk sebuah karya.
     */
    public function saveScore(Request $request, Registration $registration): JsonResponse|RedirectResponse
    {
        // Pastikan hanya karya yang masuk Top 10 yang dapat dinilai
        if (!$registration->is_curated_top10) {
            abort(403, 'Karya ini belum atau tidak masuk dalam nominasi Top 10.');
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
