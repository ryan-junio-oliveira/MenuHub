<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Contracts\ThermalPrinterInterface;

class ThermalPrinterService implements ThermalPrinterInterface
{
    const WIDTH_58MM = 32;
    const WIDTH_80MM = 48;

    public function printText(string $text): bool
    {
        return true;
    }

    public function printOrder(array $orderData): bool
    {
        return true;
    }

    public function printImage(string $imagePath): bool
    {
        return false;
    }

    public function getStatus(): string
    {
        return 'online';
    }

    public function formatOrder(Order $order, string $format = '58mm'): string
    {
        $width = $format === '80mm' ? self::WIDTH_80MM : self::WIDTH_58MM;
        $divider = str_repeat('-', $width);
        $thickDivider = str_repeat('=', $width);

        $lines = [];
        $lines[] = '';
        $lines[] = str_repeat(' ', intval(($width - 16) / 2)) . 'COMANDA DE COZINHA';
        $lines[] = $thickDivider;
        $lines[] = 'Pedido: #' . $order->order_number;
        $lines[] = 'Data: ' . ($order->ordered_at ?? $order->created_at)->format('d/m/Y H:i');
        $lines[] = $divider;

        if ($order->customer) {
            $lines[] = 'Cliente: ' . $order->customer->name;
        }

        if ($order->delivery_type === 'delivery') {
            $lines[] = 'Tipo: ENTREGA';
            if ($order->delivery_address) {
                $lines[] = 'Endereco: ' . $order->delivery_address;
            }
        } else {
            $lines[] = 'Tipo: RETIRADA';
        }

        $lines[] = $divider;
        $lines[] = 'ITEM                QTD  VALOR';
        $lines[] = $divider;

        foreach ($order->items as $item) {
            $name = $item->dish_name;
            $qty = (string) $item->quantity;
            $price = 'R$ ' . number_format($item->unit_price, 2, ',', '.');
            $size = $item->size ? ' [' . strtoupper(substr($item->size, 0, 1)) . ']' : '';
            $line = $name . $size;
            $line = str_pad($line, $width - 12) . str_pad($qty, 3, ' ', STR_PAD_LEFT) . '  ' . str_pad($price, 9, ' ', STR_PAD_LEFT);
            $lines[] = mb_substr($line, 0, $width);
        }

        $lines[] = $divider;
        $lines[] = str_pad('TOTAL: R$ ' . number_format($order->total, 2, ',', '.'), $width, ' ', STR_PAD_LEFT);

        if ($order->payment_method) {
            $paymentLabels = ['pix' => 'PIX', 'credit_card' => 'Cartao Credito', 'debit_card' => 'Cartao Debito', 'cash' => 'Dinheiro', 'money' => 'Dinheiro'];
            $lines[] = str_pad('Pagamento: ' . ($paymentLabels[$order->payment_method] ?? $order->payment_method), $width, ' ', STR_PAD_LEFT);
        }

        if ($order->customer_notes) {
            $lines[] = $divider;
            $lines[] = 'Obs: ' . $order->customer_notes;
        }

        $lines[] = $thickDivider;
        $lines[] = str_repeat(' ', intval(($width - 20) / 2)) . 'MenuHub - Comanda de Cozinha';
        $lines[] = '';

        return implode("\n", $lines);
    }
}
