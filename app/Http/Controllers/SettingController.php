<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRestaurantSettingsRequest;
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

        return view('settings.index', compact('settings'));
    }

    public function update(UpdateRestaurantSettingsRequest $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $settings = $request->validated();

        foreach ($settings as $group => $values) {
            $this->settingService->setGroup($restaurantId, $group, $values);
        }

        return redirect()->route('settings.index');
    }
}
