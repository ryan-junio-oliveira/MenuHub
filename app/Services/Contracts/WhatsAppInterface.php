<?php

namespace App\Services\Contracts;

use App\Models\Restaurant;

interface WhatsAppInterface
{
    public function sendMessage(string $to, string $message, ?Restaurant $restaurant = null): array;
    public function sendTemplate(string $to, string $templateName, array $parameters, ?Restaurant $restaurant = null): array;
    public function sendImage(string $to, string $imageUrl, string $caption = '', ?Restaurant $restaurant = null): array;
    public function sendInteractiveList(string $to, string $header, string $body, string $footer, array $sections, ?Restaurant $restaurant = null): array;
    public function sendInteractiveButtons(string $to, string $body, array $buttons, ?Restaurant $restaurant = null): array;
    public function markAsRead(string $messageId, ?Restaurant $restaurant = null): array;
    public function parseWebhook(array $payload): array;
    public function sendPixPayment(string $to, string $pixKey, float $amount, string $orderNumber, ?Restaurant $restaurant = null): array;
    public function sendOrderConfirmation(string $to, array $orderData, ?Restaurant $restaurant = null): array;
    public function sendStatusUpdate(string $to, string $orderNumber, string $status, ?string $details = null, ?Restaurant $restaurant = null): array;
    public function sendMenuToCustomer(string $to, string $customerName, array $menuData, ?Restaurant $restaurant = null): array;
}
