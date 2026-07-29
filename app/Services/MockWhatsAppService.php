<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Services\Contracts\WhatsAppInterface;
use Illuminate\Support\Facades\Log;

class MockWhatsAppService implements WhatsAppInterface
{
    public function sendMessage(string $to, string $message, ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendMessage', [
            'to' => $to,
            'body' => mb_substr($message, 0, 200),
            'restaurant' => $restaurant?->name,
        ]);

        return [
            'success' => true,
            'message_id' => 'mock_' . uniqid(),
            'status' => 'sent',
        ];
    }

    public function sendTemplate(string $to, string $templateName, array $parameters, ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendTemplate', [
            'to' => $to,
            'template' => $templateName,
            'parameters' => $parameters,
            'restaurant' => $restaurant?->name,
        ]);

        return ['success' => true, 'status' => 'sent'];
    }

    public function sendImage(string $to, string $imageUrl, string $caption = '', ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendImage', [
            'to' => $to,
            'image' => $imageUrl,
            'caption' => $caption,
            'restaurant' => $restaurant?->name,
        ]);

        return ['success' => true, 'status' => 'sent'];
    }

    public function sendInteractiveList(string $to, string $header, string $body, string $footer, array $sections, ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendInteractiveList', [
            'to' => $to,
            'header' => $header,
            'sections' => collect($sections)->pluck('title')->implode(', '),
            'restaurant' => $restaurant?->name,
        ]);

        return ['success' => true, 'status' => 'sent'];
    }

    public function sendInteractiveButtons(string $to, string $body, array $buttons, ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendInteractiveButtons', [
            'to' => $to,
            'body' => mb_substr($body, 0, 100),
            'buttons' => collect($buttons)->pluck('reply.title')->implode(', '),
            'restaurant' => $restaurant?->name,
        ]);

        return ['success' => true, 'status' => 'sent'];
    }

    public function markAsRead(string $messageId, ?Restaurant $restaurant = null): array
    {
        return ['success' => true];
    }

    public function parseWebhook(array $payload): array
    {
        Log::info('[MockWhatsApp] parseWebhook — no real messages to parse in mock mode');

        return [];
    }

    public function sendPixPayment(string $to, string $pixKey, float $amount, string $orderNumber, ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendPixPayment', [
            'to' => $to,
            'pix_key' => $pixKey,
            'amount' => $amount,
            'order' => $orderNumber,
            'restaurant' => $restaurant?->name,
        ]);

        return $this->sendMessage($to,
            "💳 *Pagamento PIX*\n\n"
            . "Valor: R$ " . number_format($amount, 2, ',', '.') . "\n"
            . "Pedido: #{$orderNumber}\n"
            . "Chave PIX: {$pixKey}\n\n"
            . "[MOCK] Pagamento simulado — nenhum valor real foi cobrado.",
            $restaurant
        );
    }

    public function sendOrderConfirmation(string $to, array $orderData, ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendOrderConfirmation', [
            'to' => $to,
            'order' => $orderData['order_number'] ?? 'N/A',
            'total' => $orderData['total'] ?? 0,
            'restaurant' => $restaurant?->name,
        ]);

        return $this->sendMessage($to,
            "✅ *PEDIDO CONFIRMADO* ✅\n\n"
            . "Pedido: #{$orderData['order_number']}\n"
            . "Total: R$ " . number_format($orderData['total'] ?? 0, 2, ',', '.') . "\n\n"
            . "[MOCK] Confirmação simulada — sem integração real com WhatsApp.",
            $restaurant
        );
    }

    public function sendStatusUpdate(string $to, string $orderNumber, string $status, ?string $details = null, ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendStatusUpdate', [
            'to' => $to,
            'order' => $orderNumber,
            'status' => $status,
            'restaurant' => $restaurant?->name,
        ]);

        return $this->sendMessage($to,
            "📢 *Atualização do Pedido #{$orderNumber}*\n\n"
            . "Status: {$status}\n"
            . ($details ? "\n{$details}\n" : '')
            . "\n[MOCK] Notificação simulada.",
            $restaurant
        );
    }

    public function sendMenuToCustomer(string $to, string $customerName, array $menuData, ?Restaurant $restaurant = null): array
    {
        Log::info('[MockWhatsApp] sendMenuToCustomer', [
            'to' => $to,
            'customer' => $customerName,
            'date' => $menuData['date'] ?? 'N/A',
            'categories' => count($menuData['categories'] ?? []),
            'restaurant' => $restaurant?->name,
        ]);

        return $this->sendMessage($to,
            "🍽️ *Cardápio do Dia* — {$menuData['date']}\n\n"
            . "Olá, {$customerName}!\n\n"
            . "O cardápio de hoje está disponível! "
            . "São " . collect($menuData['categories'] ?? [])->sum(fn($c) => count($c['dishes'] ?? [])) . " pratos disponíveis.\n\n"
            . "[MOCK] Cardápio simulado — acesse o painel para ver os detalhes completos.",
            $restaurant
        );
    }
}