<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\NotifiesOnCreate;

class Event extends Model
{
    use LogsActivity, NotifiesOnCreate;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('event');
    }

    protected $guarded = [];

    protected $casts = [
        'status' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function getStatusAttribute($value)
    {
        if ($this->start_date && $this->end_date) {
            $now = now()->startOfDay();
            $start = \Carbon\Carbon::parse($this->start_date)->startOfDay();
            $end = \Carbon\Carbon::parse($this->end_date)->startOfDay();

            if ($now->between($start, $end)) {
                return 'aktif';
            } elseif ($now->gt($end)) {
                return 'selesai';
            }
        }

        return $value;
    }

    protected $attributes = [
        'certificate_template' => 'sertifikat_default.pptx',
    ];

    public function getCertificateTemplateAttribute($value)
    {
        return $value ?: 'sertifikat_default.pptx';
    }

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


    public function examScores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamScore::class);
    }

    public function delegations(): HasMany
    {
        return $this->hasMany(EventDelegation::class);
    }
}


