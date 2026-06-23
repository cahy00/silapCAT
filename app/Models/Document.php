<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'category_id', 'desc', 'is_public', 'year', 'file'];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
