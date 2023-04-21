<?php

namespace App\Models;

use App\Traits\HasCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bicycle extends Model
{
    use HasCollection, HasFactory;

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
