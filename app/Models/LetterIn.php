<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LetterIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category_letter_id', 'departement_id', 'reference_number',
        'date_letter', 'date_in', 'origin_letter', 'properties_letter', 'file', 'staff_id'
    ];

    public function category_letter()
    {
        return $this->belongsTo(CategoryLetter::class, 'category_letter_id');
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
