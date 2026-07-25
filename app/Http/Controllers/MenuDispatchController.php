<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DailyMenu;
use App\Models\Restaurant;
use App\Models\WhatsAppSession;
use App\Services\Contracts\WhatsAppInterface;
use Illuminate\Http\Request;

class MenuDispatchController extends Controller
{
    public function __construct(
        private readonly WhatsAppInterface $whatsApp,
    ) {}

    public function dispatch(Request $request, DailyMenu $dailyMenu)
    {
        $restaurant = $request->user()->restaurant;

        if (!$restaurant) {
            return redirect()->back()->with('error', 'Restaurante não encontrado.');
        }

        if (empty($restaurant->whatsapp_phone_id) || empty($restaurant->whatsapp_api_token)) {
            return redirect()->back()->with('error', 'WhatsApp não configurado. Configure as credenciais do WhatsApp no restaurante.');
        }

        if (!$dailyMenu->is_published) {
            return redirect()->back()->with('error', 'O cardápio precisa estar publicado para ser enviado.');
        }

        $dailyMenu->load('items.dish.category');

        $customers = Customer::where('restaurant_id', $restaurant->id)
            ->whereNotNull('phone')
            ->get();

        if ($customers->isEmpty()) {
            return redirect()->back()->with('error', 'Nenhum cliente com telefone cadastrado.');
        }

        $menuData = $this->buildMenuData($dailyMenu);
        $sent = 0;
        $failed = 0;

        foreach ($customers as $customer) {
            try {
                $phone = $this->normalizePhone($customer->phone);
                if (!$phone) {
                    $failed++;
                    continue;
                }

                $this->whatsApp->sendMenuToCustomer(
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
                        'menu_id' => $dailyMenu->id,
                        'data' => [],
                        'last_interaction_at' => now(),
                    ]
                );

                $sent++;
                usleep(100000);
            } catch (\Exception $e) {
                $failed++;
                report($e);
            }
        }

        $message = "Cardápio enviado para {$sent} cliente(s)";
        if ($failed > 0) {
            $message .= " ({$failed} falhas)";
        }

        return redirect()->back()->with('success', $message . ' via WhatsApp!');
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
        if (strlen($phone) < 10 || strlen($phone) > 13) return null;
        if (strlen($phone) === 10 || strlen($phone) === 11) $phone = '55' . $phone;
        return $phone;
    }
}
