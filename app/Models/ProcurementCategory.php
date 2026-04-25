<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementCategory extends Model
{
    protected $guarded = [];

    public function procurementTypes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProcurementType::class);
    }
}
