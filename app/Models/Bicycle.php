<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bicycle extends Model
{
    protected $fillable = [
        'object_number',
        'type',
        'sub_type',
        'brand',
        'color',
        'description',
        'city',
        'storage_location',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];
}
