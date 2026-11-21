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

    protected $fillable = [
        'user_id',
        'registration_id',
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
