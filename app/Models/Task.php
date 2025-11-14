<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasUuids;
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'description',
        'due_at',
        'completed_at',
    ];

    protected $appends = [
        'is_completed',
        'is_overdue',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getIsCompletedAttribute(): bool
    {
        return ! is_null($this->completed_at);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->is_completed || is_null($this->due_at)) {
            return false;
        }

        return $this->due_at->isPast();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
