<?php

namespace App\Models;

use App\Models\TilokDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tilok extends Model
{
    use HasFactory;
    protected $table = 'tiloks';
    protected $fillable = [
    'name',
    'address',
    'start_date',
    'end_date'
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_tiloks');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function documents()
    {
        return $this->hasMany(TilokDocument::class);
    }

    
}
