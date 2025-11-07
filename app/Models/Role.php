<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    // Разрешённые для заполнения поля
    protected $fillable = [
        'name',
        'description',
    ];

    // Связь: одна роль → много пользователей
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
