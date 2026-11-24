<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\RubricScore;
use App\Models\User;

class RubricService
{
    /**
     * Skala Likert 1-5 dan keterangannya.
     */
    public static function getScale(): array
    {
        return config('rubric.scale', [
            1 => 'Sangat Kurang',
            2 => 'Kurang',
            3 => 'Cukup',
            4 => 'Baik',
            5 => 'Sangat Baik',
        ]);
    }

    /**
     * Konfigurasi seluruh instrumen rubrik.
     */
    public static function getRoles(): array
    {
        return config('rubric.roles', []);
    }

    /**
     * Konfigurasi instrumen peran juri tertentu (1, 2, atau 3).
     */
    public static function getRoleConfig(int $role): ?array
    {
        return config("rubric.roles.{$role}");
    }

    /**
     * Menghitung nilai satu juri berdasarkan skor subindikator yang diisi.
     * Rumus: Nilai Juri = (Total skor / Skor maksimal juri) x Bobot juri
     */
    public static function calculateRoleScore(int $role, array $subindicatorScores): array
    {
        $config = self::getRoleConfig($role);
        if (!$config) {
            return [
                'role' => $role,
                'total_score' => 0,
                'max_score' => 0,
                'weight' => 0,
                'weighted_score' => 0.0,
                'is_complete' => false,
                'items_count' => 0,
                'filled_count' => 0,
                'progress_text' => '0/0 terisi',
                'scores' => [],
            ];
        }

        $items = $config['subindicators'];
        $itemsCount = count($items);
        $filledCount = 0;
        $totalScore = 0;
        $cleanScores = [];

        foreach ($items as $idx => $label) {
            $val = isset($subindicatorScores[$idx]) ? (int) $subindicatorScores[$idx] : null;
            if ($val !== null && $val >= 1 && $val <= 5) {
                $cleanScores[$idx] = $val;
                $totalScore += $val;
                $filledCount++;
            } else {
                $cleanScores[$idx] = null;
            }
        }

        $isComplete = ($filledCount === $itemsCount && $itemsCount > 0);
        $maxScore = (int) $config['max_score'];
        $weight = (int) $config['weight'];

        // Nilai tertimbang hanya dihitung penuh jika SEMUA subindikator sudah terisi
        $weightedScore = 0.0;
        if ($isComplete && $maxScore > 0) {
            $weightedScore = round(($totalScore / $maxScore) * $weight, 2);
        }

        return [
            'role' => $role,
            'name' => $config['name'],
            'aspect' => $config['aspect'],
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'weight' => $weight,
            'weighted_score' => $weightedScore,
            'is_complete' => $isComplete,
            'items_count' => $itemsCount,
            'filled_count' => $filledCount,
            'progress_text' => "{$filledCount}/{$itemsCount} terisi",
            'scores' => $cleanScores,
        ];
    }

