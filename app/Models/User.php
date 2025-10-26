<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property bool $active
 */
class User extends Authenticatable
{
    use HasFactory;

    // Константы для ролей
    const ROLE_ADMIN = 'admin';
    const ROLE_ADVERTISER = 'advertiser';
    const ROLE_WEBMASTER = 'webmaster';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    // Отношения
    public function offers()
    {
        return $this->hasMany(\App\Models\Offer::class, 'advertiser_id');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(\App\Models\Offer::class, 'offer_webmaster', 'webmaster_id', 'offer_id')
            ->withTimestamps()
            ->withPivot('agreed_price');
    }

    public function clicks()
    {
        return $this->hasMany(\App\Models\Click::class, 'webmaster_id');
    }

    // Проверка роли
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAdvertiser()
    {
        return $this->role === self::ROLE_ADVERTISER;
    }

    public function isWebmaster()
    {
        return $this->role === self::ROLE_WEBMASTER;
    }

    // Проверка активности пользователя
    public function isActive()
    {
        return $this->active === 1;
    }

    // Получение роли пользователя
    public function getRole()
    {
        return $this->role;
    }

    // Валидация роли при создании
    protected static function booted()
    {
        static::creating(function ($user) {
            $user->validateRole();
        });
    }

    protected function validateRole()
    {
        $validRoles = [
            self::ROLE_ADMIN,
            self::ROLE_ADVERTISER,
            self::ROLE_WEBMASTER
        ];

        if (!in_array($this->role, $validRoles)) {
            throw ValidationException::withMessages([
                'role' => 'Неверная роль'
            ]);
        }
    }
}
