<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JudgeSuggestion extends Model
{
    use HasFactory;

    // Batas maksimal saran juri per kategori
    public const MAX_SUGGESTIONS_PER_CATEGORY = 10;

    /**
     * Daftar 5 kriteria checklist alasan saran Top 10 tahap kurasi dewan juri.
     */
    public const REASONS_LIST = [
        'Karya layak secara teknis dan berfungsi dengan baik (tanpa bug/crash)',
        'Ide karya orisinal',
        'Materi pembelajaran benar dan akurat',
        'Video demonstrasi (maks. 1 menit) sudah disimak dan sesuai dengan karya',
        'File/tautan karya dapat diakses dan dibuka dengan baik',
    ];

    protected $fillable = [
        'user_id',
        'registration_id',
        'reasons',
    ];

    protected $casts = [
        'reasons' => 'array',
    ];

    /**
     * Relasi ke akun Dewan Juri.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke karya peserta yang disarankan.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    /**
     * Dapatkan kode juri (J1, J2, J3) dari relasi user.
     */
    public function getJudgeCodeAttribute(): string
    {
        if ($this->user) {
            return $this->user->judge_code;
        }

        $user = User::find($this->user_id);
        if ($user) {
            return $user->judge_code;
        }

        return 'J' . $this->user_id;
    }

    /**
     * Konfigurasi warna visual dan fokus penilaian juri.
     */
    public function getJudgeColorClassesAttribute(): array
    {
        $code = $this->judge_code;
        return match ($code) {
            'J1' => [
                'code' => 'J1',
                'colorClass' => 'bg-indigo-600 text-white',
                'color' => 'from-indigo-600 via-indigo-700 to-indigo-900',
                'badgeColor' => 'bg-indigo-500 text-white',
                'focus' => 'Gim Edukasi & Interaktivitas Media Digital',
            ],
            'J2' => [
                'code' => 'J2',
                'colorClass' => 'bg-emerald-600 text-white',
                'color' => 'from-emerald-600 via-teal-700 to-emerald-900',
                'badgeColor' => 'bg-emerald-500 text-white',
                'focus' => 'Pedagogik, Kurikulum & Substansi Materi SMA/SMK',
            ],
            'J3' => [
                'code' => 'J3',
                'colorClass' => 'bg-amber-600 text-white',
                'color' => 'from-amber-500 via-orange-600 to-amber-800',
                'badgeColor' => 'bg-amber-500 text-white',
                'focus' => 'Desain Visual, Estetika & Kualitas UI/UX',
            ],
            default => [
                'code' => $code,
                'colorClass' => 'bg-purple-600 text-white',
                'color' => 'from-purple-600 via-indigo-700 to-purple-900',
                'badgeColor' => 'bg-purple-500 text-white',
                'focus' => 'Evaluasi Inovasi Pembelajaran Digital BTIKP',
            ],
        };
    }
}
