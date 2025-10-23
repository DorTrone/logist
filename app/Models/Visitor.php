<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $guarded = [
        'id',
    ];

    protected function casts()
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function ipAddress()
    {
        return $this->belongsTo(IpAddress::class);
    }

    public function userAgent()
    {
        return $this->belongsTo(UserAgent::class);
    }
}
