# Modelo de Dados

## Diagrama de Entidades

Usuário `1──N` Restaurante `1──N` [Pedidos, Pratos, Clientes, etc.]

## Estrutura

### plans

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| name | string | Nome do plano |
| slug | string | `essential`, `pro`, `enterprise` |
| price | decimal(8,2) | Preço mensal |
| max_users | integer | Limite de usuários |
| max_orders_monthly | integer | Limite mensal de pedidos (0 = ilimitado) |
| features | json (array) | Lista de features do plano |
| is_active | boolean | |

### restaurants

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| name | string | Nome do restaurante |
| slug | string | Slug único |
| email | string | |
| phone | string | |
| address | text | |
| logo | string | Path do logo |
| cover | string | Path da capa |
| pix_key | string | Chave PIX |
| whatsapp_number | string | Número WhatsApp |
| whatsapp_api_token | string | Token WhatsApp |
| whatsapp_phone_id | string | ID do telefone WhatsApp |
| whatsapp_business_account_id | string | Conta business WhatsApp |
| delivery_fee | decimal(8,2) | Taxa de entrega |
| minimum_order | decimal(8,2) | Pedido mínimo |
| opening_hours | json | Horários de funcionamento |
| is_active | boolean | |
| plan_id | integer (FK → plans) | |
| subscription_status | string | `active`, `trial`, `expired`, `canceled` |
| trial_ends_at | datetime | Fim do período trial |
| paid_until | datetime | Pago até |

### users

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| name | string | |
| email | string | |
| password | string | |
| role | string | `root`, `admin`, `user` |
| restaurant_id | integer (FK → restaurants) | |

### invoices

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| plan_id | integer (FK) | |
| amount | decimal(8,2) | Valor |
| status | string | `pending`, `paid`, `overdue`, `canceled` |
| due_date | date | Vencimento |
| paid_at | datetime | Pagamento |
| pix_qr_code | text | QR Code PIX |
| pix_copy_paste | text | Código copia e cola PIX |
| transaction_id | string | ID transação gateway |
| notes | text | |

### dish_categories

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| name | string | |
| description | string | |
| display_order | integer | |
| is_active | boolean | |

### dishes

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| dish_category_id | integer (FK) | |
| name | string | |
| description | text | |
| image | string | |
| price_small | decimal(8,2) | Preço pequeno |
| price_medium | decimal(8,2) | Preço médio |
| price_large | decimal(8,2) | Preço grande |
| is_gourmet | boolean | |
| max_selections | integer | |
| is_available | boolean | |
| is_active | boolean | |

### daily_menus

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| menu_date | date | |
| title | string | |
| notes | text | |
| is_published | boolean | |

### daily_menu_items

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| daily_menu_id | integer (FK) | |
| dish_id | integer (FK) | |
| price_small | decimal(8,2) | |
| price_medium | decimal(8,2) | |
| price_large | decimal(8,2) | |
| max_selections | integer | |
| is_available | boolean | |

### customers

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| name | string | |
| phone | string (encrypted) | |
| email | string (encrypted) | |
| address | text (encrypted) | |
| notes | text | |
| total_orders | integer | |
| total_spent | decimal(10,2) | |

### orders

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| customer_id | integer (FK) | |
| order_number | string | Ex: ORD-20260725-0001 |
| status | string | `received`, `preparing`, `out_for_delivery`, `completed`, `canceled` |
| source | string | `whatsapp`, `manual`, `app` |
| notes | text | |
| customer_notes | text | |
| subtotal | decimal(10,2) | |
| delivery_fee | decimal(8,2) | |
| discount | decimal(8,2) | |
| total | decimal(10,2) | |
| payment_method | string | `pix`, `cash`, `credit_card`, `debit` |
| payment_status | string | `pending`, `paid`, `refunded` |
| delivery_type | string | `delivery`, `pickup` |
| delivery_address | text | |
| ordered_at | datetime | |
| status_updated_at | datetime | |

### order_items

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| order_id | integer (FK) | |
| dish_id | integer (FK) | |
| daily_menu_item_id | integer (FK) | |
| dish_name | string | |
| size | string | `small`, `medium`, `large` |
| quantity | integer | |
| unit_price | decimal(8,2) | |
| subtotal | decimal(10,2) | |
| notes | text | |

### payments

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| order_id | integer (FK) | |
| method | string | |
| amount | decimal(10,2) | |
| status | string | |
| transaction_id | string | |
| metadata | json | |

### deliveries

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| order_id | integer (FK) | |
| type | string | `delivery`, `pickup` |
| address | text | |
| contact_name | string | |
| contact_phone | string | |
| status | string | |
| estimated_delivery_at | datetime | |
| delivered_at | datetime | |
| notes | text | |

### settings

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| key | string | |
| value | text | |
| group | string | `general`, `working_hours`, `notifications` |

### whatsapp_sessions

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | |
| restaurant_id | integer (FK) | |
| customer_phone | string | |
| customer_name | string | |
| step | string | Estado do fluxo conversacional |
| data | json | Dados da sessão |
| menu_id | integer (FK) | |
| is_completed | boolean | |
| last_interaction_at | datetime | |

### customer_tags e customer_customer_tag

| Coluna | Tipo | Descrição |
|---|---|---|
| id | integer (PK) | Tag |
| restaurant_id | integer (FK) | |
| name | string | |
| color | string | |
| | | |
| customer_id | integer (FK) | Pivot |
| customer_tag_id | integer (FK) | |

### activity_log

Tabela do pacote `spatie/laravel-activitylog` para auditoria.

## Relacionamentos Principais

```
Restaurant 1──N User
Restaurant 1──N DishCategory
Restaurant 1──N Dish
Restaurant 1──N DailyMenu
Restaurant 1──N Customer
Restaurant 1──N Order
Restaurant 1──N Payment
Restaurant 1──N Delivery
Restaurant 1──N Setting
Restaurant 1──N WhatsAppSession
Restaurant 1──N Invoice
Restaurant N──1 Plan

Plan 1──N Restaurant
Plan 1──N Invoice

DishCategory 1──N Dish
DailyMenu 1──N DailyMenuItem
Dish 1──N DailyMenuItem

Order 1──N OrderItem
Order 1──1 Payment
Order 1──1 Delivery

Customer 1──N Order
Customer N──M CustomerTag
```

## Multi-tenancy

Todas as tabelas de domínio (`dishes`, `orders`, `customers`, etc.) possuem `restaurant_id` e são filtradas automaticamente pelo `TenantScope`.
