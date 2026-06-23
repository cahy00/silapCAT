<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLocationInstitution extends Model
{
    protected $guarded = [];

    protected $casts = [
        'participants_count' => 'integer',
    ];

    public function eventLocation(): BelongsTo
    {
        return $this->belongsTo(EventLocation::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
