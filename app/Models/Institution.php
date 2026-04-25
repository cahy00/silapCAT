<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $guarded = [];

    public function eventInstitutions(): HasMany
    {
        return $this->hasMany(EventInstitution::class);
    }
}
