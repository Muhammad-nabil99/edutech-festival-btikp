<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'mode',
        'is_open',
        'start_time',
        'end_time',
        'closed_message',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Dapatkan instance pengaturan registrasi aktif (singleton row).
     */
    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'mode' => 'manual',
                'is_open' => true,
                'start_time' => null,
                'end_time' => null,
                'closed_message' => 'Pendaftaran ditutup',
            ]
        );
    }

    /**
     * Tentukan apakah pendaftaran sedang dibuka saat ini.
     */
    public function isOpen(): bool
    {
        if ($this->mode === 'manual') {
            return (bool) $this->is_open;
        }

        $now = Carbon::now();

        if ($this->start_time && $now->lt($this->start_time)) {
            return false;
        }

        if ($this->end_time && $now->gt($this->end_time)) {
            return false;
        }

        return true;
    }

    /**
     * Dapatkan kode status pendaftaran: 'open', 'not_yet_open', 'closed'.
     */
    public function getStatus(): string
    {
        if ($this->mode === 'manual') {
            return $this->is_open ? 'open' : 'closed';
        }

        $now = Carbon::now();

        if ($this->start_time && $now->lt($this->start_time)) {
            return 'not_yet_open';
        }

        if ($this->end_time && $now->gt($this->end_time)) {
            return 'closed';
        }

        return 'open';
    }

    /**
     * Dapatkan pesan status yang sesuai untuk pengguna publik.
     */
    public function getStatusMessage(): string
    {
        $status = $this->getStatus();

        if ($status === 'not_yet_open') {
            $start = $this->start_time ? $this->start_time->translatedFormat('d F Y H:i') : '-';
            $end = $this->end_time ? $this->end_time->translatedFormat('d F Y H:i') : '-';
            return "Pendaftaran dibuka pada tanggal {$start} - {$end}";
        }

        if ($status === 'closed') {
            return !empty($this->closed_message) ? $this->closed_message : 'Pendaftaran ditutup';
        }

        return 'Pendaftaran Dibuka';
    }

    /**
     * Dapatkan waktu target untuk hitung mundur (countdown timer).
     * Jika belum buka: hitung mundur ke start_time.
     * Jika sedang buka terjadwal: hitung mundur ke penutupan end_time.
     */
    public function getCountdownTarget(): ?string
    {
        if ($this->mode === 'scheduled') {
            $now = Carbon::now();
            if ($this->start_time && $now->lt($this->start_time)) {
                return $this->start_time->toIso8601String();
            }
            if ($this->end_time && $now->lt($this->end_time)) {
                return $this->end_time->toIso8601String();
            }
        }

        return null;
    }
}
