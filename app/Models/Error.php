<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Error extends Model
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

    public function status()
    {
        return trans('app.' . ['pending', 'completed', 'canceled'][$this->status]);
    }

    public function statusColor()
    {
        return ['warning', 'success', 'danger',][$this->status];
    }
}
