<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLocation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'holiday_dates' => 'array',
        'has_opening_day' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function eventLocationInstitutions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventLocationInstitution::class);
    }

    public function getTotalParticipantsAttribute(): int
    {
        return $this->eventLocationInstitutions()->sum('participants_count');
    }
}