    /**
     * Hitung rekap nilai lengkap multi-juri untuk satu karya.
     */
    public static function calculateRecap(Registration $registration): array
    {
        $rubricScores = $registration->relationLoaded('rubricScores')
            ? $registration->rubricScores
            : $registration->rubricScores()->with('user')->get();

        $scoresByRole = [];
        $usersByRole = [];

        foreach ($rubricScores as $rs) {
            $scoresByRole[$rs->jury_role][$rs->subindicator_index] = $rs->score;
            if (!isset($usersByRole[$rs->jury_role]) && $rs->user) {
                $usersByRole[$rs->jury_role] = $rs->user;
            }
        }

        // Cari juga juri dari database yang memiliki role dan kategori yang sama jika belum ada skor
        $categoryJudges = User::where('role', 'judge')
            ->where(function ($q) use ($registration) {
                $q->where('category', $registration->learning_category)
                  ->orWhereNull('category');
            })
            ->get();

        foreach ($categoryJudges as $cj) {
            $roleNum = $cj->judge_role ?? ($cj->judge_code === 'J1' ? 1 : ($cj->judge_code === 'J2' ? 2 : ($cj->judge_code === 'J3' ? 3 : 0)));
            if ($roleNum >= 1 && $roleNum <= 3 && !isset($usersByRole[$roleNum])) {
                $usersByRole[$roleNum] = $cj;
            }
        }

        $judgesResult = [];
        $waitingJudges = [];
        $completedJudgesCount = 0;
        $interimTotal = 0.0;

        for ($r = 1; $r <= 3; $r++) {
            $rScores = $scoresByRole[$r] ?? [];
            $roleScore = self::calculateRoleScore($r, $rScores);
            $judgeUser = $usersByRole[$r] ?? null;

            $roleScore['judge_name'] = $judgeUser?->name ?? "Juri {$r}";
            $roleScore['judge_id'] = $judgeUser?->id;
            $roleScore['judge_username'] = $judgeUser?->username;
            $roleScore['judge_code'] = $judgeUser?->judge_code ?? "J{$r}";

            if ($roleScore['is_complete']) {
                $completedJudgesCount++;
                $interimTotal += $roleScore['weighted_score'];
            } else {
                $waitingJudges[] = "Juri {$r}";
            }

            $judgesResult[$r] = $roleScore;
        }

        $isComplete = ($completedJudgesCount === 3);
        $interimTotal = round($interimTotal, 2);
        $finalScore = $isComplete ? $interimTotal : null;

        $statusLabel = $isComplete ? 'Nilai Akhir' : 'Nilai Sementara';
        $waitingText = count($waitingJudges) > 0 ? ('Menunggu: ' . implode(', ', $waitingJudges)) : null;

        // Status per karya: 'Belum dinilai', 'Sebagian', 'Sudah dinilai'
        $totalFilledAll = array_sum(array_column($judgesResult, 'filled_count'));
        if ($isComplete) {
            $workStatus = 'Sudah dinilai';
        } elseif ($totalFilledAll > 0) {
            $workStatus = 'Sebagian';
        } else {
            $workStatus = 'Belum dinilai';
        }

        return [
            'registration_id' => $registration->id,
            'category' => $registration->learning_category,
            'is_complete' => $isComplete,
            'status_label' => $statusLabel,
            'work_status' => $workStatus,
            'waiting_judges' => $waitingJudges,
            'waiting_text' => $waitingText,
            'completed_judges_count' => $completedJudgesCount,
            'total_subindicators' => 12,
            'filled_subindicators' => $totalFilledAll,
            'progress_all' => "{$totalFilledAll}/12",
            'interim_score' => $interimTotal,
            'final_score' => $finalScore,
            'display_score' => number_format($interimTotal, 2, '.', ''),
            'judges' => $judgesResult,
            'updated_at' => $rubricScores->max('updated_at')?->toIso8601String() ?? now()->toIso8601String(),
        ];
    }

    /**
     * Hitung pemeringkatan finalis per kategori lomba (GIM / MPI).
     * Aturan Bab IV: Hanya karya yang nilainya SUDAH LENGKAP (ketiga juri telah mengisi seluruh 12 subindikator)
     * yang masuk dalam pemeringkatan resmi 1, 2, 3...
     * Karya yang belum lengkap tetap tampil dengan status 'Sementara' dan tidak mendapat peringkat final.
     */
    public static function getRankedWorks(string $category = null): array
    {
        $query = Registration::where('is_curated_top10', true)
            ->with(['rubricScores.user', 'judgeScores.user']);

        if ($category && in_array($category, ['gim_edukasi', 'media_pembelajaran_interaktif'], true)) {
            $query->where('learning_category', $category);
        }

        $works = $query->get();

        $completed = [];
        $incomplete = [];

        foreach ($works as $work) {
            $recap = self::calculateRecap($work);
            $work->multi_recap = $recap;
            $work->calculated_score = $recap['final_score'] ?? $recap['interim_score'];

            if ($recap['is_complete']) {
                $completed[] = $work;
            } else {
                $work->category_rank = null;
                $incomplete[] = $work;
            }
        }

        // Urutkan karya lengkap berdasarkan Nilai Akhir tertinggi
        usort($completed, function ($a, $b) {
            return ($b->calculated_score <=> $a->calculated_score);
        });

        // Tetapkan peringkat 1, 2, 3... pada karya lengkap
        foreach ($completed as $idx => $work) {
            $work->category_rank = $idx + 1;
        }

        return array_merge($completed, $incomplete);
    }
}
