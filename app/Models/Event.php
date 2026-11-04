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
}
