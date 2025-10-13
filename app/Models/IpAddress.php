<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    protected $guarded = [
        'id',
    ];

    public $timestamps = false;

    public function authAttempts()
    {
        return $this->hasMany(AuthAttempt::class)
            ->orderBy('id', 'desc');
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class)
            ->orderBy('id', 'desc');
    }

    public function ip()
    {
        $ip = array_filter([$this->country_code, $this->country_name, $this->city_name]);

        return $ip ? implode(', ', $ip) : trans('app.notFound');
    }
}
