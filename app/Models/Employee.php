<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'array',
    ];

    public function eventEmployees(): HasMany
    {
        return $this->hasMany(EventEmployee::class);
    }
}
