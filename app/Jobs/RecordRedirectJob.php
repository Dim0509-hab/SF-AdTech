<?php

namespace App\Jobs;

use App\Models\Click;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordRedirectJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $clickId,
        protected string $finalUrl
    ) {}

    public function handle()
    {
        $click = Click::find($this->clickId);
        if (! $click) return;

        $click->redirected = true;
        $click->redirected_at = now();
        $click->final_url = $this->finalUrl;
        $click->redirect_attempts += 1;
        $click->save();
    }
}
