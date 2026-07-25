# Sistema de Pagamentos

## Visão Geral

O MenuHub possui dois contextos de pagamento:
1. **Pagamento de pedidos** — Cliente paga pelo pedido (PIX, dinheiro, cartão)
2. **Pagamento de assinatura** — Restaurante paga mensalidade do plano

## Gateway Unificado

`PaymentGatewayService` implementa `PaymentGatewayInterface` e suporta 4 gateways:

| Gateway | Configuração |
|---|---|
| `mock` | Modo de desenvolvimento (simula tudo) |
| `mercadopago` | `SERVICES_PIX_MERCADOPAGO_TOKEN` |
| `asaas` | `SERVICES_PIX_ASAAS_KEY` |
| `gerencianet` | `SERVICES_PIX_GERENCIANET_*` |

Selecionado via env: `SERVICES_PIX_GATEWAY=mock`

## Pagamento de Pedidos

### Fluxo

1. Admin registra pedido com método de pagamento
2. Se PIX: sistema gera QR Code e código copia e cola
3. Para pedidos WhatsApp: PIX é enviado automaticamente ao cliente
4. Admin confirma pagamento no painel → `payment.status` = `paid`
5. Se `payment_status` do pedido mudar → notificação ao cliente

### Tabela `payments`

| Campo | Descrição |
|---|---|
| `method` | `pix`, `cash`, `credit_card`, `debit` |
| `amount` | Valor |
| `status` | `pending`, `paid`, `refunded` |
| `transaction_id` | ID no gateway |
| `metadata` | Dados adicionais (JSON) |

## Pagamento de Assinatura (Billing)

A cobrança de assinatura é **manual**, realizada pelo Root.

### Fluxo

1. Root acessa **Faturamento → Restaurante**
2. Clica "Gerar Fatura"
3. Sistema:
   - Cria `Invoice` com valor do plano atual
   - Gera PIX via `PaymentGatewayService::charge()` (QR Code + copia e cola)
   - Exibe modal com dados PIX
4. Restaurante paga (fora do sistema)
5. Root confirma pagamento → `Invoice.status = paid`
6. Sistema estende `restaurant.paid_until` em +1 mês
7. Se não pago: Root pode marcar como `overdue` ou cancelar

### Status das Faturas

| Status | Significado |
|---|---|
| `pending` | Aguardando pagamento |
| `paid` | Pago, subscription estendida |
| `overdue` | Vencido, pode expirar subscription |
| `canceled` | Cancelado pelo Root |

### Atualização de Plano

Root pode fazer upgrade/downgrade de planos via BillingController. O plano entra em vigor imediatamente e as próximas faturas usarão o novo valor.

## Geração de PIX

O `PaymentGatewayService` gera payloads PIX com:
- QR Code (base64 para exibição)
- Código copia e cola (formato padronizado com CRC16)
- Valor e chave PIX do restaurante

## Integrações Futuras

- Recorrência automática de cobrança
- Boleto bancário
- Cartão de crédito recorrente
- Link de pagamento (checkout transparente)
