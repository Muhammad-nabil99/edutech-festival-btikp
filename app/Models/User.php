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
        if (preg_match('/(\d+)/', (string) $this->username, $m)) {
            return 'J' . $m[1];
        }

        if (preg_match('/Juri\s*(\d+)/i', (string) $this->name, $m)) {
            return 'J' . $m[1];
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
