# Arquitetura do Sistema

## Visão Geral

MenuHub é um sistema SaaS multi-tenant construído sobre Laravel 12 com SQLite, seguindo o padrão MVC com camada de serviços.

## Camadas

```
Request → Middleware → Controller → Service → Model → Database
                            ↓
                        Response (Blade + Alpine.js)
```

### 1. Middleware Layer

| Middleware | Função | Grupo |
|---|---|---|
| `TenantMiddleware` | Redireciona usuários sem restaurante | `tenant` |
| `CheckRole` | Aborta 403 se role não permitida | `role:root`, `role:admin` |
| `CheckSubscription` | Bloqueia expirados/cancelados, auto-expira trial | `subscription` |
| `CheckRestaurantActive` | Bloqueia restaurantes inativos | `restaurant.active` |

### 2. Controller Layer

- **Base Controller** (`Controller.php`): método `authorizePlanFeature()` para controle de features por plano
- 19 controladores organizados por domínio
- Root controllers sob namespace `Root\`

### 3. Service Layer

12 serviços com interfaces contratuais onde aplicável:

| Serviço | Interface | Finalidade |
|---|---|---|
| `DashboardService` | — | Métricas do dashboard com cache de 60s |
| `OrderService` | — | Criação/atualização de pedidos, validação de limites |
| `WhatsAppService` | `WhatsAppInterface` | Comunicação WhatsApp Cloud API |
| `WhatsAppBotService` | — | Máquina de estados do bot conversacional |
| `PaymentGatewayService` | `PaymentGatewayInterface` | Pagamentos PIX (mock/MP/Asaas/Gerencianet) |
| `ThermalPrinterService` | `ThermalPrinterInterface` | Impressão térmica (58mm/80mm) |
| `GeocodingService` | `GeocodingInterface` | Geocodificação (Google/OSM) |
| `ReportService` | — | Relatórios financeiros e de vendas |
| `DemandPredictionService` | — | Previsão de demanda semanal |
| `DailyMenuService` | — | Gerenciamento de cardápios diários |
| `SettingService` | — | Gerenciamento de configurações |

### 4. Multi-tenancy

- Escopo global `TenantScope` aplicado a 10 models via `AppServiceProvider::boot()`
- Filtragem automática por `restaurant_id` em todas as queries
- Tratamento especial para `DailyMenuItem` (via `dailyMenu.restaurant_id`) e `OrderItem` (via `order.restaurant_id`)

### 5. Eventos e Broadcast

- `OrderCreated` — broadcast para canal `restaurant.{id}.orders`
- `OrderStatusChanged` — broadcast + notificação + atualização de estatísticas
- Listeners: `LogOrderActivity`, `SendOrderStatusNotification`, `UpdateCustomerStats`
- Jobs assíncronos: `SendOrderNotification`, `ProcessWhatsAppMessage`

## Fluxo de Requisição

```
Usuário → Rota → Middleware (auth → tenant → subscription → active → role)
       → Controller → Service → Model → Database
       → View (Blade) → Alpine.js (interatividade)
```

## Rotas

| Grupo | Prefixo | Middleware |
|---|---|---|
| Público | `/` | — |
| Root | `/root` | `auth + verified + role:root` |
| Admin | `/` | `auth + tenant + subscription + active + role:admin` |
| User | `/` | `auth + tenant + subscription + active + role:admin,user` |
| Webhook | `/webhook/whatsapp` | — |
| Autenticação | — | `guest` ou `auth` |
