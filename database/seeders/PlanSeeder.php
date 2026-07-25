<?php

namespace Database\Seeders;

use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Essential',
                'slug' => 'essential',
                'price' => 49.00,
                'max_users' => 1,
                'max_orders_monthly' => 200,
                'features' => [
                    'Cardápio digital ilimitado',
                    '200 pedidos/mês',
                    'WhatsApp Bot',
                    'Pagamento PIX',
                    'Suporte por e-mail',
                ],
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 97.00,
                'max_users' => 3,
                'max_orders_monthly' => 1000,
                'features' => [
                    'Tudo do Essential',
                    '1.000 pedidos/mês',
                    '3 usuários',
                    'Relatórios financeiros',
                    'Gestão de entregas',
                    'Suporte prioritário',
                ],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 197.00,
                'max_users' => 10,
                'max_orders_monthly' => 0,
                'features' => [
                    'Tudo do Pro',
                    'Pedidos ilimitados',
                    '10 usuários',
                    'Múltiplos cardápios',
                    'API de integração',
                    'Gerente de conta dedicado',
                    'Suporte 24h',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
