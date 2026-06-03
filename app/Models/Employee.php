<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\NotifiesOnCreate;

class Employee extends Model
{
    use HasFactory, LogsActivity, NotifiesOnCreate;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('employee');
    }
    protected $guarded = [];

    protected $casts = [
        'status' => 'array',
    ];

    public function eventEmployees(): HasMany
    {
        return $this->hasMany(EventEmployee::class);
    }
}
