# Módulos do Sistema

## 1. Autenticação e Perfis

- Registro, login, recuperação de senha, verificação de e-mail
- Três roles: `root` (admin global), `admin` (gerente do restaurante), `user` (funcionário)
- Root gerencia restaurantes e usuários no painel `/root`
- Admin gerencia o dia-a-dia do restaurante

## 2. Gestão de Restaurante

- Configurações: dados cadastrais, logotipo, capa
- Horários de funcionamento
- Chave PIX, taxa de entrega, pedido mínimo
- Integração WhatsApp (token, phone ID)

## 3. Cardápio Digital

### Categorias de Pratos
- CRUD com ordenação e ativação/desativação

### Pratos
- Nome, descrição, imagem, preços por tamanho (P/M/G)
- Controle de disponibilidade e seleção máxima por item
- Classificação gourmet

### Cardápio Diário
- Criação/clone automático do cardápio do dia
- Seleção de pratos com preços e limites por item
- Publicação para envio via WhatsApp
- Envio em massa para todos os clientes com WhatsApp

## 4. Pedidos

### Criação
- Manual (admin) ou via WhatsApp bot
- Suporte a múltiplos tamanhos (small/medium/large)
- Cálculo automático de subtotal, taxa de entrega, total
- Geração de número sequencial (`ORD-YYYYMMDD-XXXX`)

### Visualização Kanban
- Quadro com colunas: Recebido → Preparando → Saiu p/ Entrega → Entregue
- Arrastar e soltar para mudar status (Alpine.js)

### Ciclo de Vida
- `received` → `preparing` → `out_for_delivery` → `completed`
- Qualquer status → `canceled`
- Eventos broadcast em tempo real (Pusher)
- Notificações WhatsApp automáticas

## 5. Clientes

- Cadastro com busca e autocomplete
- Segmentação por tags
- Anonimização de dados (LGPD)
- Histórico completo de pedidos
- Estatísticas: total de pedidos, total gasto
- Dados sensíveis criptografados (phone, email, endereço)

## 6. Pagamentos

- Suporte a PIX, dinheiro, crédito, débito
- Integração com gateway (Mercado Pago, Asaas, Gerencianet)
- Status de pagamento vinculado ao pedido
- Cobranças de assinatura (via Billing/Root)

## 7. Entregas

- Cadastro de entrega vinculada ao pedido
- Controle de status: `pending` → `in_progress` → `delivered` → `cancelled`
- Endereço, contato, previsão de entrega
- Feature gated por plano (`delivery_management`)

## 8. Relatórios

- **Financeiro:** receita mensal, ticket médio, pedidos por período
- **Pratos:** mais vendidos por quantidade
- **Combinações:** pares de pratos mais frequentes
- **Horários:** distribuição de pedidos por hora do dia
- **Demanda:** previsão semanal baseada em histórico de 4 semanas
- Feature gated por plano (`reports`)

## 9. Impressão Térmica

- Suporte a 4 drivers: `raw` (log), `network` (socket), `windows` (PowerShell), `linux` (lp)
- Formatação 58mm e 80mm
- Comandos ESC/POS para rede

## 10. Dashboard

- Estatísticas do dia (cache 60s): pedidos, receita, ticket médio
- Gráfico de 14 dias (pedidos × receita)
- Distribuição de status
- Pratos mais vendidos do dia
- Últimos pedidos

## 11. Root Admin

- Lista de restaurantes com métricas
- Criação/edição de restaurantes
- Gerenciamento de usuários (limite por plano)
- Dashboard global
- **Billing:** geração de cobranças PIX, confirmação de pagamento, upgrade/downgrade de planos

## 12. Geocodificação

- Suporte a Google Maps e OpenStreetMap
- Cálculo de distância (Haversine)
- Integração futura com rotas de entrega

## 13. Activity Log

- Auditoria de alterações em clientes (Spatie Activitylog)
- Log de mudanças em pedidos
- Limpeza automática após 365 dias
