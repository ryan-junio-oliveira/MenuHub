<?php

namespace App\Jobs;

use App\Models\Restaurant;
use App\Services\WhatsAppBotService;
use App\Services\Contracts\WhatsAppInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Restaurant $restaurant,
        public array $payload
    ) {}

    public function handle(WhatsAppInterface $whatsApp, WhatsAppBotService $botService): void
    {
        $messages = $whatsApp->parseWebhook($this->payload);

        foreach ($messages as $message) {
            if (empty($message['from'])) {
                continue;
            }

            try {
                $botService->handleIncoming($message, $this->restaurant);
            } catch (\Exception $e) {
                report($e);
            }
        }
    }
}
