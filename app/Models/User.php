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
        'category',
        'judge_role',
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
            'judge_role' => 'integer',
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
     * Label kategori penilaian juri (GIM / MPI).
     */
    public function getCategoryLabelAttribute(): ?string
    {
        return match ($this->category) {
            'gim_edukasi' => 'GIM (Gim Edukasi)',
            'media_pembelajaran_interaktif' => 'MPI (Media Pembelajaran Interaktif)',
            default => $this->category,
        };
    }

    /**
     * Cek apakah juri merupakan akun inti (Juri 1, 2, 3) yang dilindungi dari penghapusan.
     */
    public function isCoreJudge(): bool
    {
        $coreUsernames = ['juri1', 'juri2', 'juri3', 'juri1_sultra', 'juri2_sultra', 'juri3_sultra'];
        $coreEmails = ['juri1@edutech.btikp.id', 'juri2@edutech.btikp.id', 'juri3@edutech.btikp.id'];

        return in_array(strtolower((string) $this->username), $coreUsernames, true)
            || in_array(strtolower((string) $this->email), $coreEmails, true);
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
        // 0. Cek dari peran juri (judge_role: 1, 2, 3) jika ada
        if (!empty($this->judge_role)) {
            return 'J' . $this->judge_role;
        }

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
     * Label resmi Peran Juri Rubrik Bab IV.
     */
    public function getJudgeRoleLabelAttribute(): string
    {
        $role = $this->judge_role;
        if (!$role) {
            $code = $this->judge_code;
            $role = match ($code) {
                'J1' => 1,
                'J2' => 2,
                'J3' => 3,
                default => null,
            };
        }

        return match ($role) {
            1 => 'Juri 1 (Poin 1 & 3: Pedagogis & Teknis)',
            2 => 'Juri 2 (Poin 2: Kreativitas & Visual)',
            3 => 'Juri 3 (Poin 4: Presentasi & Dampak)',
            default => 'Juri ' . substr($this->judge_code, 1),
        };
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

    /**
     * Mengecek apakah juri berwenang menilai aspek rubrik tertentu (Bab IV):
     * - Juri 1: Poin 1 (Pedagogis 30%) dan Poin 3 (Teknis 25%)
     * - Juri 2: Poin 2 (Kreativitas & Desain Visual 30%)
     * - Juri 3: Poin 4 (Presentasi & Dampak 15%)
     */
    public function canScoreAspect(int $aspectNumber): bool
    {
        $role = $this->judge_role;
        if ($role === null) {
            $code = $this->judge_code;
            $role = match ($code) {
                'J1' => 1,
                'J2' => 2,
                'J3' => 3,
                default => null,
            };
        }

        return match ($role) {
            1 => in_array($aspectNumber, [1, 3], true),
            2 => $aspectNumber === 2,
            3 => $aspectNumber === 4,
            default => true,
        };
    }
}
