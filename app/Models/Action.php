<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Action extends Model
{
    protected $guarded = [
        'id',
    ];

    protected function casts()
    {
        return [
            'updates' => 'array',
            'images' => 'array',
            'created_at' => 'datetime',
        ];
    }

    const UPDATED_AT = null;

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function getName()
    {
        return trans('app.action') . ' #' . $this->id;
    }

    public function getImages($url = false)
    {
        $url = $url ? url('') : '';

        if (count($this->images ?: []) > 0) {
            return collect($this->images)
                ->transform(function ($i) use ($url) {
                    return $url . Storage::url($i);
                })
                ->toArray();
        } else {
            return [];
        }
    }
}
