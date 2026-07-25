<?php

namespace App\Console\Commands;

use App\Models\DailyMenu;
use App\Models\Customer;
use App\Models\Restaurant;
use App\Models\WhatsAppSession;
use App\Services\Contracts\WhatsAppInterface;
use Illuminate\Console\Command;

class SendDailyMenu extends Command
{
    protected $signature = 'menuhub:send-daily-menu {restaurant? : The restaurant ID to send for}';
    protected $description = 'Send today\'s daily menu to all customers via WhatsApp';

    public function handle(WhatsAppInterface $whatsApp): int
    {
        $restaurantId = $this->argument('restaurant');

        $restaurants = $restaurantId
            ? Restaurant::where('id', $restaurantId)->where('is_active', true)->get()
            : Restaurant::where('is_active', true)->get();

        if ($restaurants->isEmpty()) {
            $this->warn('No active restaurants found.');
            return 0;
        }

        $sent = 0;
        $failed = 0;

        foreach ($restaurants as $restaurant) {
            if (empty($restaurant->whatsapp_phone_id) || empty($restaurant->whatsapp_api_token)) {
                $this->warn("Restaurant '{$restaurant->name}' has no WhatsApp configured. Skipping.");
                continue;
            }

            $menu = DailyMenu::where('restaurant_id', $restaurant->id)
                ->where('menu_date', today())
                ->where('is_published', true)
                ->with('items.dish.category')
                ->first();

            if (!$menu) {
                $this->warn("Restaurant '{$restaurant->name}' has no published menu for today. Skipping.");
                continue;
            }

            $customers = Customer::where('restaurant_id', $restaurant->id)
                ->whereNotNull('phone')
                ->get();

            if ($customers->isEmpty()) {
                $this->warn("Restaurant '{$restaurant->name}' has no customers with phone numbers.");
                continue;
            }

            $menuData = $this->buildMenuData($menu);

            foreach ($customers as $customer) {
                try {
                    $phone = $this->normalizePhone($customer->phone);
                    if (!$phone) {
                        continue;
                    }

                    $whatsApp->sendMenuToCustomer(
                        $phone,
                        $customer->name,
                        $menuData,
                        $restaurant
                    );

                    WhatsAppSession::updateOrCreate(
                        ['restaurant_id' => $restaurant->id, 'customer_phone' => $phone],
                        [
                            'customer_name' => $customer->name,
                            'step' => 'awaiting_size',
                            'menu_id' => $menu->id,
                            'data' => [],
                            'last_interaction_at' => now(),
                        ]
                    );

                    $sent++;
                    $this->info("Sent menu to {$customer->name} ({$phone})");

                    usleep(200000);
                } catch (\Exception $e) {
                    $failed++;
                    $this->error("Failed to send to {$customer->phone}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Done! Sent: {$sent}, Failed: {$failed}");

        return 0;
    }

    private function buildMenuData(DailyMenu $menu): array
    {
        $categories = [];
        $grouped = $menu->items->groupBy(fn($item) => $item->dish?->category?->name ?? 'Geral');

        foreach ($grouped as $categoryName => $items) {
            $dishes = $items->map(fn($item) => [
                'id' => $item->dish->id,
                'name' => $item->dish->name,
                'description' => $item->dish->description,
                'price_small' => $item->price_small,
                'price_medium' => $item->price_medium,
                'price_large' => $item->price_large,
            ])->toArray();

            $categories[] = [
                'name' => $categoryName,
                'dishes' => $dishes,
            ];
        }

        $menuDate = $menu->menu_date instanceof \Carbon\Carbon
            ? $menu->menu_date->format('d/m/Y')
            : $menu->menu_date;

        return [
            'date' => $menuDate,
            'categories' => $categories,
        ];
    }

    private function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) < 10 || strlen($phone) > 13) {
            return null;
        }

        if (strlen($phone) === 10 || strlen($phone) === 11) {
            $phone = '55' . $phone;
        }

        return $phone;
    }
}
