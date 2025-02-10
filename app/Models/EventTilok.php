<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTilok extends Model
{
    use HasFactory;

    protected $fillable =[
        'event_id',
        'tilok_id'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tilok()
    {
        return $this->belongsTo(Tilok::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_tilok_users');
    }
}
