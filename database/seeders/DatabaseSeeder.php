<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DailyMenu;
use App\Models\DailyMenuItem;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Plan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        User::create([
            'name' => env('ROOT_NAME', 'Root'),
            'email' => env('ROOT_EMAIL', 'root@menuhub.com'),
            'password' => Hash::make(env('ROOT_PASSWORD', 'password')),
            'role' => 'root',
            'restaurant_id' => null,
        ]);

        $proPlan = Plan::where('slug', 'pro')->first();

        $restaurant = Restaurant::create([
            'name' => 'Marmita do Zé',
            'slug' => 'marmita-do-ze',
            'email' => 'contato@marmitadoze.com',
            'phone' => '(11) 99999-8888',
            'address' => 'Rua das Flores, 123 - Centro',
            'pix_key' => 'contato@marmitadoze.com',
            'whatsapp_number' => '5511999998888',
            'delivery_fee' => 5.00,
            'minimum_order' => 15.00,
            'is_active' => true,
            'plan_id' => $proPlan?->id,
            'subscription_status' => 'active',
            'paid_until' => now()->addMonth(),
        ]);

        User::create([
            'name' => 'Admin Marmita do Zé',
            'email' => 'admin@marmitadoze.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'restaurant_id' => $restaurant->id,
        ]);

        User::create([
            'name' => 'João Cozinha',
            'email' => 'joao@marmitadoze.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'restaurant_id' => $restaurant->id,
        ]);

        $categories = [
            DishCategory::create(['restaurant_id' => $restaurant->id, 'name' => 'Carnes', 'description' => 'Opções de proteína']),
            DishCategory::create(['restaurant_id' => $restaurant->id, 'name' => 'Complementos', 'description' => 'Acompanhamentos']),
            DishCategory::create(['restaurant_id' => $restaurant->id, 'name' => 'Frituras', 'description' => 'Porções fritas']),
            DishCategory::create(['restaurant_id' => $restaurant->id, 'name' => 'Saladas', 'description' => 'Opções saudáveis']),
            DishCategory::create(['restaurant_id' => $restaurant->id, 'name' => 'Bebidas', 'description' => 'Refrigerantes e sucos']),
        ];

        $dishesData = [
            ['category' => 0, 'name' => 'Frango Grelhado', 'description' => 'Filé de frango temperado e grelhado na hora', 'small' => 16.00, 'medium' => 20.00, 'large' => 24.00],
            ['category' => 0, 'name' => 'Bife Acebolado', 'description' => 'Bife coberto com cebolas refogadas', 'small' => 18.00, 'medium' => 22.00, 'large' => 26.00],
            ['category' => 0, 'name' => 'Feijoada', 'description' => 'Feijoada completa com arroz, couve e farofa', 'small' => 17.00, 'medium' => 21.00, 'large' => 25.00],
            ['category' => 0, 'name' => 'Filé de Peixe', 'description' => 'Filé de merluza à milanesa', 'small' => 19.00, 'medium' => 23.00, 'large' => 27.00],
            ['category' => 1, 'name' => 'Arroz Branco', 'description' => null, 'small' => 4.00, 'medium' => 5.00, 'large' => 6.00],
            ['category' => 1, 'name' => 'Feijão Preto', 'description' => null, 'small' => 4.00, 'medium' => 5.00, 'large' => 6.00],
            ['category' => 1, 'name' => 'Farofa', 'description' => null, 'small' => 3.00, 'medium' => 4.00, 'large' => 5.00],
            ['category' => 1, 'name' => 'Molho Especial', 'description' => null, 'small' => 2.00, 'medium' => 3.00, 'large' => 4.00],
            ['category' => 2, 'name' => 'Batata Frita', 'description' => 'Porção de batata frita crocante', 'small' => 8.00, 'medium' => 12.00, 'large' => 16.00],
            ['category' => 2, 'name' => 'Mandioca Frita', 'description' => 'Porção de mandioca frita', 'small' => 8.00, 'medium' => 12.00, 'large' => 16.00],
            ['category' => 3, 'name' => 'Salada Verde', 'description' => 'Alface, rúcula, tomate e cenoura', 'small' => 6.00, 'medium' => 8.00, 'large' => 10.00],
            ['category' => 4, 'name' => 'Coca-Cola 350ml', 'description' => null, 'small' => 5.00, 'medium' => null, 'large' => null],
            ['category' => 4, 'name' => 'Suco Natural', 'description' => 'Suco de laranja ou limão', 'small' => 7.00, 'medium' => 9.00, 'large' => null],
        ];

        $dishes = [];
        foreach ($dishesData as $d) {
            $dishes[] = Dish::create([
                'restaurant_id' => $restaurant->id,
                'dish_category_id' => $categories[$d['category']]->id,
                'name' => $d['name'],
                'description' => $d['description'],
                'price_small' => $d['small'],
                'price_medium' => $d['medium'],
                'price_large' => $d['large'],
                'is_available' => true,
                'is_active' => true,
                'max_selections' => in_array($d['category'], [0, 2]) ? 1 : 3,
            ]);
        }

        $menu = DailyMenu::create([
            'restaurant_id' => $restaurant->id,
            'menu_date' => today()->format('Y-m-d'),
            'title' => 'Cardápio de Hoje',
            'is_published' => true,
        ]);

        foreach ($dishes as $dish) {
            DailyMenuItem::create([
                'daily_menu_id' => $menu->id,
                'dish_id' => $dish->id,
                'price_small' => $dish->price_small,
                'price_medium' => $dish->price_medium,
                'price_large' => $dish->price_large,
                'max_selections' => $dish->max_selections,
                'is_available' => true,
            ]);
        }

        $customers = [];
        $customerData = [
            ['name' => 'Maria Silva', 'phone' => '(11) 98888-7777', 'address' => 'Rua A, 100 - Centro'],
            ['name' => 'João Santos', 'phone' => '(11) 97777-6666', 'address' => 'Rua B, 200 - Jardins'],
            ['name' => 'Ana Oliveira', 'phone' => '(11) 96666-5555', 'address' => 'Rua C, 300 - Vila Nova'],
            ['name' => 'Carlos Lima', 'phone' => '(11) 95555-4444', 'address' => null],
        ];

        foreach ($customerData as $c) {
            $customers[] = Customer::create([
                'restaurant_id' => $restaurant->id,
                'name' => $c['name'],
                'phone' => $c['phone'],
                'address' => $c['address'],
            ]);
        }

        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'customer_id' => $customers[0]->id,
            'order_number' => 'ORD-' . today()->format('Ymd') . '-0001',
            'status' => 'received',
            'source' => 'whatsapp',
            'subtotal' => 32.00,
            'delivery_fee' => 5.00,
            'total' => 37.00,
            'payment_method' => 'pix',
            'delivery_type' => 'delivery',
            'delivery_address' => 'Rua A, 100 - Centro',
            'ordered_at' => now()->subHours(2),
        ]);

        OrderItem::create(['order_id' => $order->id, 'dish_id' => $dishes[0]->id, 'dish_name' => 'Frango Grelhado', 'size' => 'medium', 'quantity' => 1, 'unit_price' => 20.00, 'subtotal' => 20.00]);
        OrderItem::create(['order_id' => $order->id, 'dish_id' => $dishes[8]->id, 'dish_name' => 'Batata Frita', 'size' => 'medium', 'quantity' => 1, 'unit_price' => 12.00, 'subtotal' => 12.00]);

        $order2 = Order::create([
            'restaurant_id' => $restaurant->id,
            'customer_id' => $customers[1]->id,
            'order_number' => 'ORD-' . today()->format('Ymd') . '-0002',
            'status' => 'preparing',
            'source' => 'whatsapp',
            'subtotal' => 48.00,
            'delivery_fee' => 0,
            'total' => 48.00,
            'payment_method' => 'cash',
            'delivery_type' => 'pickup',
            'ordered_at' => now()->subHour(),
        ]);

        OrderItem::create(['order_id' => $order2->id, 'dish_id' => $dishes[2]->id, 'dish_name' => 'Feijoada', 'size' => 'large', 'quantity' => 1, 'unit_price' => 25.00, 'subtotal' => 25.00]);
        OrderItem::create(['order_id' => $order2->id, 'dish_id' => $dishes[4]->id, 'dish_name' => 'Arroz Branco', 'size' => 'large', 'quantity' => 1, 'unit_price' => 6.00, 'subtotal' => 6.00]);
        OrderItem::create(['order_id' => $order2->id, 'dish_id' => $dishes[5]->id, 'dish_name' => 'Feijão Preto', 'size' => 'large', 'quantity' => 1, 'unit_price' => 6.00, 'subtotal' => 6.00]);
        OrderItem::create(['order_id' => $order2->id, 'dish_id' => $dishes[12]->id, 'dish_name' => 'Suco Natural', 'size' => 'large', 'quantity' => 1, 'unit_price' => 9.00, 'subtotal' => 9.00]);
        OrderItem::create(['order_id' => $order2->id, 'dish_id' => $dishes[11]->id, 'dish_name' => 'Coca-Cola 350ml', 'size' => 'small', 'quantity' => 2, 'unit_price' => 5.00, 'subtotal' => 10.00]);

        Setting::create(['restaurant_id' => $restaurant->id, 'key' => 'name', 'value' => 'Marmita do Zé', 'group' => 'general']);
        Setting::create(['restaurant_id' => $restaurant->id, 'key' => 'email', 'value' => 'contato@marmitadoze.com', 'group' => 'general']);
        Setting::create(['restaurant_id' => $restaurant->id, 'key' => 'phone', 'value' => '(11) 99999-8888', 'group' => 'general']);
        Setting::create(['restaurant_id' => $restaurant->id, 'key' => 'address', 'value' => 'Rua das Flores, 123 - Centro', 'group' => 'general']);
        Setting::create(['restaurant_id' => $restaurant->id, 'key' => 'pix_key', 'value' => 'contato@marmitadoze.com', 'group' => 'general']);
        Setting::create(['restaurant_id' => $restaurant->id, 'key' => 'delivery_fee', 'value' => '5.00', 'group' => 'general']);
        Setting::create(['restaurant_id' => $restaurant->id, 'key' => 'whatsapp', 'value' => '5511999998888', 'group' => 'general']);
        Setting::create(['restaurant_id' => $restaurant->id, 'key' => 'instagram', 'value' => '@marmitadoze', 'group' => 'general']);
    }
}
