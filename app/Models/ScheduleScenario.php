<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleScenario extends Model
{
    protected $fillable = [
        'name',
        'event_name',
        'formation_year',
        'procurement_type_id',
        'description',
        'items_data',
        'results_data',
        'summary_meta',
    ];

    protected $casts = [
        'formation_year' => 'integer',
        'procurement_type_id' => 'integer',
        'items_data' => 'array',
        'results_data' => 'array',
        'summary_meta' => 'array',
    ];

    public function procurementType(): BelongsTo
    {
        return $this->belongsTo(ProcurementType::class);
    }
}
