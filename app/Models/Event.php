<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = ['title', 'event_date', 'description', 'position'];

    protected $touches = ['timeline'];

    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }

    public function timeline(): BelongsTo
    {
        return $this->belongsTo(Timeline::class);
    }

    public function isPast(): bool
    {
        return $this->event_date->isPast();
    }

    public function isUpcoming(): bool
    {
        return $this->event_date->isFuture() || $this->event_date->isToday();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now()->toDateString());
    }
}
