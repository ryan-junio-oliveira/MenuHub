<?php

namespace App\Services;

use App\Services\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService implements PaymentGatewayInterface
{
    public function charge(array $data): array
    {
        $gateway = config('services.pix.gateway', 'mock');

        return match ($gateway) {
            'mercadopago' => $this->chargeMercadoPago($data),
            'asaas' => $this->chargeAsaas($data),
            'gerencianet' => $this->chargeGerencianet($data),
            default => $this->chargeMock($data),
        };
    }

    public function refund(string $transactionId, float $amount): array
    {
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'refunded_amount' => $amount,
            'status' => 'refunded',
            'message' => 'Reembolso processado com sucesso',
        ];
    }

    public function getStatus(string $transactionId): string
    {
        return 'completed';
    }

    private function chargeMock(array $data): array
    {
        $pixKey = $data['pix_key'] ?? 'mock_pix_key';
        $amount = $data['amount'] ?? 0;
        $orderId = $data['order_id'] ?? 'unknown';

        return [
            'success' => true,
            'transaction_id' => 'TXN-' . strtoupper(substr(md5(uniqid()), 0, 16)),
            'status' => 'pending',
            'amount' => $amount,
            'pix_code' => $this->generatePixCopyPaste($pixKey, $amount, $orderId),
            'pix_qr_code' => null,
            'pix_expiration' => now()->addMinutes(30)->toIso8601String(),
            'message' => 'Pagamento PIX gerado com sucesso',
        ];
    }

    private function chargeMercadoPago(array $data): array
    {
        $accessToken = config('services.pix.mercadopago_token');
        $response = Http::withToken($accessToken)
            ->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => $data['amount'],
                'description' => "Pedido #{$data['order_id']}",
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $data['email'] ?? 'cliente@email.com',
                    'first_name' => $data['customer_name'] ?? 'Cliente',
                ],
            ]);

        $result = $response->json();

        if ($response->successful()) {
            return [
                'success' => true,
                'transaction_id' => $result['id'],
                'status' => $result['status'],
                'amount' => $data['amount'],
                'pix_code' => $result['point_of_interaction']['transaction_data']['qr_code'] ?? '',
                'pix_qr_code' => $result['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                'pix_expiration' => $result['date_of_expiration'] ?? null,
                'message' => 'Pagamento PIX gerado com sucesso',
            ];
        }

        Log::error('MercadoPago charge failed', ['response' => $result]);
        return $this->chargeMock($data);
    }

    private function chargeAsaas(array $data): array
    {
        $apiKey = config('services.pix.asaas_key');
        $response = Http::withHeaders([
            'access_token' => $apiKey,
        ])->post('https://www.asaas.com/api/v3/payments', [
            'customer' => $data['asaas_customer_id'] ?? '',
            'billingType' => 'PIX',
            'value' => $data['amount'],
            'dueDate' => now()->addDays(1)->format('Y-m-d'),
            'description' => "Pedido #{$data['order_id']}",
            'externalReference' => (string) $data['order_id'],
        ]);

        $result = $response->json();

        if ($response->successful()) {
            return [
                'success' => true,
                'transaction_id' => $result['id'],
                'status' => $result['status'],
                'amount' => $data['amount'],
                'pix_code' => $result['pixQrCode'] ?? $result['bankSlipUrl'] ?? '',
                'pix_qr_code' => null,
                'pix_expiration' => $result['dueDate'] ?? null,
                'message' => 'Pagamento PIX gerado via Asaas',
            ];
        }

        Log::error('Asaas charge failed', ['response' => $result]);
        return $this->chargeMock($data);
    }

    private function chargeGerencianet(array $data): array
    {
        return $this->chargeMock($data);
    }

    private function generatePixCopyPaste(string $pixKey, float $amount, string $orderId): string
    {
        $merchantName = 'MenuHub';
        $merchantNameClean = substr(preg_replace('/[^a-zA-Z0-9 ]/', '', $merchantName), 0, 25);
        $txId = substr(preg_replace('/[^a-zA-Z0-9]/', '', $orderId), 0, 25);

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
}
