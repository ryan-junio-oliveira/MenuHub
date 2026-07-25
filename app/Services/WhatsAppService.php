<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Services\Contracts\WhatsAppInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService implements WhatsAppInterface
{
    private const API_BASE = 'https://graph.facebook.com/v21.0';

    public function sendMessage(string $to, string $message, ?Restaurant $restaurant = null): array
    {
        $phoneId = $this->getPhoneId($restaurant);
        $token = $this->getToken($restaurant);

        $response = Http::withToken($token)
            ->post("{$this->getApiBase($restaurant)}/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

        $this->logResponse('sendMessage', $response->json());

        return $response->json();
    }

    public function sendTemplate(string $to, string $templateName, array $parameters, ?Restaurant $restaurant = null): array
    {
        $phoneId = $this->getPhoneId($restaurant);
        $token = $this->getToken($restaurant);

        $body = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => 'pt_BR'],
                'components' => [],
            ],
        ];

        if (!empty($parameters)) {
            $body['template']['components'][] = [
                'type' => 'body',
                'parameters' => array_map(fn($v) => ['type' => 'text', 'text' => (string) $v], $parameters),
            ];
        }

        $response = Http::withToken($token)
            ->post("{$this->getApiBase($restaurant)}/{$phoneId}/messages", $body);

        $this->logResponse('sendTemplate', $response->json());

        return $response->json();
    }

    public function sendImage(string $to, string $imageUrl, string $caption = '', ?Restaurant $restaurant = null): array
    {
        $phoneId = $this->getPhoneId($restaurant);
        $token = $this->getToken($restaurant);

        $body = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'image',
            'image' => ['link' => $imageUrl],
        ];

        if ($caption) {
            $body['image']['caption'] = $caption;
        }

        $response = Http::withToken($token)
            ->post("{$this->getApiBase($restaurant)}/{$phoneId}/messages", $body);

        $this->logResponse('sendImage', $response->json());

        return $response->json();
    }

    public function sendInteractiveList(string $to, string $header, string $body, string $footer, array $sections, ?Restaurant $restaurant = null): array
    {
        $phoneId = $this->getPhoneId($restaurant);
        $token = $this->getToken($restaurant);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'header' => ['type' => 'text', 'text' => $header],
                'body' => ['text' => $body],
                'footer' => ['text' => $footer],
                'action' => [
                    'button' => 'Ver Opções',
                    'sections' => $sections,
                ],
            ],
        ];

        $response = Http::withToken($token)
            ->post("{$this->getApiBase($restaurant)}/{$phoneId}/messages", $payload);

        $this->logResponse('sendInteractiveList', $response->json());

        return $response->json();
    }

    public function sendInteractiveButtons(string $to, string $body, array $buttons, ?Restaurant $restaurant = null): array
    {
        $phoneId = $this->getPhoneId($restaurant);
        $token = $this->getToken($restaurant);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => ['text' => $body],
                'action' => [
                    'buttons' => $buttons,
                ],
            ],
        ];

        $response = Http::withToken($token)
            ->post("{$this->getApiBase($restaurant)}/{$phoneId}/messages", $payload);

        $this->logResponse('sendInteractiveButtons', $response->json());

        return $response->json();
    }

    public function markAsRead(string $messageId, ?Restaurant $restaurant = null): array
    {
        $phoneId = $this->getPhoneId($restaurant);
        $token = $this->getToken($restaurant);

        $response = Http::withToken($token)
            ->post("{$this->getApiBase($restaurant)}/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'status' => 'read',
                'message_id' => $messageId,
            ]);

        return $response->json();
    }

    public function parseWebhook(array $payload): array
    {
        $messages = [];

        $entries = $payload['entry'] ?? [];
        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $value = $change['value'] ?? [];
                $metadata = $value['metadata'] ?? [];
                $contacts = $value['contacts'] ?? [];
                $incomingMessages = $value['messages'] ?? [];

                foreach ($incomingMessages as $msg) {
                    $parsed = [
                        'message_id' => $msg['id'] ?? null,
                        'from' => $msg['from'] ?? null,
                        'timestamp' => $msg['timestamp'] ?? null,
                        'type' => $msg['type'] ?? null,
                        'restaurant_phone_id' => $metadata['phone_number_id'] ?? null,
                        'contact_name' => $contacts[0]['profile']['name'] ?? 'Cliente',
                    ];

                    switch ($msg['type'] ?? '') {
                        case 'text':
                            $parsed['text'] = $msg['text']['body'] ?? '';
                            break;
                        case 'interactive':
                            $interactive = $msg['interactive'] ?? [];
                            if (isset($interactive['button_reply'])) {
                                $parsed['interactive_type'] = 'button_reply';
                                $parsed['button_id'] = $interactive['button_reply']['id'] ?? '';
                                $parsed['button_title'] = $interactive['button_reply']['title'] ?? '';
                            } elseif (isset($interactive['list_reply'])) {
                                $parsed['interactive_type'] = 'list_reply';
                                $parsed['list_id'] = $interactive['list_reply']['id'] ?? '';
                                $parsed['list_title'] = $interactive['list_reply']['title'] ?? '';
                            }
                            break;
                        case 'location':
                            $location = $msg['location'] ?? [];
                            $parsed['latitude'] = $location['latitude'] ?? null;
                            $parsed['longitude'] = $location['longitude'] ?? null;
                            $parsed['location_name'] = $location['name'] ?? null;
                            $parsed['address'] = $location['address'] ?? null;
                            break;
                        case 'order':
                            $parsed['order'] = $msg['order'] ?? [];
                            break;
                    }

                    $messages[] = $parsed;
                }
            }
        }

        return $messages;
    }

    public function sendPixPayment(string $to, string $pixKey, float $amount, string $orderNumber, ?Restaurant $restaurant = null): array
    {
        $pixCopyPaste = $this->generatePixCopyPaste($pixKey, $amount, $orderNumber, $restaurant?->name ?? 'MenuHub');

        $message = "💳 *Pagamento PIX*\n\n"
            . "Valor: R$ " . number_format($amount, 2, ',', '.') . "\n"
            . "Pedido: #{$orderNumber}\n\n"
            . "*Chave PIX:* {$pixKey}\n\n"
            . "Ou use o código abaixo para copiar e colar:\n\n"
            . "```\n{$pixCopyPaste}\n```\n\n"
            . "Após o pagamento, envie o comprovante aqui para confirmarmos.";

        return $this->sendMessage($to, $message, $restaurant);
    }

    public function sendOrderConfirmation(string $to, array $orderData, ?Restaurant $restaurant = null): array
    {
        $itemsText = '';
        foreach ($orderData['items'] as $item) {
            $itemsText .= "{$item['quantity']}x {$item['dish_name']}";
            if (!empty($item['size'])) {
                $sizeLabels = ['small' => 'P', 'medium' => 'M', 'large' => 'G'];
                $itemsText .= " [" . ($sizeLabels[$item['size']] ?? $item['size']) . "]";
            }
            $itemsText .= " - R$ " . number_format($item['unit_price'] * $item['quantity'], 2, ',', '.') . "\n";
        }

        $message = "✅ *RESUMO DO PEDIDO* ✅\n\n"
            . "Pedido: #{$orderData['order_number']}\n\n"
            . "*Itens:*\n{$itemsText}\n";

        if (!empty($orderData['delivery_fee']) && $orderData['delivery_fee'] > 0) {
            $message .= "Taxa de Entrega: R$ " . number_format($orderData['delivery_fee'], 2, ',', '.') . "\n";
        }

        $message .= "\n*Total: R$ " . number_format($orderData['total'], 2, ',', '.') . "*\n";

        $deliveryLabels = ['delivery' => 'Entrega no Endereço', 'pickup' => 'Retirada no Local'];
        $message .= "Tipo: " . ($deliveryLabels[$orderData['delivery_type']] ?? $orderData['delivery_type']) . "\n";

        $paymentLabels = ['pix' => 'PIX', 'cash' => 'Dinheiro', 'credit_card' => 'Cartão de Crédito', 'debit_card' => 'Cartão de Débito'];
        $message .= "Pagamento: " . ($paymentLabels[$orderData['payment_method']] ?? $orderData['payment_method']) . "\n";

        if (!empty($orderData['estimated_time'])) {
            $message .= "\n⏱ *Tempo estimado:* {$orderData['estimated_time']} minutos\n";
        }

        return $this->sendMessage($to, $message, $restaurant);
    }

    public function sendStatusUpdate(string $to, string $orderNumber, string $status, ?string $details = null, ?Restaurant $restaurant = null): array
    {
        $statusLabels = [
            'received' => '📥 *Recebido*',
            'preparing' => '👨‍🍳 *Em Preparo*',
            'out_for_delivery' => '🛵 *Saiu para Entrega*',
            'completed' => '✅ *Finalizado*',
            'canceled' => '❌ *Cancelado*',
        ];

        $message = "📢 *Atualização do Pedido #{$orderNumber}*\n\n"
            . "Status: " . ($statusLabels[$status] ?? $status) . "\n";

        if ($details) {
            $message .= "\n{$details}\n";
        }

        $message .= "\nObrigado por pedir conosco! 🍽️";

        return $this->sendMessage($to, $message, $restaurant);
    }

    public function sendMenuToCustomer(string $to, string $customerName, array $menuData, ?Restaurant $restaurant = null): array
    {
        $greeting = $customerName ? "Olá, {$customerName}! 🎉\n\n" : "Olá! 🎉\n\n";

        $message = $greeting
            . "Segue o *Cardápio do Dia* - {$menuData['date']}:\n\n";

        foreach ($menuData['categories'] as $category) {
            $message .= "*{$category['name']}*\n";
            foreach ($category['dishes'] as $dish) {
                $message .= "• {$dish['name']}";
                if ($dish['price_small']) {
                    $message .= " (P: R$ " . number_format($dish['price_small'], 2, ',', '.');
                    if ($dish['price_medium']) {
                        $message .= " | M: R$ " . number_format($dish['price_medium'], 2, ',', '.');
                    }
                    if ($dish['price_large']) {
                        $message .= " | G: R$ " . number_format($dish['price_large'], 2, ',', '.');
                    }
                    $message .= ")";
                }
                $message .= "\n";
            }
            $message .= "\n";
        }

        $message .= "💬 *Para fazer seu pedido, responda esta mensagem ou clique no botão abaixo:*";

        $result = $this->sendInteractiveButtons($to, $message, [
            [
                'type' => 'reply',
                'reply' => [
                    'id' => 'start_order',
                    'title' => 'Fazer Pedido',
                ],
            ],
        ], $restaurant);

        return $result;
    }

    private function getPhoneId(?Restaurant $restaurant): string
    {
        return $restaurant?->whatsapp_phone_id
            ?? config('services.whatsapp.phone_id')
            ?? env('WHATSAPP_PHONE_ID', '');
    }

    private function getToken(?Restaurant $restaurant): string
    {
        return $restaurant?->whatsapp_api_token
            ?? config('services.whatsapp.token')
            ?? env('WHATSAPP_API_TOKEN', '');
    }

    private function getApiBase(?Restaurant $restaurant): string
    {
        return self::API_BASE;
    }

    private function generatePixCopyPaste(string $pixKey, float $amount, string $orderNumber, string $merchantName): string
    {
        $merchantNameClean = substr(preg_replace('/[^a-zA-Z0-9 ]/', '', $merchantName), 0, 25);
        $txId = substr(preg_replace('/[^a-zA-Z0-9]/', '', $orderNumber), 0, 25);

        $payload = '000201';
        $payload .= '010212';
        $payload .= '26' . str_pad(dechex(30 + strlen($pixKey)), 2, '0', STR_PAD_LEFT) . '0014BR.GOV.BCB.PIX' . '01' . str_pad(dechex(strlen($pixKey)), 2, '0', STR_PAD_LEFT) . $pixKey;
        $payload .= '52040000';
        $payload .= '5303986';
        $payload .= '54' . str_pad(dechex(strlen(number_format($amount, 2, '.', ''))), 2, '0', STR_PAD_LEFT) . number_format($amount, 2, '.', '');
        $payload .= '5802BR';
        $payload .= '59' . str_pad(dechex(strlen($merchantNameClean)), 2, '0', STR_PAD_LEFT) . $merchantNameClean;
        $payload .= '60' . str_pad(dechex(strlen('MENUHUB')), 2, '0', STR_PAD_LEFT) . 'MENUHUB';
        $payload .= '62' . str_pad(dechex(10 + strlen($txId)), 2, '0', STR_PAD_LEFT) . '05' . str_pad(dechex(strlen($txId)), 2, '0', STR_PAD_LEFT) . $txId;
        $payload .= '6304';

        $crc16 = $this->crc16Checksum($payload);
        $payload .= strtoupper(dechex($crc16));

        return $payload;
    }

    private function crc16Checksum(string $data): int
    {
        $crc = 0xFFFF;
        $data = mb_convert_encoding($data, 'ISO-8859-1', 'UTF-8');
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x0001) {
                    $crc = ($crc >> 1) ^ 0x8408;
                } else {
                    $crc = $crc >> 1;
                }
            }
        }
        return $crc ^ 0xFFFF;
    }

    private function logResponse(string $method, ?array $response): void
    {
        if (config('app.debug')) {
            Log::debug("WhatsApp API [{$method}]: " . json_encode($response));
        }
    }
}
