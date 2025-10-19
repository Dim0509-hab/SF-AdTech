<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Offer;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $webmaster = User::where('role', 'webmaster')->first();
        $offers = Offer::all();

        foreach ($offers as $offer) {
            DB::table('offer_webmaster')->insert([
                'offer_id' => $offer->id,
                'webmaster_id' => $webmaster->id,
                'agreed_price' => $offer->price * 0.8, // вебмастер получает 80%
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
