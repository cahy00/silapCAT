<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $table = 'reports';
    protected $fillable = 
    [
        'event_id',
        'tilok_id',
        'instansi_name',
        'exam_date',
        'session',
        'participant_total',
        'participant_present',
        'participant_absent',
        'highest_score',
        'lowest_score',
        'average_score',
        // 'report_file',
        // 'survey_report_file',
        // 'report_tim_file',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tilok()
    {
        return $this->belongsTo(Tilok::class);
    }

}
