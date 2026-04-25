<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationSurvey extends Model
{
    protected $guarded = [];

    protected $casts = [
        'photos' => 'array',
        'surveyor_name' => 'array',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
