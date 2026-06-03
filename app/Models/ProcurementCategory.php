<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProcurementCategory extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('procurement_category');
    }

    protected $guarded = [];

    public function procurementTypes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProcurementType::class);
    }
}
