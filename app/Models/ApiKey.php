<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'name',
        'key',
        'last_used_at',
    ];

    protected $hidden = [
        'key',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(Team $team, User $user, string $name): self
    {
        $key = 'sk_'.Str::random(48);

        return self::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'name' => $name,
            'key' => hash('sha256', $key),
        ]);
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public static function findByKey(string $key): ?self
    {
        return self::where('key', hash('sha256', $key))->first();
    }
}
