<?php

namespace App\Services\Contracts;

interface ThermalPrinterInterface
{
    public function printText(string $text): bool;
    public function printOrder(array $orderData): bool;
    public function printImage(string $imagePath): bool;
    public function getStatus(): string;
}
