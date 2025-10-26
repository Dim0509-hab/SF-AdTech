<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class RoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function createAdminUser()
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'active' => 1
        ]);
    }

    protected function createAdvertiserUser()
    {
        return User::factory()->create([
            'role' => User::ROLE_ADVERTISER,
            'active' => 1
        ]);
    }

    protected function createWebmasterUser()
    {
        return User::factory()->create([
            'role' => User::ROLE_WEBMASTER,
            'active' => 1
        ]);
    }

    public function testAdminRole()
    {
        Log::info('Запуск теста: Проверка роли администратора');

        $user = $this->createAdminUser();

        Log::debug('Создан пользователь: '. $user->id);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isAdvertiser());
        $this->assertFalse($user->isWebmaster());

        Log::info('Тест пройден успешно');
    }

    public function testAdvertiserRole()
    {
        Log::info('Запуск теста: Проверка роли рекламодателя');

        $user = $this->createAdvertiserUser();

        Log::debug('Создан пользователь: '. $user->id);

        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isAdvertiser());
        $this->assertFalse($user->isWebmaster());

        Log::info('Тест пройден успешно');
    }

    public function testWebmasterRole()
    {
        Log::info('Запуск теста: Проверка роли вебмастера');

        $user = $this->createWebmasterUser();

        Log::debug('Создан пользователь: '. $user->id);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isAdvertiser());
        $this->assertTrue($user->isWebmaster());

        Log::info('Тест пройден успешно');
    }

    public function testInvalidRole()
    {
        Log::info('Запуск теста: Проверка недопустимой роли');

        try {
            $this->expectException(\Illuminate\Validation\ValidationException::class);
            User::factory()->create([
                'role' => 'invalid_role'
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка в тесте: '. $e->getMessage());
            throw $e;
        }

        Log::info('Тест пройден успешно');
    }


public function testActiveUser()
{
    $user = $this->createAdminUser();

    dump('Создан пользователь: ' . $user->id);
    dump('Роль: ' . $user->role);
    dump('Статус активности: ' . $user->active);

    // Проверьте значение напрямую из БД
    $this->assertEquals(1, $user->active);
    $this->assertTrue($user->isActive());

    // Проверьте через SQL
    $isActive = DB::table('users')
        ->where('id', $user->id)
        ->pluck('active')
        ->first();

    dump('Значение из БД: ' . $isActive);
    $this->assertTrue($isActive === 1);
}




    public function testInactiveUser()
    {
        Log::info('Запуск теста: Проверка неактивного пользователя');

        $user = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'active' => 0
        ]);

        Log::debug('Создан пользователь: '. $user->id);

        $this->assertFalse($user->isActive());

        Log::info('Тест пройден успешно');
    }
}
