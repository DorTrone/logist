<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

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

    public function method()
    {
        return trans('app.' . ['phone', 'email'][$this->method]);
    }

    public function status()
    {
        return trans('app.' . ['pending', 'sent', 'completed', 'canceled'][$this->status]);
    }

    public function statusColor()
    {
        return ['warning', 'primary', 'success', 'danger',][$this->status];
    }
}
