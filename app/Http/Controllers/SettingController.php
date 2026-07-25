<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRestaurantSettingsRequest;
use App\Models\Restaurant;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $settings = (object) array_merge(
            $this->settingService->getGroup($restaurantId, 'general'),
            $this->settingService->getGroup($restaurantId, 'working_hours'),
            $this->settingService->getGroup($restaurantId, 'notifications'),
        );

        $restaurant = $request->user()->restaurant;
        $settings->name = $settings->name ?? $restaurant?->name;
        $settings->email = $settings->email ?? $restaurant?->email;
        $settings->phone = $settings->phone ?? $restaurant?->phone;
        $settings->address = $settings->address ?? $restaurant?->address;
        $settings->pix_key = $settings->pix_key ?? $restaurant?->pix_key;
        $settings->delivery_fee = $settings->delivery_fee ?? $restaurant?->delivery_fee;

        $hours = json_decode($settings->opening_hours ?? '{}', true);
        $settings->opening_hours = $hours;

        return view('settings.index', compact('settings'));
    }

    public function update(UpdateRestaurantSettingsRequest $request)
    {
        $restaurantId = $request->user()->restaurant_id;
        $restaurant = $request->user()->restaurant;

        $validated = $request->validated();

        $generalSettings = [];
        $workingHoursSettings = [];
        $notificationSettings = [];

        foreach ($validated as $key => $value) {
            if (in_array($key, ['name', 'email', 'phone', 'address', 'cnpj', 'pix_key', 'delivery_fee', 'free_delivery_min', 'delivery_radius', 'estimated_delivery_time', 'whatsapp', 'instagram'])) {
                $generalSettings[$key] = $value;
            } elseif (in_array($key, ['days'])) {
                $workingHoursSettings['opening_hours'] = json_encode($value);
            }
        }

        if (!empty($generalSettings)) {
            $this->settingService->setGroup($restaurantId, 'general', $generalSettings);
        }

        if (!empty($workingHoursSettings)) {
            $this->settingService->setGroup($restaurantId, 'working_hours', $workingHoursSettings);
        }

        if (!empty($notificationSettings)) {
            $this->settingService->setGroup($restaurantId, 'notifications', $notificationSettings);
        }

        if (isset($validated['name'])) {
            $restaurant->update(['name' => $validated['name']]);
        }
        if (isset($validated['email'])) {
            $restaurant->update(['email' => $validated['email']]);
        }
        if (isset($validated['phone'])) {
            $restaurant->update(['phone' => $validated['phone']]);
        }
        if (isset($validated['address'])) {
            $restaurant->update(['address' => $validated['address']]);
        }
        if (isset($validated['pix_key'])) {
            $restaurant->update(['pix_key' => $validated['pix_key']]);
        }
        if (isset($validated['delivery_fee'])) {
            $restaurant->update(['delivery_fee' => $validated['delivery_fee']]);
        }

        return redirect()->route('settings.index')->with('success', 'Configurações salvas com sucesso!');
    }
}
