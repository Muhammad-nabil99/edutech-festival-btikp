<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JudgeScore extends Model
{
    use HasFactory;

    // Bobot penilaian sesuai Rubrik Bab IV
    public const WEIGHT_PEDAGOGICAL = 0.30;
    public const WEIGHT_CREATIVITY = 0.30;
    public const WEIGHT_TECHNICAL = 0.25;
    public const WEIGHT_PRESENTATION = 0.15;

    protected $fillable = [
        'user_id',
        'registration_id',
        'pedagogical_score',
        'creativity_score',
        'technical_score',
        'presentation_score',
        'total_score',
        'notes',
    ];

    protected $casts = [
        'pedagogical_score' => 'float',
        'creativity_score' => 'float',
        'technical_score' => 'float',
        'presentation_score' => 'float',
        'total_score' => 'float',
    ];

    /**
     * Hitung total nilai berbobot secara otomatis.
     */
    public static function computeWeightedTotal(
        float $pedagogical,
        float $creativity,
        float $technical,
        float $presentation
    ): float {
        $total = ($pedagogical * self::WEIGHT_PEDAGOGICAL)
               + ($creativity * self::WEIGHT_CREATIVITY)
               + ($technical * self::WEIGHT_TECHNICAL)
               + ($presentation * self::WEIGHT_PRESENTATION);

        return round($total, 2);
    }

    /**
     * Relasi ke Juri (User).
     */
    public function judge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Karya Pendaftar (Registration).
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
}
