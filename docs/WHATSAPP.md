# Integração WhatsApp

## Visão Geral

O MenuHub utiliza a **WhatsApp Cloud API** (v21.0) da Meta para:
- Envio automático de cardápios
- Bot conversacional para pedidos
- Notificações de status de pedido
- Confirmação e envio de PIX

## Configuração

```env
WHATSAPP_API_TOKEN=seu_token_aqui
WHATSAPP_PHONE_ID=id_do_telefone
WHATSAPP_BUSINESS_ACCOUNT_ID=id_da_conta_business
WHATSAPP_VERIFY_TOKEN=menuhub_webhook_2024
```

A URL do webhook deve apontar para: `https://seudominio.com/webhook/whatsapp`

### Verificação do Webhook

O método `verify()` do `WhatsAppWebhookController` responde ao challenge de verificação usando o token configurado.

## WhatsAppService

Implementa `WhatsAppInterface` com os seguintes métodos:

| Método | Função |
|---|---|
| `sendMessage(to, body)` | Envio de texto |
| `sendTemplate(to, template, params)` | Template de mensagem |
| `sendImage(to, imageUrl, caption)` | Imagem |
| `sendInteractiveList(to, header, body, sections)` | Lista interativa |
| `sendInteractiveButtons(to, header, body, buttons)` | Botões interativos |
| `markAsRead(messageId)` | Marcar como lido |
| `parseWebhook(payload)` | Parse da mensagem recebida (texto, interactive, location, order) |
| `sendPixPayment(to, value, pixCode, qrCode)` | Envia PIX |
| `sendOrderConfirmation(to, order)` | Confirmação de pedido |
| `sendStatusUpdate(to, order)` | Atualização de status |
| `sendMenuToCustomer(to, menu, restaurant)` | Cardápio completo |

## Bot Conversacional (WhatsAppBotService)

### Arquitetura

Máquina de estados que gerencia o fluxo completo de pedidos.

### Estados

```
idle → awaiting_size → awaiting_protein → awaiting_sides
     → awaiting_delivery_type → awaiting_address
     → awaiting_payment → awaiting_change
     → confirming → completed
```

### Fluxo

1. **idle**: Cliente envia "cardápio" ou qualquer mensagem → bot responde com boas-vindas e pergunta se quer fazer pedido
2. **awaiting_size**: Bot envia cardápio categorizado com botões de seleção de tamanho
3. **awaiting_protein**: Se o prato exige proteína → seleção de proteína
4. **awaiting_sides**: Seleção de acompanhamentos (limitado por `max_selections`)
5. **awaiting_delivery_type**: Escolhe entre delivery ou retirada
6. **awaiting_address**: Se delivery → informa endereço
7. **awaiting_payment**: Escolhe forma de pagamento (PIX ou dinheiro)
8. **awaiting_change**: Se dinheiro → informa valor do troco
9. **confirming**: Bot resume pedido e confirma
10. **completed**: Pedido registrado → notificação ao admin

### Recuperação

- Sessões expiram (`last_interaction_at`)
- Se o cliente retorna, o bot retoma do estado atual
- Se `is_completed`, reinicia o fluxo

### Webhook

Rota `POST /webhook/whatsapp` → `WhatsAppWebhookController@handle` → despacha `ProcessWhatsAppMessage` job (fila).

## Notificações

### Automáticas

- **Confirmação de pedido**: enviada ao cliente via `WhatsAppService::sendOrderConfirmation()`
- **Atualização de status**: enviada ao cliente via `WhatsAppService::sendStatusUpdate()`
- **Envio de cardápio**: disparado manualmente pelo admin via `MenuDispatchController@dispatch`

### Job

`SendOrderNotification` gerencia o envio de notificações WhatsApp de forma assíncrona.

## Envio de Cardápio

1. Admin publica cardápio em `daily-menus`
2. Clica em "Enviar via WhatsApp"
3. Sistema busca todos os clientes com telefone
4. Para cada cliente:
   - Cria/atualiza `WhatsAppSession`
   - Envia cardápio com botões interativos
5. Cliente responde → bot gerencia o pedido
