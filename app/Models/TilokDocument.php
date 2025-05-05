<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TilokDocument extends Model
{
    use HasFactory;
    protected $table = 'tilok_documents';

    protected $fillable = [
        'event_id',
        'tilok_id',
        'document_name',
        'document_path'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tilok()
    {
        return $this->belongsTo(Tilok::class);
    }

    



}
