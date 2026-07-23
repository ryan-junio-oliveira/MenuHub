<?php

namespace App\Services\Contracts;

interface GeocodingInterface
{
    public function geocode(string $address): array;
    public function reverseGeocode(float $lat, float $lng): array;
    public function calculateDistance(array $origin, array $destination): float;
}
