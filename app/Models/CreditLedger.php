<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'type',
        'delta',
        'reason',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeText($query)
    {
        return $query->where('type', 'text');
    }

    public function scopeImage($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeDebits($query)
    {
        return $query->where('delta', '<', 0);
    }

    public function scopeCredits($query)
    {
        return $query->where('delta', '>', 0);
    }

    public function isDebit(): bool
    {
        return $this->delta < 0;
    }

    public function isCredit(): bool
    {
        return $this->delta > 0;
    }
}
