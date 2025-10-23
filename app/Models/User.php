<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts()
    {
        return [
            'guards' => 'array',
            'permissions' => 'array',
            'api_permissions' => 'array',
            'queries' => 'array',
            'last_seen' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public $timestamps = false;

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'user_task')
            ->orderBy('name')
            ->orderBy('id', 'desc');
    }

    public function language()
    {
        return ['English', 'Türkmen', 'Русский', 'Chinese'][$this->language];
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
        return $this->name . ' ' . $this->id;
    }
}
