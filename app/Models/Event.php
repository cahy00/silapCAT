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
        'documentation_photos' => 'array',
    ];

    public function getStatusAttribute($value)
    {
        return \App\Enums\EventStatus::resolve($value, $this->start_date, $this->end_date);
    }

    protected static function booted(): void
    {
        // Simpan status efektif ke DB agar query where('status', ...) konsisten dengan tampilan.
        static::saving(function (Event $event) {
            $event->attributes['status'] = \App\Enums\EventStatus::resolve(
                $event->attributes['status'] ?? null,
                $event->start_date,
                $event->end_date,
            );
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return \App\Enums\EventStatus::tryFrom((string) $this->status)?->getLabel() ?? (string) $this->status;
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


