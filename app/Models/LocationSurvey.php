<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class LocationSurvey extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('location_survey');
    }
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
