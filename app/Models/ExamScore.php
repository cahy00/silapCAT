<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\NotifiesOnCreate;

class ExamScore extends Model
{
    use LogsActivity, NotifiesOnCreate;

    protected $guarded = [];

    protected $casts = [
        'exam_date' => 'date',
        'cat_score' => 'float',
        'interview_score' => 'float',
        'total_score' => 'float',
    ];

    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('exam_score');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (ExamScore $model) {
            $catRaw = floatval($model->cat_score);
            $catScaled = $catRaw / 5; // Convert 500 max scale to 100 max scale

            if ($model->exam_type === 'UD_I') {
                $model->interview_score = null;
                $total = $catScaled;
            } elseif ($model->exam_type === 'UD_II') {
                $interview = floatval($model->interview_score);
                $total = ($catScaled * 0.6) + ($interview * 0.4);
            } else { // UPKP
                $interview = floatval($model->interview_score);
                $total = ($catScaled * 0.5) + ($interview * 0.5);
            }

            $model->total_score = round($total, 2);
            $model->status = $model->total_score >= 70 ? 'Lulus' : 'Tidak Lulus';
        });
    }
}
