<?php

namespace App\Services\Contracts;

interface PaymentGatewayInterface
{
    public function charge(array $data): array;
    public function refund(string $transactionId, float $amount): array;
    public function getStatus(string $transactionId): string;
}
