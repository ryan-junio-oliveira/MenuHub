<?php

namespace App\Services\Contracts;

interface WhatsAppInterface
{
    public function sendMessage(string $to, string $message): array;
    public function sendTemplate(string $to, string $templateName, array $parameters): array;
    public function sendImage(string $to, string $imageUrl, string $caption = ''): array;
    public function markAsRead(string $messageId): array;
    public function parseWebhook(array $payload): array;
}
