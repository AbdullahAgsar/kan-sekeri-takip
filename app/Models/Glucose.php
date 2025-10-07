<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Glucose extends Model
{
    protected $fillable = ['value', 'note', 'is_hungry', 'measurement_datetime'];
    protected $casts = [
        'is_hungry' => 'boolean',
        'measurement_datetime' => 'datetime',
        'value' => 'decimal:1',
    ];

}
