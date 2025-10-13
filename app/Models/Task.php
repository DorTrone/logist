<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [
        'id',
    ];

    public $timestamps = false;

    protected function casts()
    {
        return [
            'queries' => 'array',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_task')
            ->orderBy('username')
            ->orderBy('id', 'desc');
    }

    public function getName()
    {
        $locale = app()->getLocale();
        if ($locale == 'tm') {
            return $this->name_tm ?: $this->name;
        } elseif ($locale == 'ru') {
            return $this->name_ru ?: $this->name;
        } elseif ($locale == 'cn') {
            return $this->name_cn ?: $this->name;
        } else {
            return $this->name;
        }
    }
}
