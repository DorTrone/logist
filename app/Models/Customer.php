<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable, HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts()
    {
        return [
            'last_seen' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function packages()
    {
        return $this->hasMany(Package::class)
            ->orderBy('id', 'desc');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)
            ->orderBy('id', 'desc');
    }

    public function authMethod()
    {
        return trans('app.' . ['phone', 'email', 'username'][$this->auth_method]);
    }

    public function authMethodIcon()
    {
        return ['telephone-fill', 'envelope-fill', 'person-circle'][$this->auth_method];
    }

    public function language()
    {
        return ['English', 'Türkmen', 'Русский', 'Chinese'][$this->language];
    }

    public function languageCode()
    {
        return ['en', 'tm', 'ru', 'cn'][$this->language];
    }

    public function platform()
    {
        return ['Web', 'Android', 'iOS'][$this->platform];
    }

    public function platformIcon()
    {
        return ['browser-chrome', 'android2', 'apple'][$this->platform];
    }

    public function getName()
    {
        return $this->code . ' ' . $this->name . ' ' . $this->surname;
    }
}
