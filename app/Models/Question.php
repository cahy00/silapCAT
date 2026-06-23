<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id', 'name', 'nip', 'question_category_id', 'wa', 'pesan', 'status', 'ministry_id'
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function question_category()
    {
        return $this->belongsTo(QuestionCategory::class);
    }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function answer()
    {
        return $this->hasMany(Answer::class);
    }
}
