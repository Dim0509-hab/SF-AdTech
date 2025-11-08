<?php

namespace App\Console\Commands;

use App\Models\Rejection;
use App\Models\Click;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedRejections extends Command
{
    protected $signature = 'seed:rejections {--limit=30 : Количество записей}';
    protected $description = 'Заполнить таблицу rejections реалистичными данными на основе кликов';

    public function handle()
    {
        $limit = $this->option('limit');
        $reasons = [
            'Подозрение на дубль клика',
            'Фрод-попытка: высокая частота',
            'Несоответствие геолокации',
            'Бот-активность: подозрительный UA',
            'Нарушение условий оффера',
            'IP из чёрного списка',
            'Подозрительное поведение: много кликов',
            'Невалидный referral',
        ];

        // Берём случайные клики
        $clicks = Click::inRandomOrder()->limit($limit)->get();

        if ($clicks->isEmpty()) {
            $this->error('❌ Нет данных в таблице clicks. Сначала добавьте клики.');
            return 1;
        }

        $bar = $this->output->createProgressBar(count($clicks));
        $bar->start();

        DB::transaction(function () use ($clicks, $reasons, $bar) {
            foreach ($clicks as $click) {
                Rejection::create([
                    'webmaster_id' => $click->webmaster_id,
                    'offer_id'     => $click->offer_id,
                    'link_hash'    => $click->link_hash,
                    'reason'       => $reasons[array_rand($reasons)],
                    'context'      => [
                        'original_click_id' => $click->id,
                        'ip' => $click->ip ?? fake()->ipv4,
                        'user_agent' => $click->user_agent ?? fake()->userAgent,
                        'referral' => $click->referral ?? null,
                        'country' => $click->country ?? null,
                        'device' => $click->device ?? null,
                        'created_at_original' => $click->created_at,
                    ],
                    'created_at' => $click->created_at->copy()->addSeconds(rand(1, 60)),
                    'updated_at' => now(),
                ]);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->info("\n✅ Успешно добавлено " . $clicks->count() . " отказов в таблицу `rejections`.");
        return 0;
    }
}
