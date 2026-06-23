<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryLetter extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function letterin()
    {
        return $this->hasMany(LetterIn::class);
    }
}
