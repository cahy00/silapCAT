<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ministry extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
