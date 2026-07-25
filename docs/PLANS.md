# Sistema de Planos

## Modelo de Assinatura

MenuHub opera com planos de assinatura mensal. A cobrança é manual (realizada pelo Root via PIX) — sem recorrência automática.

## Planos Disponíveis

| Feature | Essential (R$49) | Pro (R$97) | Enterprise (R$197) |
|---|---|---|---|
| Cardápio básico | ✅ | ✅ | ✅ |
| Bot WhatsApp | ✅ | ✅ | ✅ |
| Pagamento PIX | ✅ | ✅ | ✅ |
| **Relatórios** | ❌ | ✅ | ✅ |
| **Gestão de entregas** | ❌ | ✅ | ✅ |
| Suporte prioritário | ❌ | ✅ | ✅ |
| Acesso API | ❌ | ❌ | ✅ |
| Pedidos ilimitados | ❌ | ❌ | ✅ |
| Gerente dedicado | ❌ | ❌ | ✅ |
| Suporte 24h | ❌ | ❌ | ✅ |

### Limites

| Plano | Max. Usuários | Max. Pedidos/mês |
|---|---|---|
| Essential | 2 | 300 |
| Pro | 5 | 1000 |
| Enterprise | 20 | Ilimitado (0) |

## Implementação

### Feature Map

O `Plan` model possui um `$featureMap` estático que mapeia slugs a conjuntos de features:

```php
essential  → basic_menu, whatsapp_bot, pix_payment
pro        → + reports, delivery_management, priority_support
enterprise → + api_access, unlimited_orders, dedicated_manager, support_24h
```

Método `Plan::hasFeature(string $feature): bool` verifica se o plano possui determinada feature.

### Controle de Acesso

**Middlewares** (aplicados ao grupo de rotas autenticadas):
- `CheckSubscription`: Bloqueia se `subscription_status ∈ {expired, canceled}`. Auto-expira trials vencidos. Redireciona para `/assinatura-expirada`.
- `CheckRestaurantActive`: Bloqueia se `restaurant.is_active = false` (Root bypass).

**Feature Gate** em controllers:
- Método `Controller::authorizePlanFeature(Request, string $feature)` aborta 403 se o plano não incluir a feature.
- Aplicado via `middleware()` no construtor de `ReportController` e `DeliveryController`.

**Limites de usuário**:
- `Root\UserController::store()` e `update()` verificam `plan.max_users` antes de criar ou transferir usuários.

**Limite de pedidos**:
- `OrderService::createOrder()` verifica `plan.max_orders_monthly` (0 = ilimitado), lança `RuntimeException` se excedido.

**Sidebar**:
- Itens de menu com chave `'feature'` são filtrados se o plano do usuário não incluir a feature.

## Fluxo de Cobrança (Root)

1. Root acessa **Faturamento** → seleciona restaurante
2. Gera fatura: sistema cria invoice com valor do plano + gera PIX (QR Code + copia e cola)
3. Root confirma pagamento → invoice marcada como `paid` → subscription estendida (`paid_until`)
4. Se não pago → Root marca como `overdue`
5. Cancelamento de invoice permite gerar nova

### Tabela `invoices`

| Campo | Descrição |
|---|---|
| `status` | `pending`, `paid`, `overdue`, `canceled` |
| `amount` | Valor baseado no `plan.price` |
| `pix_qr_code` | QR Code base64 |
| `pix_copy_paste` | Código PIX copia e cola |

### Modelo de Dados

`restaurants` possui:
- `plan_id` → FK para `plans`
- `subscription_status` → `active`, `trial`, `expired`, `canceled`
- `trial_ends_at` → data de término do trial
- `paid_until` → data até quando está pago
