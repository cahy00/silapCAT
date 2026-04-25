<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'string',
    ];

    public function eventInstitutions(): HasMany
    {
        return $this->hasMany(EventInstitution::class);
    }

    public function eventLocations(): HasMany
    {
        return $this->hasMany(EventLocation::class);
    }

    public function eventEmployees(): HasMany
    {
        return $this->hasMany(EventEmployee::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function procurementType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProcurementType::class);
    }
}
