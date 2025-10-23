<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $guarded = [
        'id',
    ];

    protected function casts()
    {
        return [
            'datetime' => 'datetime',
        ];
    }

    public $timestamps = false;

    public function getTitle()
    {
        return $this->title;
    }

    public function getBody()
    {
        return $this->body;
    }
}
