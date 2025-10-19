<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Offer;

class ClickSeeder extends Seeder
{
    public function run(): void
    {
        $webmaster = User::where('role', 'webmaster')->first();
        $offers = Offer::all();

        foreach ($offers as $offer) {
            for ($i = 0; $i < 15; $i++) {
                DB::table('clicks')->insert([
                    'offer_id' => $offer->id,
                    'webmaster_id' => $webmaster->id,
                    'created_at' => now()->subDays(rand(0, 10)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
