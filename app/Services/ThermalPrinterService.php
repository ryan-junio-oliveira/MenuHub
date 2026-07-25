<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Contracts\ThermalPrinterInterface;
use Illuminate\Support\Facades\Log;

class ThermalPrinterService implements ThermalPrinterInterface
{
    const WIDTH_58MM = 32;
    const WIDTH_80MM = 48;

    private string $printerType;
    private ?string $printerPath;

    public function __construct()
    {
        $this->printerType = config('printing.driver', 'raw');
        $this->printerPath = config('printing.path', '/dev/usb/lp0');
    }

    public function printText(string $text): bool
    {
        $driver = config('printing.driver', 'raw');

        return match ($driver) {
            'network' => $this->printNetwork($text),
            'windows' => $this->printWindows($text),
            'linux' => $this->printLinux($text),
            default => $this->printRaw($text),
        };
    }

    public function printOrder(array $orderData): bool
    {
        $formatted = $this->formatOrderFromArray($orderData);
        return $this->printText($formatted);
    }

    public function printImage(string $imagePath): bool
    {
        if (!file_exists($imagePath)) {
            Log::warning('Printer image not found: ' . $imagePath);
            return false;
        }

        try {
            $driver = config('printing.driver', 'raw');

            return match ($driver) {
                'network' => $this->printImageNetwork($imagePath),
                default => $this->printImageRaw($imagePath),
            };
        } catch (\Exception $e) {
            Log::error('Image printing failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getStatus(): string
    {
        $driver = config('printing.driver', 'raw');

        return match ($driver) {
            'network' => $this->checkNetworkPrinter(),
            'windows' => $this->checkWindowsPrinter(),
            'linux' => $this->checkLinuxPrinter(),
            default => 'online',
        };
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

        $deliveryLabels = ['delivery' => 'ENTREGA', 'pickup' => 'RETIRADA'];
        $lines[] = 'Tipo: ' . ($deliveryLabels[$order->delivery_type] ?? $order->delivery_type);

        if ($order->delivery_type === 'delivery' && $order->delivery_address) {
            $lines[] = 'Endereco: ' . $order->delivery_address;
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

    private function formatOrderFromArray(array $data): string
    {
        $width = self::WIDTH_58MM;
        $divider = str_repeat('-', $width);

        $lines = [];
        $lines[] = '';
        $lines[] = str_repeat(' ', intval(($width - 16) / 2)) . 'COMANDA DE COZINHA';
        $lines[] = str_repeat('=', $width);
        $lines[] = 'Pedido: #' . ($data['order_number'] ?? 'N/A');
        $lines[] = $divider;
        $lines[] = 'Cliente: ' . ($data['customer'] ?? 'N/A');

        $lines[] = 'Tipo: ' . (($data['delivery_type'] ?? 'pickup') === 'delivery' ? 'ENTREGA' : 'RETIRADA');

        if (!empty($data['delivery_address'])) {
            $lines[] = 'Endereco: ' . $data['delivery_address'];
        }

        $lines[] = $divider;
        $lines[] = 'ITEM                QTD  VALOR';
        $lines[] = $divider;

        foreach ($data['items'] ?? [] as $item) {
            $name = $item['dish_name'] ?? $item['name'] ?? '';
            $qty = (string) ($item['quantity'] ?? 1);
            $priceVal = $item['unit_price'] ?? $item['price'] ?? 0;
            $price = 'R$ ' . number_format((float) $priceVal, 2, ',', '.');
            $size = !empty($item['size']) ? ' [' . strtoupper(substr($item['size'], 0, 1)) . ']' : '';
            $line = $name . $size;
            $line = str_pad($line, $width - 12) . str_pad($qty, 3, ' ', STR_PAD_LEFT) . '  ' . str_pad($price, 9, ' ', STR_PAD_LEFT);
            $lines[] = mb_substr($line, 0, $width);
        }

        $lines[] = $divider;
        $lines[] = str_pad('TOTAL: R$ ' . number_format((float) ($data['total'] ?? 0), 2, ',', '.'), $width, ' ', STR_PAD_LEFT);
        $lines[] = str_repeat('=', $width);
        $lines[] = 'MenuHub - Comanda de Cozinha';
        $lines[] = '';

        return implode("\n", $lines);
    }

    private function printNetwork(string $text): bool
    {
        $host = config('printing.host', '192.168.1.100');
        $port = config('printing.port', 9100);

        try {
            $socket = @fsockopen($host, $port, $errno, $errstr, 5);
            if (!$socket) {
                Log::warning("Network printer connection failed: {$errstr} ({$errno})");
                return false;
            }

            $this->writeEscPosCommands($socket, $text);
            fclose($socket);
            return true;
        } catch (\Exception $e) {
            Log::error('Network printing failed: ' . $e->getMessage());
            return false;
        }
    }

    private function printWindows(string $text): bool
    {
        $printerName = config('printing.printer_name', 'Microsoft Print to PDF');

        try {
            $escapedText = $text;
            $tempFile = tempnam(sys_get_temp_dir(), 'print_') . '.txt';
            file_put_contents($tempFile, $escapedText);

            $command = sprintf(
                'powershell -Command "Get-Content \'%s\' | Out-Printer -Name \'%s\'"',
                $tempFile,
                $printerName
            );

            exec($command . ' 2>&1', $output, $exitCode);

            unlink($tempFile);

            if ($exitCode !== 0) {
                Log::warning('Windows print command failed', ['output' => $output]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Windows printing failed: ' . $e->getMessage());
            return false;
        }
    }

    private function printLinux(string $text): bool
    {
        $printerName = config('printing.printer_name', 'default');

        try {
            $tempFile = tempnam(sys_get_temp_dir(), 'print_') . '.txt';
            file_put_contents($tempFile, $text);

            if ($printerName === 'default') {
                exec('lp ' . escapeshellarg($tempFile) . ' 2>&1', $output, $exitCode);
            } else {
                exec('lp -d ' . escapeshellarg($printerName) . ' ' . escapeshellarg($tempFile) . ' 2>&1', $output, $exitCode);
            }

            unlink($tempFile);

            if ($exitCode !== 0) {
                Log::warning('Linux print command failed', ['output' => $output]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Linux printing failed: ' . $e->getMessage());
            return false;
        }
    }

    private function printRaw(string $text): bool
    {
        Log::info('[Printer Mock] Would print:', ['text' => substr($text, 0, 200) . '...']);
        return true;
    }

    private function printImageNetwork(string $imagePath): bool
    {
        $host = config('printing.host', '192.168.1.100');
        $port = config('printing.port', 9100);

        try {
            $imageData = file_get_contents($imagePath);
            $socket = @fsockopen($host, $port, $errno, $errstr, 5);
            if (!$socket) {
                return false;
            }

            $esc = "\x1b";
            $gs = "\x1d";
            fwrite($socket, "{$gs}V\x00");

            fwrite($socket, $imageData);
            fwrite($socket, "\n\n\n\n\n");
            fclose($socket);
            return true;
        } catch (\Exception $e) {
            Log::error('Network image printing failed: ' . $e->getMessage());
            return false;
        }
    }

    private function printImageRaw(string $imagePath): bool
    {
        Log::info('[Printer Mock] Would print image:', ['path' => $imagePath]);
        return true;
    }

    private function writeEscPosCommands($socket, string $text): void
    {
        $esc = "\x1b";
        $gs = "\x1d";

        fwrite($socket, "{$esc}@");
        fwrite($socket, "{$esc}a\x01");
        fwrite($socket, "{$gs}!\x00");

        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $encoded = mb_convert_encoding($line, 'ISO-8859-1', 'UTF-8');
            fwrite($socket, $encoded . "\n");
        }

        fwrite($socket, "\n\n\n\n\n");
        fwrite($socket, "{$gs}V\x00");
    }

    private function checkNetworkPrinter(): string
    {
        $host = config('printing.host', '192.168.1.100');
        $port = config('printing.port', 9100);

        $socket = @fsockopen($host, $port, $errno, $errstr, 3);
        if ($socket) {
            fclose($socket);
            return 'online';
        }

        return 'offline';
    }

    private function checkWindowsPrinter(): string
    {
        $printerName = config('printing.printer_name', 'Microsoft Print to PDF');
        exec('powershell -Command "Get-Printer -Name \'' . $printerName . '\' | Select-Object -ExpandProperty PrinterStatus"', $output);

        return !empty($output) && $output[0] !== 'Offline' ? 'online' : 'offline';
    }

    private function checkLinuxPrinter(): string
    {
        $printerName = config('printing.printer_name', 'default');
        $cmd = $printerName === 'default'
            ? 'lpstat -t 2>&1'
            : "lpstat -p " . escapeshellarg($printerName) . " 2>&1";

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            return 'offline';
        }

        foreach ($output as $line) {
            if (stripos($line, 'idle') !== false || stripos($line, 'printing') !== false) {
                return 'online';
            }
        }

        return 'offline';
    }
}
