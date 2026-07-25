<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\DailyMenu;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\WhatsAppSession;
use App\Services\Contracts\WhatsAppInterface;
use App\Events\OrderCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppBotService
{
    public function __construct(
        private readonly WhatsAppInterface $whatsApp,
        private readonly OrderService $orderService,
    ) {}

    public function handleIncoming(array $message, Restaurant $restaurant): void
    {
        $phone = $message['from'];
        $messageId = $message['message_id'];
        $contactName = $message['contact_name'] ?? 'Cliente';

        $this->whatsApp->markAsRead($messageId, $restaurant);

        $session = WhatsAppSession::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'customer_phone' => $phone],
            ['customer_name' => $contactName, 'step' => 'idle'],
        );

        if ($session->customer_name !== $contactName) {
            $session->update(['customer_name' => $contactName]);
        }

        $session->update(['last_interaction_at' => now()]);

        $input = $this->extractInput($message);

        match ($session->step) {
            'idle' => $this->handleIdle($session, $restaurant, $input),
            'awaiting_size' => $this->handleSizeSelection($session, $restaurant, $input),
            'awaiting_protein' => $this->handleProteinSelection($session, $restaurant, $input),
            'awaiting_sides' => $this->handleSidesSelection($session, $restaurant, $input),
            'awaiting_delivery_type' => $this->handleDeliveryType($session, $restaurant, $input),
            'awaiting_address' => $this->handleAddress($session, $restaurant, $input),
            'awaiting_payment' => $this->handlePayment($session, $restaurant, $input),
            'awaiting_change' => $this->handleChange($session, $restaurant, $input),
            'confirming' => $this->handleConfirmation($session, $restaurant, $input),
            default => $this->sendWelcomeMessage($session, $restaurant),
        };
    }

    public function sendMenuToCustomer(WhatsAppSession $session, DailyMenu $menu, Restaurant $restaurant): void
    {
        $menu->load('items.dish.category');
        $grouped = $menu->items->groupBy(fn($item) => $item->dish?->category?->name ?? 'Geral');

        $sections = [];
        foreach ($grouped as $categoryName => $items) {
            $rows = $items->map(function ($item) {
                $dish = $item->dish;
                $desc = [];
                if ($item->price_small) $desc[] = "P: R$" . number_format($item->price_small, 2, ',', '.');
                if ($item->price_medium) $desc[] = "M: R$" . number_format($item->price_medium, 2, ',', '.');
                if ($item->price_large) $desc[] = "G: R$" . number_format($item->price_large, 2, ',', '.');
                return [
                    'id' => "protein_{$dish->id}",
                    'title' => $dish->name,
                    'description' => implode(' | ', $desc),
                ];
            })->toArray();

            $sections[] = [
                'title' => $categoryName,
                'rows' => $rows,
            ];
        }

        $sizeButtons = [
            ['type' => 'reply', 'reply' => ['id' => 'size_small', 'title' => 'Pequena (P)']],
            ['type' => 'reply', 'reply' => ['id' => 'size_medium', 'title' => 'Média (M)']],
            ['type' => 'reply', 'reply' => ['id' => 'size_large', 'title' => 'Grande (G)']],
        ];

        $menuDate = $menu->menu_date instanceof \Carbon\Carbon
            ? $menu->menu_date->format('d/m/Y')
            : $menu->menu_date;

        $this->whatsApp->sendMessage(
            $session->customer_phone,
            "🍽️ *Cardápio do Dia - {$menuDate}*\n\nOlá, {$session->customer_name}! Veja as opções de hoje:",
            $restaurant
        );

        foreach ($grouped as $categoryName => $items) {
            $text = "*{$categoryName}*\n";
            foreach ($items as $item) {
                $dish = $item->dish;
                $text .= "• {$dish->name}";
                $prices = [];
                if ($item->price_small) $prices[] = "P: R$ " . number_format($item->price_small, 2, ',', '.');
                if ($item->price_medium) $prices[] = "M: R$ " . number_format($item->price_medium, 2, ',', '.');
                if ($item->price_large) $prices[] = "G: R$ " . number_format($item->price_large, 2, ',', '.');
                if ($prices) $text .= " (" . implode(' / ', $prices) . ")";
                $text .= "\n";
            }
            $this->whatsApp->sendMessage($session->customer_phone, $text, $restaurant);
        }

        $this->whatsApp->sendInteractiveButtons(
            $session->customer_phone,
            "👉 *Para começar*, escolha o *TAMANHO* da sua marmita:",
            $sizeButtons,
            $restaurant
        );

        $session->update([
            'step' => 'awaiting_size',
            'menu_id' => $menu->id,
            'data' => [],
        ]);
    }

    private function handleIdle(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        if ($input === 'start_order' || $input === 'cardapio' || $input === 'menu') {
            $todayMenu = DailyMenu::where('restaurant_id', $restaurant->id)
                ->where('menu_date', today())
                ->where('is_published', true)
                ->first();

            if ($todayMenu) {
                $this->sendMenuToCustomer($session, $todayMenu, $restaurant);
            } else {
                $this->whatsApp->sendMessage(
                    $session->customer_phone,
                    "Olá, {$session->customer_name}! 🙋\n\n"
                        . "Ainda não temos um cardápio disponível para hoje. "
                        . "Tente novamente mais tarde ou entre em contato conosco pelo telefone.",
                    $restaurant
                );
            }
        } else {
            $this->sendWelcomeMessage($session, $restaurant);
        }
    }

    private function handleSizeSelection(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        $sizeMap = [
            'size_small' => 'small',
            'size_medium' => 'medium',
            'size_large' => 'large',
            'p' => 'small',
            'm' => 'medium',
            'g' => 'large',
            'pequena' => 'small',
            'media' => 'medium',
            'grande' => 'large',
            '1' => 'small',
            '2' => 'medium',
            '3' => 'large',
        ];

        $size = $sizeMap[$input] ?? null;

        if (!$size) {
            $this->whatsApp->sendMessage(
                $session->customer_phone,
                "Por favor, escolha um tamanho válido:\n\n"
                    . "1 - Pequena (P)\n"
                    . "2 - Média (M)\n"
                    . "3 - Grande (G)",
                $restaurant
            );
            return;
        }

        $data = $session->data ?? [];
        $data['size'] = $size;
        $session->update(['data' => $data, 'step' => 'awaiting_protein']);

        $menu = DailyMenu::with('items.dish.category')->find($session->menu_id);
        $proteinItems = $menu?->items->filter(fn($item) => $item->dish?->category?->name === 'Carnes' || $item->dish?->category?->name === 'Proteínas');

        if ($proteinItems && $proteinItems->count() > 0) {
            $rows = $proteinItems->map(fn($item) => [
                'id' => "protein_{$item->dish->id}",
                'title' => $item->dish->name,
                'description' => $item->dish->description ?? '',
            ])->toArray();

            $this->whatsApp->sendInteractiveList(
                $session->customer_phone,
                "🍖 Escolha sua Proteína",
                "Selecione até 1 opção de carne:",
                "Você pode escolher 1 proteína",
                [['title' => 'Carnes / Proteínas', 'rows' => $rows]],
                $restaurant
            );
        } else {
            $this->whatsApp->sendInteractiveButtons(
                $session->customer_phone,
                "🥗 *Hora dos Complementos!*\n\n"
                    . "Deseja adicionar complementos ao seu pedido?",
                [
                    ['type' => 'reply', 'reply' => ['id' => 'sides_yes', 'title' => 'Sim']],
                    ['type' => 'reply', 'reply' => ['id' => 'sides_no', 'title' => 'Não']],
                ],
                $restaurant
            );
        }
    }

    private function handleProteinSelection(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        if (str_starts_with($input, 'protein_')) {
            $dishId = str_replace('protein_', '', $input);
            $menu = DailyMenu::with('items.dish')->find($session->menu_id);
            $selectedItem = $menu?->items->firstWhere('dish_id', $dishId);

            $data = $session->data ?? [];
            $data['proteins'] = $data['proteins'] ?? [];

            $dishName = $selectedItem?->dish?->name ?? "Item #{$dishId}";
            $data['proteins'][] = [
                'dish_id' => (int) $dishId,
                'dish_name' => $dishName,
                'quantity' => 1,
            ];

            $session->update(['data' => $data]);
        }

        $this->whatsApp->sendInteractiveButtons(
            $session->customer_phone,
            "🥗 *Complementos & Frituras*\n\n"
                . "Deseja adicionar complementos ao seu pedido?",
            [
                ['type' => 'reply', 'reply' => ['id' => 'sides_yes', 'title' => 'Sim, quero!']],
                ['type' => 'reply', 'reply' => ['id' => 'sides_no', 'title' => 'Não, obrigado']],
            ],
            $restaurant
        );

        $session->update(['step' => 'awaiting_sides']);
    }

    private function handleSidesSelection(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        if ($input === 'sides_yes') {
            $menu = DailyMenu::with('items.dish.category')->find($session->menu_id);
            $sideItems = $menu?->items->filter(
                fn($item) => !in_array($item->dish?->category?->name, ['Carnes', 'Proteínas'])
            );

            if ($sideItems && $sideItems->count() > 0) {
                $rows = $sideItems->map(fn($item) => [
                    'id' => "side_{$item->dish->id}",
                    'title' => $item->dish->name,
                    'description' => $item->dish->description ?? '',
                ])->toArray();

                $this->whatsApp->sendInteractiveList(
                    $session->customer_phone,
                    "🥗 Complementos",
                    "Escolha seus complementos:",
                    "Você pode selecionar vários itens",
                    [['title' => 'Complementos & Frituras', 'rows' => $rows]],
                    $restaurant
                );

                $this->whatsApp->sendMessage(
                    $session->customer_phone,
                    "Você pode escolher vários complementos. "
                        . "Quando terminar, digite *'pronto'* ou *'finalizar'*.",
                    $restaurant
                );

                $data = $session->data ?? [];
                $data['awaiting_multiple_sides'] = true;
                $session->update(['data' => $data]);
                return;
            }
        }

        $this->askDeliveryType($session, $restaurant);
    }

    private function handleDeliveryType(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        if ($input === 'sides_yes' || str_starts_with($input, 'side_')) {
            $data = $session->data ?? [];
            $data['sides'] = $data['sides'] ?? [];

            if (str_starts_with($input, 'side_')) {
                $dishId = str_replace('side_', '', $input);
                $menu = DailyMenu::with('items.dish')->find($session->menu_id);
                $selectedItem = $menu?->items->firstWhere('dish_id', $dishId);

                if ($selectedItem) {
                    $existingKey = collect($data['sides'])->search(fn($s) => ($s['dish_id'] ?? null) == $dishId);
                    if ($existingKey !== false) {
                        $data['sides'][$existingKey]['quantity']++;
                    } else {
                        $data['sides'][] = [
                            'dish_id' => (int) $dishId,
                            'dish_name' => $selectedItem->dish->name,
                            'quantity' => 1,
                        ];
                    }
                    $session->update(['data' => $data]);
                    $this->whatsApp->sendMessage(
                        $session->customer_phone,
                        "✅ Adicionado! Digite *'pronto'* para finalizar ou escolha mais itens.",
                        $restaurant
                    );
                }
            } elseif ($input === 'sides_yes' || $input === 'pronto' || $input === 'finalizar' || $input === 'nao' || $input === 'não') {
                $this->askDeliveryType($session, $restaurant);
            }
            return;
        }

        $this->askDeliveryType($session, $restaurant);
    }

    private function handleAddress(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        $data = $session->data ?? [];

        if ($input === 'use_registered') {
            $customer = Customer::where('restaurant_id', $restaurant->id)
                ->where('phone', $session->customer_phone)
                ->first();

            if ($customer?->address) {
                $data['delivery_address'] = $customer->address;
                $session->update(['data' => $data]);
                $this->askPayment($session, $restaurant);
                return;
            }
        }

        if (str_starts_with($input, 'location_')) {
            $parts = explode(',', substr($input, 9));
            if (count($parts) >= 2) {
                $data['delivery_address'] = "Lat: {$parts[0]}, Lng: {$parts[1]}";
                $session->update(['data' => $data]);
                $this->askPayment($session, $restaurant);
                return;
            }
        }

        if (strlen($input) > 5 && $input !== 'address_manual') {
            $data['delivery_address'] = $input;
            $session->update(['data' => $data]);
            $this->askPayment($session, $restaurant);
            return;
        }

        $this->whatsApp->sendInteractiveButtons(
            $session->customer_phone,
            "📍 *Endereço de Entrega*\n\n"
                . "Digite seu endereço completo (rua, número, bairro) "
                . "ou compartilhe sua localização:",
            [
                ['type' => 'reply', 'reply' => ['id' => 'address_manual', 'title' => 'Digitar Endereço']],
                ['type' => 'reply', 'reply' => ['id' => 'use_registered', 'title' => 'Usar Endereço Salvo']],
            ],
            $restaurant
        );
    }

    private function handlePayment(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        $paymentMap = [
            'payment_pix' => 'pix',
            'payment_cash' => 'cash',
            'payment_credit' => 'credit_card',
            'payment_debit' => 'debit_card',
            'pix' => 'pix',
            'dinheiro' => 'cash',
            'credito' => 'credit_card',
            'debito' => 'debit_card',
            'cartao' => 'credit_card',
            '1' => 'pix',
            '2' => 'cash',
            '3' => 'credit_card',
            '4' => 'debit_card',
        ];

        $method = $paymentMap[$input] ?? null;

        if (!$method) {
            $this->whatsApp->sendInteractiveButtons(
                $session->customer_phone,
                "💳 *Forma de Pagamento*\n\nComo você gostaria de pagar?",
                [
                    ['type' => 'reply', 'reply' => ['id' => 'payment_pix', 'title' => 'PIX']],
                    ['type' => 'reply', 'reply' => ['id' => 'payment_cash', 'title' => 'Dinheiro']],
                    ['type' => 'reply', 'reply' => ['id' => 'payment_credit', 'title' => 'Cartão']],
                ],
                $restaurant
            );
            return;
        }

        $data = $session->data ?? [];
        $data['payment_method'] = $method;
        $session->update(['data' => $data]);

        if ($method === 'cash') {
            $this->whatsApp->sendMessage(
                $session->customer_phone,
                "💰 *Troco para quanto?*\n\nDigite o valor para o qual precisa de troco "
                    . "(ex: 50, 100) ou digite '0' se não precisar de troco.",
                $restaurant
            );
            $session->update(['step' => 'awaiting_change']);
        } else {
            $this->confirmOrder($session, $restaurant);
        }
    }

    private function handleChange(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        $data = $session->data ?? [];
        $amount = (float) str_replace(['R', '$', ' ', ','], [''], $input);
        if ($amount > 0) {
            $data['change_for'] = $amount;
        }
        $session->update(['data' => $data]);
        $this->confirmOrder($session, $restaurant);
    }

    private function handleConfirmation(WhatsAppSession $session, Restaurant $restaurant, string $input): void
    {
        if ($input === 'confirm_yes' || $input === 'sim' || $input === 's') {
            $this->placeOrder($session, $restaurant);
        } elseif ($input === 'confirm_no' || $input === 'nao' || $input === 'não' || $input === 'n') {
            $session->update([
                'step' => 'idle',
                'data' => [],
                'menu_id' => null,
            ]);
            $this->whatsApp->sendMessage(
                $session->customer_phone,
                "OK! Pedido cancelado. Quando quiser pedir, é só me chamar! 😊",
                $restaurant
            );
        } else {
            $this->whatsApp->sendInteractiveButtons(
                $session->customer_phone,
                "❓ Confirmar Pedido?\n\n"
                    . "Digite *'Sim'* para confirmar ou *'Não'* para cancelar.",
                [
                    ['type' => 'reply', 'reply' => ['id' => 'confirm_yes', 'title' => 'Sim, Confirmar']],
                    ['type' => 'reply', 'reply' => ['id' => 'confirm_no', 'title' => 'Não, Cancelar']],
                ],
                $restaurant
            );
        }
    }

    private function sendWelcomeMessage(WhatsAppSession $session, Restaurant $restaurant): void
    {
        $this->whatsApp->sendInteractiveButtons(
            $session->customer_phone,
            "Olá, {$session->customer_name}! 🙋‍♂️\n\n"
                . "Bem-vindo(a) ao *{$restaurant->name}*!\n\n"
                . "Para fazer um pedido, clique no botão abaixo:",
            [
                ['type' => 'reply', 'reply' => ['id' => 'start_order', 'title' => '🍽️ Ver Cardápio']],
            ],
            $restaurant
        );
    }

    private function askDeliveryType(WhatsAppSession $session, Restaurant $restaurant): void
    {
        $this->whatsApp->sendInteractiveButtons(
            $session->customer_phone,
            "📍 *Forma de Entrega*\n\nComo prefere receber seu pedido?",
            [
                ['type' => 'reply', 'reply' => ['id' => 'delivery_delivery', 'title' => 'Entrega']],
                ['type' => 'reply', 'reply' => ['id' => 'delivery_pickup', 'title' => 'Retirar no Local']],
            ],
            $restaurant
        );

        $session->update(['step' => 'awaiting_delivery_type']);
    }

    private function confirmOrder(WhatsAppSession $session, Restaurant $restaurant): void
    {
        $data = $session->data ?? [];
        $menu = DailyMenu::with('items.dish')->find($session->menu_id);

        $total = 0;
        $itemsSummary = [];

        $sizeLabels = ['small' => 'P', 'medium' => 'M', 'large' => 'G'];
        $sizePrices = ['small' => 'price_small', 'medium' => 'price_medium', 'large' => 'price_large'];

        $proteins = $data['proteins'] ?? [];
        foreach ($proteins as $protein) {
            $menuItem = $menu?->items->firstWhere('dish_id', $protein['dish_id']);
            $price = $menuItem?->{$sizePrices[$data['size']]} ?? 0;
            $subtotal = (float) $price * $protein['quantity'];
            $total += $subtotal;
            $itemsSummary[] = "{$protein['dish_name']} [" . ($sizeLabels[$data['size']] ?? '') . "]";
        }

        $sides = $data['sides'] ?? [];
        foreach ($sides as $side) {
            $menuItem = $menu?->items->firstWhere('dish_id', $side['dish_id']);
            $price = $menuItem?->{$sizePrices[$data['size']]} ?? 0;
            $subtotal = (float) $price * $side['quantity'];
            $total += $subtotal;
            $itemsSummary[] = "{$side['dish_name']} x{$side['quantity']}";
        }

        $deliveryFee = 0;
        if (($data['delivery_type'] ?? 'delivery') === 'delivery') {
            $deliveryFee = (float) ($restaurant->delivery_fee ?? 0);
            if ($data['free_delivery_min'] ?? false) {
                $deliveryFee = $total >= (float) ($data['free_delivery_min'] ?? 0) ? 0 : $deliveryFee;
            }
        }

        $grandTotal = $total + $deliveryFee;

        $data['total'] = $grandTotal;
        $data['subtotal'] = $total;
        $data['delivery_fee'] = $deliveryFee;
        $session->update(['data' => $data]);

        $confirmMessage = "📋 *RESUMO DO PEDIDO*\n\n"
            . "*Tamanho:* " . ($sizeLabels[$data['size']] ?? $data['size']) . "\n\n"
            . "*Itens:*\n" . implode("\n", array_map(fn($i) => "• {$i}", $itemsSummary)) . "\n\n";

        if ($deliveryFee > 0) {
            $confirmMessage .= "Taxa de Entrega: R$ " . number_format($deliveryFee, 2, ',', '.') . "\n";
        }

        $confirmMessage .= "*Total: R$ " . number_format($grandTotal, 2, ',', '.') . "*\n\n"
            . "Tipo: " . (($data['delivery_type'] ?? 'delivery') === 'delivery' ? '🚚 Entrega' : '🏪 Retirada') . "\n";

        if (!empty($data['delivery_address'])) {
            $confirmMessage .= "Endereço: {$data['delivery_address']}\n";
        }

        $paymentLabels = ['pix' => 'PIX', 'cash' => 'Dinheiro', 'credit_card' => 'Cartão de Crédito', 'debit_card' => 'Cartão de Débito'];
        $confirmMessage .= "Pagamento: " . ($paymentLabels[$data['payment_method']] ?? $data['payment_method']) . "\n";

        if (!empty($data['change_for'])) {
            $confirmMessage .= "Troco para: R$ " . number_format($data['change_for'], 2, ',', '.') . "\n";
        }

        $confirmMessage .= "\n*Confirmar pedido?*";

        $this->whatsApp->sendInteractiveButtons(
            $session->customer_phone,
            $confirmMessage,
            [
                ['type' => 'reply', 'reply' => ['id' => 'confirm_yes', 'title' => '✅ Confirmar']],
                ['type' => 'reply', 'reply' => ['id' => 'confirm_no', 'title' => '❌ Cancelar']],
            ],
            $restaurant
        );

        $session->update(['step' => 'confirming']);
    }

    private function placeOrder(WhatsAppSession $session, Restaurant $restaurant): void
    {
        try {
            $data = $session->data ?? [];
            $menu = DailyMenu::with('items.dish')->find($session->menu_id);

            $customer = Customer::firstOrCreate(
                ['restaurant_id' => $restaurant->id, 'phone' => $session->customer_phone],
                [
                    'restaurant_id' => $restaurant->id,
                    'name' => $session->customer_name,
                    'phone' => $session->customer_phone,
                    'address' => $data['delivery_address'] ?? null,
                ]
            );

            $orderItems = [];

            $proteins = $data['proteins'] ?? [];
            foreach ($proteins as $protein) {
                $menuItem = $menu?->items->firstWhere('dish_id', $protein['dish_id']);
                $sizePrices = ['small' => 'price_small', 'medium' => 'price_medium', 'large' => 'price_large'];
                $unitPrice = $menuItem?->{$sizePrices[$data['size']]} ?? 0;
                $orderItems[] = [
                    'dish_id' => $protein['dish_id'],
                    'daily_menu_item_id' => $menuItem?->id,
                    'dish_name' => $protein['dish_name'],
                    'size' => $data['size'],
                    'quantity' => $protein['quantity'],
                    'unit_price' => (float) $unitPrice,
                    'subtotal' => (float) $unitPrice * $protein['quantity'],
                ];
            }

            $sides = $data['sides'] ?? [];
            foreach ($sides as $side) {
                $menuItem = $menu?->items->firstWhere('dish_id', $side['dish_id']);
                $sizePrices = ['small' => 'price_small', 'medium' => 'price_medium', 'large' => 'price_large'];
                $unitPrice = $menuItem?->{$sizePrices[$data['size']]} ?? 0;
                $orderItems[] = [
                    'dish_id' => $side['dish_id'],
                    'daily_menu_item_id' => $menuItem?->id,
                    'dish_name' => $side['dish_name'],
                    'size' => $data['size'],
                    'quantity' => $side['quantity'],
                    'unit_price' => (float) $unitPrice,
                    'subtotal' => (float) $unitPrice * $side['quantity'],
                ];
            }

            $order = DB::transaction(function () use ($restaurant, $customer, $orderItems, $data) {
                $orderNumber = $this->orderService->createOrder([
                    'customer_id' => $customer->id,
                    'source' => 'whatsapp',
                    'items' => $orderItems,
                    'subtotal' => $data['subtotal'],
                    'delivery_fee' => $data['delivery_fee'] ?? 0,
                    'discount' => 0,
                    'total' => $data['total'],
                    'payment_method' => $data['payment_method'],
                    'delivery_type' => $data['delivery_type'] ?? 'pickup',
                    'delivery_address' => $data['delivery_address'] ?? null,
                ], $restaurant->id);

                return $order;
            });

            $this->whatsApp->sendOrderConfirmation($session->customer_phone, [
                'order_number' => $order->order_number,
                'items' => $orderItems,
                'subtotal' => $data['subtotal'],
                'delivery_fee' => $data['delivery_fee'] ?? 0,
                'total' => $data['total'],
                'delivery_type' => $data['delivery_type'] ?? 'pickup',
                'payment_method' => $data['payment_method'],
                'estimated_time' => 40,
            ], $restaurant);

            if ($data['payment_method'] === 'pix' && !empty($restaurant->pix_key)) {
                $this->whatsApp->sendPixPayment(
                    $session->customer_phone,
                    $restaurant->pix_key,
                    $data['total'],
                    $order->order_number,
                    $restaurant
                );
            }

            $session->update([
                'step' => 'completed',
                'data' => array_merge($data, ['order_id' => $order->id]),
                'is_completed' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp order placement failed: ' . $e->getMessage(), [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            $this->whatsApp->sendMessage(
                $session->customer_phone,
                "❌ *Desculpe, ocorreu um erro ao processar seu pedido!*\n\n"
                    . "Por favor, tente novamente ou entre em contato conosco diretamente.",
                $restaurant
            );

            $session->update(['step' => 'idle']);
        }
    }

    private function extractInput(array $message): string
    {
        if (isset($message['type']) && $message['type'] === 'interactive') {
            if (isset($message['button_id'])) {
                return $message['button_id'];
            }
            if (isset($message['list_id'])) {
                return $message['list_id'];
            }
        }

        if (isset($message['text'])) {
            $text = trim(mb_strtolower($message['text']));
            $text = preg_replace('/[^\w\sáéíóúàèìòùãõâêîôûç]/u', '', $text);
            return $text;
        }

        if (isset($message['latitude'])) {
            return "location_{$message['latitude']},{$message['longitude']}";
        }

        return '';
    }
}
