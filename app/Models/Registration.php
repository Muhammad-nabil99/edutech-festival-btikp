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
     * Relasi ke penilaian dewan juri.
     */
    public function judgeScores(): HasMany
    {
        return $this->hasMany(JudgeScore::class, 'registration_id');
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
