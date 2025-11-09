<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property bool $active
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $role_id
 * @property string $status Статус модерации: ожидает, одобрен, отклонён
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Click> $clicks
 * @property-read int|null $clicks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Offer> $offers
 * @property-read int|null $offers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User approved()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
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
    'status',
    'active',
    'balance',
    'hold',
    'payment_method',
    'payout_details',
    'api_token',
    'referral_code',
    'registered_ip',
    'user_agent',
    'referrer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];



public function offers(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Offer::class,     // модель
            'offer_webmaster',            // имя pivot-таблицы
            'webmaster_id',               // внешний ключ текущей модели (User)
            'offer_id'                    // внешний ключ целевой модели (Offer)
        )
        ->withPivot([
            'cost_per_click',
            'agreed_price',
            'status',
            'created_at'
        ])
        ->withTimestamps()
        ->as('subscription'); // теперь $offer->subscription->cost_per_click
    }



        // Скоупы для удобства
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
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


    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


    public function isWebmaster(): bool
{
    return $this->role === 'webmaster' && $this->status === 'approved';
}
public function isApproved(): bool
{
    return $this->status === 'approved';
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
