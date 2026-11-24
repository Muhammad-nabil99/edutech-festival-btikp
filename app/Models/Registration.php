<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Registration extends Model
{
    use HasFactory;

    // Batas slot maksimal Top 10 per kategori
    public const MAX_TOP10_PER_CATEGORY = 10;

    protected $fillable = [
        'registration_number',
        'full_name',
        'school_name',
        'regency_city',
        'email',
        'whatsapp_number',
        'learning_category',
        'profile_photo_path',
        'media_link',
        'is_curated_top10',
        'curated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_curated_top10' => 'boolean',
            'curated_at' => 'datetime',
        ];
    }

    /**
     * Accessor yang otomatis disertakan dalam serialization JSON / JavaScript.
     */
    protected $appends = [
        'profile_photo_url',
        'category_label',
        'whatsapp_chat_url',
    ];

    /**
     * Mutator agar nama sekolah selalu disimpan dalam huruf kapital (UPPERCASE).
     */
    public function setSchoolNameAttribute(mixed $value): void
    {
        $this->attributes['school_name'] = mb_strtoupper(trim((string) $value));
    }

    /**
     * Kategori pembelajaran dalam label bahasa manusia yang rapi.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->learning_category) {
            'gim_edukasi' => 'Gim Edukasi',
            'media_pembelajaran_interaktif' => 'Media Pembelajaran Interaktif',
            default => $this->learning_category,
        };
    }

    /**
     * URL publik untuk foto profil yang selalu dapat diakses.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if (!empty($this->profile_photo_path)) {
            if (isset($this->id)) {
                return route('registration.photo', $this->id);
            }

            return asset('storage/' . ltrim($this->profile_photo_path, '/'));
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=1E40AF&color=FFFFFF&size=200';
    }

    /**
     * Format WhatsApp URL untuk click-to-chat.
     */
    public function getWhatsappChatUrlAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->whatsapp_number);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $text = rawurlencode("Halo {$this->full_name}, kami dari Panitia EduTech Festival BTIKP Sultra terkait pendaftaran nomor {$this->registration_number}.");
        return "https://wa.me/{$phone}?text={$text}";
    }

    /**
     * Relasi ke penilaian dewan juri (total/ringkasan).
     */
    public function judgeScores(): HasMany
    {
        return $this->hasMany(JudgeScore::class, 'registration_id');
    }

    /**
     * Relasi ke rincian nilai 12 subindikator instrumen rubrik Bab IV (Likert 1-5).
     */
    public function rubricScores(): HasMany
    {
        return $this->hasMany(RubricScore::class, 'registration_id');
    }

    /**
     * Cek status penilaian karya khusus untuk satu dewan juri tertentu:
     * 'Belum dinilai', 'Sebagian', atau 'Sudah dinilai'.
     */
    public function judgeWorkStatus(User $judge): string
    {
        $role = $judge->judge_role ?? ($judge->judge_code === 'J1' ? 1 : ($judge->judge_code === 'J2' ? 2 : ($judge->judge_code === 'J3' ? 3 : 0)));
        if ($role < 1 || $role > 3) {
            return 'Belum dinilai';
        }

        $config = \App\Services\RubricService::getRoleConfig($role);
        $totalItems = $config ? count($config['subindicators']) : 0;

        $filledCount = $this->rubricScores()
            ->where('jury_role', $role)
            ->whereBetween('score', [1, 5])
            ->count();

        if ($filledCount === 0) {
            // Cek fallback legacy judgeScores
            if ($this->judgeScores()->where('user_id', $judge->id)->exists()) {
                return 'Sudah dinilai';
            }
            return 'Belum dinilai';
        }

        if ($filledCount >= $totalItems && $totalItems > 0) {
            return 'Sudah dinilai';
        }

        return 'Sebagian';
    }

    /**
     * Relasi ke rekomendasi / usulan Top 10 dari dewan juri.
     */
    public function judgeSuggestions(): HasMany
    {
        return $this->hasMany(JudgeSuggestion::class, 'registration_id');
    }

    /**
     * Cek apakah karya ini disarankan oleh dewan juri tertentu.
     */
    public function isSuggestedBy(int|User $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($this->relationLoaded('judgeSuggestions')) {
            return $this->judgeSuggestions->contains('user_id', $userId);
        }

        return $this->judgeSuggestions()->where('user_id', $userId)->exists();
    }

    /**
     * Jumlah saran juri yang diperoleh karya ini.
     */
    public function getSuggestionsCountAttribute(): int
    {
        if ($this->relationLoaded('judgeSuggestions')) {
            return $this->judgeSuggestions->count();
        }

        return $this->judgeSuggestions()->count();
    }

    /**
     * Rata-rata nilai total dari seluruh juri.
     */
    public function getAverageScoreAttribute(): float
    {
        return round((float) $this->judgeScores()->avg('total_score'), 2);
    }

    /**
     * Rekap Gabungan Multi-Juri Rubrik Bab IV:
     * Menghitung nilai realtime seiring juri mengisi penilaian.
     */
    public function getMultiJudgeRecapAttribute(): array
    {
        $hasRubricScores = $this->relationLoaded('rubricScores')
            ? $this->rubricScores->isNotEmpty()
            : $this->rubricScores()->exists();

        if ($hasRubricScores) {
            $recap = \App\Services\RubricService::calculateRecap($this);
            return array_merge($recap, [
                'waiting_for' => $recap['waiting_judges'],
                'missing_judges' => $recap['waiting_judges'],
                'p1' => $recap['judges'][1]['weighted_score'] ?? null,
                'p2' => $recap['judges'][2]['weighted_score'] ?? null,
                'p3' => $recap['judges'][1]['weighted_score'] ?? null,
                'p4' => $recap['judges'][3]['weighted_score'] ?? null,
                'j1_name' => $recap['judges'][1]['judge_name'] ?? 'Juri 1',
                'j2_name' => $recap['judges'][2]['judge_name'] ?? 'Juri 2',
                'j3_name' => $recap['judges'][3]['judge_name'] ?? 'Juri 3',
                'weighted_total' => $recap['final_score'] ?? $recap['interim_score'],
                'average_score' => $recap['final_score'] ? round($recap['final_score'] / 3, 2) : round($recap['interim_score'] / 3, 2),
            ]);
        }

        // Fallback jika belum ada rubric_scores (legacy test data)
        $scores = $this->relationLoaded('judgeScores') ? $this->judgeScores : $this->judgeScores()->with('judge')->get();

        $scoreJ1 = $scores->first(fn ($s) => $s->judge && ($s->judge->judge_role === 1 || $s->judge->judge_code === 'J1'));
        $scoreJ2 = $scores->first(fn ($s) => $s->judge && ($s->judge->judge_role === 2 || $s->judge->judge_code === 'J2'));
        $scoreJ3 = $scores->first(fn ($s) => $s->judge && ($s->judge->judge_role === 3 || $s->judge->judge_code === 'J3'));

        $p1 = ($scoreJ1 && $scoreJ1->pedagogical_score >= 1) ? (float) $scoreJ1->pedagogical_score : null;
        $p3 = ($scoreJ1 && $scoreJ1->technical_score >= 1) ? (float) $scoreJ1->technical_score : null;
        $p2 = ($scoreJ2 && $scoreJ2->creativity_score >= 1) ? (float) $scoreJ2->creativity_score : null;
        $p4 = ($scoreJ3 && $scoreJ3->presentation_score >= 1) ? (float) $scoreJ3->presentation_score : null;

        $missing = [];
        if ($p1 === null || $p3 === null) {
            $missing[] = 'Juri 1';
        }
        if ($p2 === null) {
            $missing[] = 'Juri 2';
        }
        if ($p4 === null) {
            $missing[] = 'Juri 3';
        }

        $isComplete = count($missing) === 0;

        $weightedTotal = null;
        $averageScore = null;
        if ($isComplete) {
            $weightedTotal = round(($p1 * 0.30) + ($p2 * 0.30) + ($p3 * 0.25) + ($p4 * 0.15), 2);
            $averageScore = round(($p1 + $p2 + $p3 + $p4) / 4, 2);
        }

        return [
            'is_complete' => $isComplete,
            'waiting_for' => $missing,
            'missing_judges' => $missing,
            'waiting_judges' => $missing,
            'status_label' => $isComplete ? 'Nilai Akhir' : 'Nilai Sementara',
            'waiting_text' => count($missing) > 0 ? ('Menunggu: ' . implode(', ', $missing)) : null,
            'interim_score' => $weightedTotal ?? 0.0,
            'final_score' => $weightedTotal,
            'display_score' => number_format($weightedTotal ?? 0.0, 2, '.', ''),
            'p1' => $p1,
            'p2' => $p2,
            'p3' => $p3,
            'p4' => $p4,
            'j1_name' => $scoreJ1?->judge?->name ?? 'Juri 1',
            'j2_name' => $scoreJ2?->judge?->name ?? 'Juri 2',
            'j3_name' => $scoreJ3?->judge?->name ?? 'Juri 3',
            'j1_score_id' => $scoreJ1?->id,
            'j2_score_id' => $scoreJ2?->id,
            'j3_score_id' => $scoreJ3?->id,
            'score_j1' => $scoreJ1,
            'score_j2' => $scoreJ2,
            'score_j3' => $scoreJ3,
            'weighted_total' => $weightedTotal,
            'average_score' => $averageScore,
        ];
    }

    /**
     * Generate nomor registrasi otomatis dengan format EDUTECH-2026-XXXX.
     */
    public static function generateRegistrationNumber(): string
    {
        $year = date('Y');
        $lastRegistration = self::whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastRegistration && preg_match('/EDUTECH-\d{4}-(\d+)/', $lastRegistration->registration_number, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return sprintf('EDUTECH-%s-%04d', $year, $nextNumber);
    }
}
