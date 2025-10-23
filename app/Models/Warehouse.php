<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'postal_code',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}

