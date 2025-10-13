<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected function casts()
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    const UPDATED_AT = null;

    public function getName()
    {
        return trans('app.contact') . ' #' . $this->id;
    }
}
