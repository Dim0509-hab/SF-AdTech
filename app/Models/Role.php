<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Если таблица не 'roles'
    // protected $table = 'your_roles_table';

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
