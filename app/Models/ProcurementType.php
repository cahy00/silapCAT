<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementType extends Model
{
    protected $guarded = [];

    public function procurementCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProcurementCategory::class);
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Event::class);
    }
}
