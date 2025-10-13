<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $guarded = [
        'id',
    ];

    protected function casts()
    {
        return [
            'datetime_start' => 'datetime',
            'datetime_end' => 'datetime',
        ];
    }

    public $timestamps = false;

    public function getName()
    {
        return trans('app.banner') . ' #' . $this->id;
    }

    public function getImage($lang = null, $url = false)
    {
        if ($lang == 'tm') {
            $image = $this->image_tm;
        } elseif ($lang == 'ru') {
            $image = $this->image_ru;
        } elseif ($lang == 'cn') {
            $image = $this->image_cn;
        } else {
            $image = $this->image;
        }
        $url = $url ? url('') : '';

        if ($image) {
            return $url . Storage::url($image);
        } else {
            return null;
        }
    }

    public function getImage2($lang = null, $url = false)
    {
        if ($lang == 'tm') {
            $image = $this->image_2_tm;
        } elseif ($lang == 'ru') {
            $image = $this->image_2_ru;
        } elseif ($lang == 'cn') {
            $image = $this->image_2_cn;
        } else {
            $image = $this->image_2;
        }
        $url = $url ? url('') : '';

        if ($image) {
            return $url . Storage::url($image);
        } else {
            return null;
        }
    }
}
