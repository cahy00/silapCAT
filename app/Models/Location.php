<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\NotifiesOnCreate;

class Location extends Model
{
    use LogsActivity, NotifiesOnCreate;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('location');
    }

    protected $guarded = [];

    public function eventLocations(): HasMany
    {
        return $this->hasMany(EventLocation::class);
    }

    public function locationSurvey(): HasOne
    {
        return $this->hasOne(LocationSurvey::class);
    }
}
