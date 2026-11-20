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
}
