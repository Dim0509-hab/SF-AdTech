<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Offer;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 👑 Админ
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
        ]);

        // 💼 Рекламодатель
        $advertiser = User::create([
            'name' => 'Advertiser',
            'email' => 'adv@example.com',
            'password' => Hash::make('password'),
            'role' => 'advertiser',
            'active' => true,
        ]);

        // 🌐 Веб-мастер
        $webmaster = User::create([
            'name' => 'Webmaster',
            'email' => 'wm@example.com',
            'password' => Hash::make('password'),
            'role' => 'webmaster',
            'active' => true,
        ]);

        // 📢 Пример оффера
        Offer::create([
            'advertiser_id' => $advertiser->id,
            'name' => 'Тестовый оффер',
            'price' => 5.00,
            'target_url' => 'https://example.com',
            'themes' => json_encode(['IT', 'Реклама']),
            'active' => true,
        ]);
    }
}
