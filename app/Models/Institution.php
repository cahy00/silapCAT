<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\NotifiesOnCreate;

class Institution extends Model
{
    use LogsActivity, NotifiesOnCreate;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('institution');
    }
    protected $guarded = [];

    public function eventInstitutions(): HasMany
    {
        return $this->hasMany(EventInstitution::class);
    }
}
