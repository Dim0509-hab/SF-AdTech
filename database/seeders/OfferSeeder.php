<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Offer;
use App\Models\User;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $advertiser = User::where('role', 'advertiser')->first();

        if (!$advertiser) return;

        Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
            'name' => 'Курс по Laravel 10',
            'price' => 3.50,
            'target_url' => 'https://laravel.com',
            'topics' => 'Программирование, Обучение',
            'active' => true,
        ]);

        Offer::factory()->create([
            'advertiser_id' => $advertiser->id,
            'name' => 'Рекламная платформа SF-AdTech',
            'price' => 5.00,
            'target_url' => 'https://example.com/adtech',
            'topics' => 'Реклама, Технологии',
            'active' => true,
        ]);
    }
}
