<?php

namespace App\Models;

use App\Models\Tilok;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $fillable = 
    [
        'name', 'slug', 'start_date', 'end_date'
    ];

    public function tiloks()
    {
        return $this->belongsToMany(Tilok::class, 'event_tiloks');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
