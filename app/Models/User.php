<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user adalah administrator / panitia.
     */
    public function isAdmin(): bool
    {
        return ($this->role ?? 'admin') === 'admin';
    }

    /**
     * Cek apakah user adalah dewan juri.
     */
    public function isJudge(): bool
    {
        return ($this->role ?? '') === 'judge';
    }

    /**
     * Relasi ke daftar penilaian juri.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(JudgeScore::class, 'user_id');
    }

    /**
     * Relasi ke daftar rekomendasi / saran Top 10 dari juri.
     */
    public function suggestions(): HasMany
    {
        return $this->hasMany(JudgeSuggestion::class, 'user_id');
    }

    /**
     * Kode/Inisial Juri (J1, J2, J3) untuk avatar circle.
     */
    public function getJudgeCodeAttribute(): string
    {
        // 1. Cek dari username (misal: juri1, juri_1, judge1)
        if (preg_match('/(\d+)/', (string) $this->username, $m)) {
            return 'J' . $m[1];
        }

        // 2. Cek dari email (misal: juri1@edutech.btikp.id)
        if (preg_match('/(?:juri|judge)?(\d+)/i', (string) $this->email, $m)) {
            return 'J' . $m[1];
        }

        // 3. Cek dari nama (misal: Dewan Juri 1 (Gim & Multimedia), Juri 1)
        if (preg_match('/(?:Juri|Judge|J)\s*(\d+)/i', (string) $this->name, $m)) {
            return 'J' . $m[1];
        }

        // 4. Fallback urutan berdasarkan ID di antara akun role judge
        // Juri pertama = J1, Juri kedua = J2, Juri ketiga = J3
        // Ini mencegah juri pertama keliru menjadi 'J2' karena ID=1 digunakan Admin BTIKP
        try {
            static $judgeIndexCache = null;
            if ($judgeIndexCache === null) {
                $judgeIndexCache = static::where('role', 'judge')
                    ->orderBy('id', 'asc')
                    ->pluck('id')
                    ->flip()
                    ->all();
            }

            if (isset($judgeIndexCache[$this->id])) {
                return 'J' . ($judgeIndexCache[$this->id] + 1);
            }
        } catch (\Throwable $e) {
            // Abaikan jika database query belum siap
        }

        return 'J' . $this->id;
    }

    /**
     * Warna khas tiap juri untuk avatar badge.
     */
    public function getJudgeColorClassesAttribute(): array
    {
        $code = $this->judge_code;
        return match ($code) {
            'J1' => [
                'bg' => 'bg-indigo-600',
                'bg_light' => 'bg-indigo-50',
                'border' => 'border-indigo-300',
                'text' => 'text-indigo-700',
                'ring' => 'ring-indigo-400',
            ],
            'J2' => [
                'bg' => 'bg-emerald-600',
                'bg_light' => 'bg-emerald-50',
                'border' => 'border-emerald-300',
                'text' => 'text-emerald-700',
                'ring' => 'ring-emerald-400',
            ],
            'J3' => [
                'bg' => 'bg-amber-600',
                'bg_light' => 'bg-amber-50',
                'border' => 'border-amber-300',
                'text' => 'text-amber-700',
                'ring' => 'ring-amber-400',
            ],
            default => [
                'bg' => 'bg-purple-600',
                'bg_light' => 'bg-purple-50',
                'border' => 'border-purple-300',
                'text' => 'text-purple-700',
                'ring' => 'ring-purple-400',
            ],
        };
    }
}
