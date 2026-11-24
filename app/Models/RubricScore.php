<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RubricScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'user_id',
        'jury_role',
        'subindicator_index',
        'score',
    ];

    protected $casts = [
        'jury_role' => 'integer',
        'subindicator_index' => 'integer',
        'score' => 'integer',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSubindicatorTextAttribute(): ?string
    {
        return config("rubric.roles.{$this->jury_role}.subindicators.{$this->subindicator_index}");
    }
}
