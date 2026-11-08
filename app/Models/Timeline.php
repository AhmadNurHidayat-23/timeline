<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Timeline extends Model
{
    protected $fillable = ['name', 'description', 'sort_mode'];

    protected static function booted(): void
    {
        static::creating(function (Timeline $timeline) {
            $timeline->share_token = Str::random(64);
        });
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function orderedEvents(): HasMany
    {
        $query = $this->events();

        return $this->sort_mode === 'manual'
            ? $query->orderBy('position')->orderBy('id')
            : $query->orderBy('event_date')->orderBy('position')->orderBy('id');
    }

    public function isManualSort(): bool
    {
        return $this->sort_mode === 'manual';
    }

    public function isChronologicalSort(): bool
    {
        return $this->sort_mode === 'chronological';
    }
}
