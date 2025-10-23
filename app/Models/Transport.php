<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Transport extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected function casts()
    {
        return [
            'images' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function packages()
    {
        return $this->hasMany(Package::class)
            ->orderBy('id', 'desc');
    }

    public function paymentReports()
    {
        return $this->hasMany(PaymentReport::class)
            ->orderBy('id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class)
            ->orderBy('id');
    }

    public function type()
    {
        return trans('const.' . config('const.transportTypes')[$this->type]['name']);
    }

    public function status()
    {
        return trans('const.' . config('const.transportStatuses')[$this->status]['name']);
    }

    public function statusColor()
    {
        return config('const.transportStatuses')[$this->status]['color'];
    }

    public function nextStatuses()
    {
        return [[0, 1], [1]][$this->status];
    }

    public function getName()
    {
        return $this->code;
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

    public function scopeFilterQuery($query, $f_transportTypes, $f_transportStatuses)
    {
        return $query
            ->when(isset($f_transportTypes) and count($f_transportTypes) > 0, function ($query) use ($f_transportTypes) {
                return $query->whereIn('type', $f_transportTypes);
            })
            ->when(isset($f_transportStatuses) and count($f_transportStatuses) > 0, function ($query) use ($f_transportStatuses) {
                return $query->whereIn('status', $f_transportStatuses);
            });
    }
}
