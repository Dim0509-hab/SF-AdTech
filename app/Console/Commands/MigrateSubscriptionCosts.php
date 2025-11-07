<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class MigrateSubscriptionCosts extends Command
{
    protected $signature = 'subscriptions:migrate-costs';
    protected $description = 'Переносит cost_per_click из subscriptions в offer_webmaster';

    public function handle()
    {
        $subscriptions = DB::table('subscriptions')
            ->select('webmaster_id', 'offer_id', 'cost_per_click')
            ->get();

        foreach ($subscriptions as $sub) {
            DB::table('offer_webmaster')
                ->where('webmaster_id', $sub->webmaster_id)
                ->where('offer_id', $sub->offer_id)
                ->update(['cost_per_click' => $sub->cost_per_click]);
        }

        $this->info('Данные перенесены!');
        return 0;
    }
}

