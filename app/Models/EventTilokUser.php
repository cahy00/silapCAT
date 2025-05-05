<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTilokUser extends Model
{
    use HasFactory;
    protected $table = 'event_tilok_users';
    protected $fillable = ['event_tilok_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eventTilok()
    {
        return $this->belongsTo(EventTilok::class);
    }
}
