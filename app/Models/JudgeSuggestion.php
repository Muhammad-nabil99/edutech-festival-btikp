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
}
