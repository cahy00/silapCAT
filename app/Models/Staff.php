<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $fillable = [
        'name', 'departement_id', 'position', 'nip', 'photo', 'lhkpn', 'category'
    ];

    public function letterin()
    {
        return $this->hasMany(LetterIn::class);
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getMaskedNipAttribute()
    {
        $nip = $this->attributes['nip'];
        $prefix = substr($nip, 0, 6);
        $suffix = substr($nip, -2);
        $maskLength = strlen($nip) - (strlen($prefix) + strlen($suffix));
        $masked = str_repeat('*', $maskLength);
        return $prefix . $masked . $suffix;
    }
}
