<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class RoleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Очищаем таблицу пользователей перед заполнением
        User::truncate();

        // Создаем администраторов
        User::factory()
            ->count(1)
            ->state('admin')
            ->create([
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'active' => 1
            ]);

        // Создаем вебмастеров
        User::factory()
            ->count(1)
            ->state('webmaster')
            ->create([
                'email' => 'webmaster@example.com',
                'password' => bcrypt('password'),
                'role' => 'webmaster',
                'active' => 1
            ]);

        // Создаем рекламодателей
        User::factory()
            ->count(1)
            ->state('advertiser')
            ->create([
                'email' => 'advertiser@example.com',
                'password' => bcrypt('password'),
                'role' => 'advertiser',
                'active' => 1
            ]);
    }
}
