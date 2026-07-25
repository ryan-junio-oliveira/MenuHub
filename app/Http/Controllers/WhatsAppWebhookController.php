<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Services\WhatsAppBotService;
use App\Services\Contracts\WhatsAppInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private readonly WhatsAppInterface $whatsApp,
        private readonly WhatsAppBotService $botService,
    ) {}

    public function verify(Request $request)
    {
        $verifyToken = config('services.whatsapp.verify_token', env('WHATSAPP_VERIFY_TOKEN', 'menuhub_webhook_2024'));

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        $payload = $request->all();

        if (config('app.debug')) {
            Log::debug('WhatsApp Webhook received', $payload);
        }

        $messages = $this->whatsApp->parseWebhook($payload);

        foreach ($messages as $message) {
            $phoneId = $message['restaurant_phone_id'] ?? null;

            $restaurant = null;
            if ($phoneId) {
                $restaurant = Restaurant::where('whatsapp_phone_id', $phoneId)->first();
            }

            if (!$restaurant) {
                Log::warning('No restaurant found for WhatsApp webhook', [
                    'phone_id' => $phoneId,
                    'from' => $message['from'] ?? 'unknown',
                ]);
                continue;
            }

            if (empty($message['from'])) {
                continue;
            }

            try {
                $this->botService->handleIncoming($message, $restaurant);
            } catch (\Exception $e) {
                Log::error('WhatsApp bot error: ' . $e->getMessage(), [
                    'from' => $message['from'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
