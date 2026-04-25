<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Location extends Model
{
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
