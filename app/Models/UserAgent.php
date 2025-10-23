<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAgent extends Model
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

    public function ua()
    {
        $ua = array_filter([$this->device, $this->platform, $this->browser, $this->robot ? '(' . $this->robot . ')' : null]);

        return $ua ? implode(', ', $ua) : trans('app.notFound');
    }
}
