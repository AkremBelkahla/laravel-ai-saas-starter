<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'type',
        'status',
        'prompt',
        'options',
        'result',
        'tokens_used',
        'estimated_cost',
        'actual_cost',
        'model',
        'error',
        'completed_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'result' => 'array',
            'meta' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeText($query)
    {
        return $query->where('type', 'text');
    }

    public function scopeImage($query)
    {
        return $query->where('type', 'image');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
}
