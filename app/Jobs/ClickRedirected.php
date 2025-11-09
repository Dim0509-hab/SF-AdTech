<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Click;

class ClickRedirected implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $click;

    public function __construct(Click $click)
    {
        $this->click = $click;
    }

    public function handle()
    {
        $this->click->save(); // Сохраняет все изменения
    }
}
