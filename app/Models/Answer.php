<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'jawaban'];

    public function questions()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
