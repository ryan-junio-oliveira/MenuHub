<?php

namespace App\Services;

use App\Services\Contracts\GeocodingInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService implements GeocodingInterface
{
    public function geocode(string $address): array
    {
        $provider = config('services.geocoding.provider', 'mock');

        return match ($provider) {
            'google' => $this->geocodeGoogle($address),
            'openstreetmap' => $this->geocodeOpenStreetMap($address),
            default => $this->geocodeMock($address),
        };
    }

    public function reverseGeocode(float $lat, float $lng): array
    {
        $provider = config('services.geocoding.provider', 'mock');

        return match ($provider) {
            'google' => $this->reverseGeocodeGoogle($lat, $lng),
            'openstreetmap' => $this->reverseGeocodeOpenStreetMap($lat, $lng),
            default => $this->reverseGeocodeMock($lat, $lng),
        };
    }

    public function calculateDistance(array $origin, array $destination): float
    {
        $lat1 = deg2rad($origin['lat']);
        $lon1 = deg2rad($origin['lng']);
        $lat2 = deg2rad($destination['lat']);
        $lon2 = deg2rad($destination['lng']);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return 6371 * $c;
    }

    private function geocodeMock(string $address): array
    {
        return [
            'success' => true,
            'lat' => -23.5505,
            'lng' => -46.6333,
            'formatted_address' => $address,
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zipcode' => '01001-000',
            'provider' => 'mock',
        ];
    }

    private function geocodeGoogle(string $address): array
    {
        $apiKey = config('services.geocoding.google_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => $apiKey,
            'language' => 'pt-BR',
            'region' => 'br',
        ]);

        $result = $response->json();

        if ($response->successful() && ($result['status'] ?? '') === 'OK') {
            $location = $result['results'][0]['geometry']['location'] ?? [];
            $components = $this->parseAddressComponents($result['results'][0]['address_components'] ?? []);

            return [
                'success' => true,
                'lat' => $location['lat'] ?? null,
                'lng' => $location['lng'] ?? null,
                'formatted_address' => $result['results'][0]['formatted_address'] ?? $address,
                'neighborhood' => $components['neighborhood'] ?? '',
                'city' => $components['city'] ?? '',
                'state' => $components['state'] ?? '',
                'zipcode' => $components['zipcode'] ?? '',
                'provider' => 'google',
            ];
        }

        Log::warning('Google Geocoding failed', ['response' => $result]);
        return $this->geocodeMock($address);
    }

    private function geocodeOpenStreetMap(string $address): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'MenuHub/1.0',
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 1,
        ]);

        $result = $response->json();

        if ($response->successful() && !empty($result)) {
            $location = $result[0];
            $addressData = $location['address'] ?? [];

            return [
                'success' => true,
                'lat' => (float) $location['lat'],
                'lng' => (float) $location['lon'],
                'formatted_address' => $location['display_name'] ?? $address,
                'neighborhood' => $addressData['suburb'] ?? $addressData['neighbourhood'] ?? '',
                'city' => $addressData['city'] ?? $addressData['town'] ?? $addressData['village'] ?? '',
                'state' => $addressData['state'] ?? '',
                'zipcode' => $addressData['postcode'] ?? '',
                'provider' => 'openstreetmap',
            ];
        }

        Log::warning('OpenStreetMap Geocoding failed', ['response' => $result]);
        return $this->geocodeMock($address);
    }

    private function reverseGeocodeMock(float $lat, float $lng): array
    {
        return [
            'success' => true,
            'address' => "{$lat}, {$lng}",
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zipcode' => '01001-000',
            'provider' => 'mock',
        ];
    }

    private function reverseGeocodeGoogle(float $lat, float $lng): array
    {
        $apiKey = config('services.geocoding.google_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$lat},{$lng}",
            'key' => $apiKey,
            'language' => 'pt-BR',
        ]);

        $result = $response->json();

        if ($response->successful() && ($result['status'] ?? '') === 'OK') {
            $components = $this->parseAddressComponents($result['results'][0]['address_components'] ?? []);

            return [
                'success' => true,
                'address' => $result['results'][0]['formatted_address'] ?? '',
                'neighborhood' => $components['neighborhood'] ?? '',
                'city' => $components['city'] ?? '',
                'state' => $components['state'] ?? '',
                'zipcode' => $components['zipcode'] ?? '',
                'provider' => 'google',
            ];
        }

        return $this->reverseGeocodeMock($lat, $lng);
    }

    private function reverseGeocodeOpenStreetMap(float $lat, float $lng): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'MenuHub/1.0',
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'lat' => $lat,
            'lon' => $lng,
            'format' => 'json',
            'addressdetails' => 1,
        ]);

        $result = $response->json();

        if ($response->successful() && !empty($result)) {
            $addressData = $result['address'] ?? [];

            return [
                'success' => true,
                'address' => $result['display_name'] ?? '',
                'neighborhood' => $addressData['suburb'] ?? $addressData['neighbourhood'] ?? '',
                'city' => $addressData['city'] ?? $addressData['town'] ?? $addressData['village'] ?? '',
                'state' => $addressData['state'] ?? '',
                'zipcode' => $addressData['postcode'] ?? '',
                'provider' => 'openstreetmap',
            ];
        }

        return $this->reverseGeocodeMock($lat, $lng);
    }

    private function parseAddressComponents(array $components): array
    {
        $result = [
            'neighborhood' => '',
            'city' => '',
            'state' => '',
            'zipcode' => '',
        ];

        foreach ($components as $component) {
            $types = $component['types'] ?? [];
            if (in_array('neighborhood', $types)) {
                $result['neighborhood'] = $component['long_name'];
            } elseif (in_array('locality', $types) || in_array('administrative_area_level_2', $types)) {
                $result['city'] = $component['long_name'];
            } elseif (in_array('administrative_area_level_1', $types)) {
                $result['state'] = $component['short_name'];
            } elseif (in_array('postal_code', $types)) {
                $result['zipcode'] = $component['long_name'];
            }
        }

        return $result;
    }
}
