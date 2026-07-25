# Especificação de Requisitos de Software

**Projeto:** MenuHub (MarmitaBot SaaS)
**Versão:** 1.0.0
**Data:** 25 de Julho de 2026

---

## 1. Visão Geral do Produto

O MenuHub é uma plataforma SaaS multi-tenant para gestão de restaurantes, cardápios digitais e pedidos via WhatsApp. O sistema substitui o envio manual de listas de texto estáticas por cardápios diários interativos, fluxos de checkout conversacionais automatizados (bot), gestão em tempo real de comandas de cozinha (Kanban) e inteligência analítica de desempenho.

### Público-alvo
- Restaurantes, marmitarias, cantinas
- Segmento de refeições por quilo e marmitas delivery
- Pequenos e médios estabelecimentos que usam WhatsApp como canal principal

### Diferenciais
- Bot conversacional WhatsApp sem digitação manual
- Multi-tenancy com isolamento completo entre restaurantes
- Planos com feature gating e limites progressivos
- Impressão térmica 58mm/80mm
- LGPD compliance nativo

---

## 2. Atores do Sistema

| Ator | Descrição | Permissões |
|---|---|---|
| **Root** | Administrador global da plataforma | Gerenciar restaurantes, usuários, planos, cobranças. Acesso total a todos os dados. |
| **Admin** | Gerente/Proprietário do restaurante | Gerenciar cardápio, pedidos, clientes, relatórios, configurações |
| **User** | Funcionário do restaurante | Visualizar dashboard, gerenciar pedidos (Kanban), atualizar status |
| **Cliente** | Consumidor final | Realiza pedidos via WhatsApp, recebe notificações |
| **Sistema** | Processos automáticos | Envio de cardápios, notificações, webhook, jobs em fila |

---

## 3. Módulos e Requisitos Funcionais (RF)

### Módulo A: Autenticação e Controle de Acesso

#### RF01 — Autenticação de Usuários
**Descrição:** O sistema deve permitir login, registro e recuperação de senha.
- Login com e-mail e senha
- Registro de novos usuários
- Recuperação de senha via e-mail
- Verificação de e-mail (opcional)
- Confirmação de senha para ações sensíveis

#### RF02 — Controle de Perfis (Roles)
**Descrição:** O sistema deve possuir três níveis de acesso.
- **Root:** acesso irrestrito, painel `/root`
- **Admin:** acesso completo ao restaurante
- **User:** acesso restrito a pedidos e dashboard
- Middleware `CheckRole` bloqueia 403 se role não autorizada

#### RF03 — Multi-tenancy
**Descrição:** Cada restaurante deve ter dados isolados.
- Escopo global `TenantScope` filtra automaticamente por `restaurant_id`
- Aplicado a 10 models: Customer, DailyMenu, DailyMenuItem, Delivery, Dish, DishCategory, Order, OrderItem, Payment, Setting
- Usuários sem `restaurant_id` são redirecionados ao cadastro

---

### Módulo B: Gestão do Restaurante

#### RF04 — Configurações do Restaurante
**Descrição:** O admin deve configurar os dados do estabelecimento.
- Nome, slug, e-mail, telefone, endereço
- Logotipo e imagem de capa (upload)
- Chave PIX
- Taxa de entrega e pedido mínimo
- Horários de funcionamento
- Ativação/desativação do restaurante (Root também controla)
- Integração WhatsApp (token, phone ID, business account ID)

#### RF05 — Gerenciamento de Usuários (Root)
**Descrição:** O Root deve gerenciar usuários da plataforma.
- Listar todos os usuários não-root
- Criar usuário em restaurante específico
- Alterar role e restaurante
- **Regra de negócio:** Não pode exceder `plan.max_users` do restaurante
- Root não pode ser excluído

---

### Módulo C: Cardápio Digital

#### RF06 — Categorias de Pratos
**Descrição:** O admin deve organizar pratos em categorias.
- CRUD de categorias (nome, descrição, ordem, ativo/inativo)
- Ordenação por `display_order`

#### RF07 — Cadastro de Pratos
**Descrição:** O sistema deve permitir cadastro completo de pratos.
- Nome, descrição, imagem
- Preços por tamanho: pequeno (P), médio (M), grande (G) — valores opcionais
- Categoria, disponibilidade, ativo/inativo
- Flag gourmet (preço adicional)
- Limite de seleções por prato (`max_selections`)
- Upload e substituição de imagem

#### RF08 — Cardápio Diário
**Descrição:** O admin deve montar o cardápio do dia.
- Seleção visual de pratos do acervo
- Preços e limites por item no dia (podem diferir do prato base)
- Publicação do cardápio
- **Regra de negócio:** Apenas 1 cardápio por data (get-or-create)
- Despublicação

#### RF09 — Envio do Cardápio via WhatsApp
**Descrição:** O sistema deve enviar o cardápio publicado para todos os clientes com WhatsApp.
- Disparo imediato ("Enviar Agora")
- Cria/atualiza sessão WhatsApp para cada cliente
- Envio de cardápio categorizado com botões interativos

---

### Módulo D: Pedidos

#### RF10 — Criação de Pedido (Manual)
**Descrição:** O admin deve registrar pedidos manualmente.
- Seleção de cliente (autocomplete)
- Seleção de itens do cardápio do dia
- Cálculo automático de subtotal, taxa de entrega, total
- Método de pagamento e tipo de entrega
- Número sequencial (`ORD-YYYYMMDD-XXXX`)
- **Regra de negócio:** Verifica `plan.max_orders_monthly` antes de criar; se excedido, bloqueia com mensagem de erro

#### RF11 — Criação de Pedido (WhatsApp Bot)
**Descrição:** O cliente deve fazer pedidos via WhatsApp de forma conversacional.
- **Fluxo completo:** tamanho → proteína → acompanhamentos → tipo de entrega → pagamento → confirmação
- Ver REQUISITOS.md Módulo H para detalhes do fluxo

#### RF12 — Kanban de Pedidos
**Descrição:** O sistema deve exibir pedidos em quadro Kanban por status.
- Colunas: Recebido → Preparando → Saiu para Entrega → Finalizado + Cancelado
- Atualização de status via clique (arrastar/drop opcional)
- Broadcast em tempo real via Pusher (eventos `order.status.changed`)

#### RF13 — Ciclo de Vida do Pedido
**Descrição:** O sistema deve gerenciar transições de status válidas.
- `received` → `preparing` → `out_for_delivery` → `completed`
- Qualquer status → `canceled`
- Notificação automática ao cliente via WhatsApp em cada transição
- Log de atividade do pedido
- Atualização de estatísticas do cliente (`total_orders`, `total_spent`)

#### RF14 — Edição e Exclusão de Pedidos
**Descrição:** O admin deve editar ou excluir pedidos.
- Edição de dados e itens do pedido
- Exclusão em transação com itens

---

### Módulo E: Clientes

#### RF15 — Cadastro de Clientes
**Descrição:** O sistema deve gerenciar a base de clientes.
- Nome, telefone (criptografado), e-mail (criptografado), endereço (criptografado)
- Observações
- Busca com autocomplete para seleção em pedidos
- Segmentação por tags

#### RF16 — Tags de Clientes
**Descrição:** O admin deve segmentar clientes com tags.
- CRUD de tags (nome, cor)
- Associação N:N com clientes
- Filtro de clientes por tag

#### RF17 — Anonimização de Dados (LGPD)
**Descrição:** O sistema deve permitir anonimizar dados do cliente.
- Substitui nome por `[Removido]`
- Remove telefone, e-mail, endereço e observações
- Preserva dados agregados (total de pedidos, total gasto)
- Ação irreversível via botão "Anonimizar"

#### RF18 — Histórico do Cliente
**Descrição:** O sistema deve exibir histórico completo do cliente.
- Lista de pedidos realizados
- Total de pedidos e total gasto
- Tags associadas

---

### Módulo F: Pagamentos

#### RF19 — Pagamento de Pedidos
**Descrição:** O sistema deve registrar pagamentos dos pedidos.
- Métodos: PIX, dinheiro, cartão de crédito, cartão de débito
- Status: pendente, pago, estornado
- Geração de PIX via gateway (QR Code + copia e cola)
- Atualização automática do `payment_status` do pedido

#### RF20 — Gateway de Pagamento Unificado
**Descrição:** O sistema deve suportar múltiplos gateways PIX.
- Gateways: Mock (dev), Mercado Pago, Asaas, Gerencianet
- Selecionado via env `SERVICES_PIX_GATEWAY`
- Interface contratual `PaymentGatewayInterface`

---

### Módulo G: Entregas

#### RF21 — Gestão de Entregas
**Descrição:** O sistema deve gerenciar entregas de pedidos.
- CRUD de entregas vinculadas a pedidos
- Status: pendente → em andamento → entregue → cancelado
- Endereço, contato, previsão de entrega
- **Regra de negócio:** Feature gated por plano (`delivery_management`)
- Ao marcar como entregue, atualiza pedido para `completed`

---

### Módulo H: Bot WhatsApp

#### RF22 — Webhook WhatsApp
**Descrição:** O sistema deve receber e processar mensagens do WhatsApp.
- Rota `POST /webhook/whatsapp` recebe payload da Meta
- Rota `GET /webhook/whatsapp` responde ao challenge de verificação
- Processamento assíncrono via `ProcessWhatsAppMessage` job (fila)

#### RF23 — Máquina de Estados do Bot
**Descrição:** O bot deve guiar o cliente por um fluxo conversacional completo.

**Estados do fluxo:**
| Estado | Ação do Bot | Entrada do Cliente |
|---|---|---|
| `idle` | Envia boas-vindas e pergunta se quer pedir | "sim", "cardápio", qualquer texto |
| `awaiting_size` | Envia cardápio categorizado com botões de tamanho | Seleciona P, M ou G |
| `awaiting_protein` | Se prato exige proteína, envia opções | Seleciona proteína |
| `awaiting_sides` | Envia complementos disponíveis (limite `max_selections`) | Seleciona acompanhamentos |
| `awaiting_delivery_type` | Pergunta entrega ou retirada | Escolhe delivery/pickup |
| `awaiting_address` | Se delivery, solicita endereço | Envia endereço |
| `awaiting_payment` | Pergunta forma de pagamento | Escolhe PIX, dinheiro |
| `awaiting_change` | Se dinheiro, pergunta valor do troco | Informa valor |
| `confirming` | Exibe resumo do pedido e confirma | Confirma ou cancela |
| `completed` | Pedido registrado, notifica admin | — |

#### RF24 — Disparo de Cardápio Interativo
**Descrição:** O bot deve enviar cardápio usando componentes oficiais da WhatsApp Cloud API.
- List Messages para categorias de pratos
- Interactive Buttons para ações e seleções
- Seções organizadas por categoria

#### RF25 — Notificações WhatsApp Automáticas
**Descrição:** O sistema deve notificar clientes via WhatsApp.
- **Confirmação de pedido:** resumo completo, valor total, estimativa
- **PIX:** envio automático de chave PIX copia e cola + QR Code
- **Atualização de status:** notificação a cada transição no Kanban

---

### Módulo I: Relatórios e Analytics

#### RF26 — Relatório Financeiro
**Descrição:** O sistema deve exibir receita por período.
- Receita mensal, ticket médio, total de pedidos
- Período selecionável (mês a mês dentro do ano)

#### RF27 — Relatório de Pratos
**Descrição:** O sistema deve exibir pratos mais vendidos.
- Top N pratos por quantidade vendida
- Período selecionável

#### RF28 — Relatório de Combinações
**Descrição:** O sistema deve exibir combinações de pratos mais frequentes.
- Pares de pratos comprados juntos
- Útil para criar combos/promoções

#### RF29 — Relatório de Horários
**Descrição:** O sistema deve exibir distribuição de pedidos por hora.
- Quantidade de pedidos por hora do dia
- Ajuda no dimensionamento de equipe

#### RF30 — Previsão de Demanda
**Descrição:** O sistema deve prever demanda semanal.
- Análise de últimas 4 semanas
- Média por dia da semana
- Previsão para próxima semana por prato
- **Regra de negócio:** Feature gated por plano (`reports`)

---

### Módulo J: Dashboard

#### RF31 — Dashboard do Restaurante
**Descrição:** O sistema deve exibir um dashboard com métricas do dia.
- Cache de 60 segundos
- Pedidos do dia, receita, ticket médio
- Distribuição de status
- Pratos mais vendidos do dia
- Últimos pedidos
- Gráfico de 14 dias (pedidos × receita)

#### RF32 — Dashboard Global (Root)
**Descrição:** O Root deve ver métricas agregadas de todos os restaurantes.
- Total de restaurantes, usuários, pedidos, receita
- Últimos restaurantes cadastrados
- Contagem de pedidos por status

---

### Módulo K: Impressão Térmica

#### RF33 — Impressão de Comandas
**Descrição:** O sistema deve imprimir comandas em impressoras térmicas.
- Suporte a 4 drivers: raw (log), network (socket), windows (PowerShell), linux (lp)
- Formatação 58mm e 80mm
- Comandos ESC/POS para impressão em rede
- `ThermalPrinterService` com `ThermalPrinterInterface`

---

### Módulo L: Planos e Assinatura

#### RF34 — Planos de Assinatura
**Descrição:** O sistema deve oferecer planos mensais com diferentes limites e features.
- **Essential (R$49):** 2 usuários, 300 pedidos/mês, features básicas
- **Pro (R$97):** 5 usuários, 1000 pedidos/mês, + relatórios e entregas
- **Enterprise (R$197):** 20 usuários, ilimitado, + API e suporte 24h

#### RF35 — Feature Gating
**Descrição:** O sistema deve liberar/restringir funcionalidades conforme o plano.
- Mapa de features (`Plan::$featureMap`) por slug
- Método `Plan::hasFeature(string $feature): bool`
- 403 se plano não incluir feature (em controllers)
- Sidebar filtra itens indisponíveis

#### RF36 — Limites por Plano
**Descrição:** O sistema deve aplicar limites definidos no plano.
- **Usuários:** `UserController` verifica `plan.max_users` ao criar/editar
- **Pedidos:** `OrderService::createOrder()` verifica `plan.max_orders_monthly`
- Limite mensal de pedidos é resetado a cada mês

#### RF37 — Cobrança (Billing)
**Descrição:** O Root deve gerenciar cobranças dos restaurantes.
- Geração de fatura com valor do plano
- Geração de PIX (QR Code + copia e cola)
- Confirmação de pagamento → estende `paid_until`
- Marcação como overdue/cancelado
- Upgrade/downgrade de plano
- **Regra de negócio:** Cobrança manual (sem recorrência automática)

#### RF38 — Middleware de Subscription
**Descrição:** O sistema deve bloquear acessos de assinaturas vencidas.
- `CheckSubscription` middleware:
  - Auto-expira trial se `trial_ends_at` passou
  - Redireciona expirados/cancelados para `/assinatura-expirada`
  - Aplicado a todas as rotas autenticadas (exceto root)

---

### Módulo M: Administração Root

#### RF39 — Gestão de Restaurantes (Root)
**Descrição:** O Root deve gerenciar todos os restaurantes.
- Lista com métricas (usuários, pedidos, receita)
- Criação de restaurante com usuário admin
- Ativação/desativação
- Detalhes: pedidos recentes, receita, pedidos mensais

#### RF40 — Painel de Cobranças (Root)
**Descrição:** O Root deve gerenciar cobranças de todos os restaurantes.
- Lista de restaurantes com plano, status, total gasto
- Geração de faturas PIX
- Confirmação/cancelamento de pagamento
- Upgrade/downgrade de planos
- Alteração manual de status de assinatura

---

## 4. Regras de Negócio (RN)

### RN01 — Multi-tenancy
Toda operação de leitura/escrita deve ser filtrada por `restaurant_id`. Usuários só acessam dados do seu restaurante. Root tem acesso irrestrito.

### RN02 — Roles
`root` pode tudo. `admin` gerencia o restaurante. `user` só vê dashboard e pedidos. Middleware `CheckRole` valida em cada grupo de rotas.

### RN03 — Limite de Usuários
`plan.max_users` define o número máximo de usuários ativos por restaurante. Root é notificado ao tentar criar/realocar usuário que exceda o limite.

### RN04 — Limite de Pedidos Mensais
`plan.max_orders_monthly` (0 = ilimitado) define o número máximo de pedidos por mês. O sistema bloqueia a criação ao atingir o limite.

### RN05 — Feature Gating
Features não inclusas no plano retornam 403. A verificação ocorre via método `authorizePlanFeature()` no controller.

### RN06 — Ciclo do Pedido
Transições válidas: `received → preparing → out_for_delivery → completed`. Qualquer → `canceled`. Transições inválidas são rejeitadas.

### RN07 — Geração de Número de Pedido
Formato `ORD-YYYYMMDD-XXXX` onde XXXX é sequencial diário por restaurante.

### RN08 — Subscription Expiry
Se `trial_ends_at` passou, status muda automaticamente para `expired`. Restaurantes com status `expired` ou `canceled` não acessam o sistema.

### RN09 — Pagamento de Fatura
Ao confirmar pagamento de invoice, `paid_until` é estendido em 1 mês e `subscription_status` volta para `active`.

### RN10 — Anonimização LGPD
Dados do cliente (nome, telefone, e-mail, endereço) são criptografados em repouso. A anonimização é irreversível e preserva métricas agregadas.

### RN11 — Cardápio Único por Data
Apenas um cardápio ativo por data. Se já existir, o sistema retorna o existente (get-or-create).

### RN12 — Preços por Tamanho
Pratos podem ter preços diferentes para pequeno, médio e grande. Pelo menos um tamanho deve ter preço definido.

### RN13 — Cache do Dashboard
Dados do dashboard são cacheados por 60 segundos para evitar consultas repetitivas.

### RN14 — Activity Log
Alterações em clientes e pedidos são registradas com retenção de 365 dias.

### RN15 — Isolamento WhatsApp
Cada restaurante tem seu próprio token/phone ID WhatsApp. Sessões de bot são isoladas por restaurante.

---

## 5. Casos de Uso (UC)

### UC01 — Realizar Pedido via WhatsApp
| Campo | Valor |
|---|---|
| **Ator principal** | Cliente |
| **Pré-condições** | Restaurante tem cardápio publicado do dia. Cliente está na base. |
| **Fluxo principal** | 1. Cliente envia mensagem para o número do restaurante |
| | 2. Bot envia cardápio com botões de tamanho |
| | 3. Cliente seleciona tamanho |
| | 4. Cliente seleciona proteína (se aplicável) |
| | 5. Cliente seleciona acompanhamentos |
| | 6. Cliente escolhe entrega ou retirada |
| | 7. Se delivery, informa endereço |
| | 8. Cliente escolhe pagamento (PIX/dinheiro) |
| | 9. Se dinheiro, informa troco |
| | 10. Bot confirma e registra pedido |
| | 11. Admin recebe notificação |
| **Pós-condições** | Pedido criado com status `received`. Sessão WhatsApp marcada como `completed`. |
| **Fluxo alternativo** | Cliente cancela → sessão reinicia. Timeout → sessão expira. |

### UC02 — Gerenciar Cardápio do Dia
| Campo | Valor |
|---|---|
| **Ator principal** | Admin |
| **Pré-condições** | Restaurante possui pratos cadastrados |
| **Fluxo principal** | 1. Admin acessa "Cardápio Diário" |
| | 2. Sistema exibe cardápio do dia (ou cria novo) |
| | 3. Admin seleciona pratos disponíveis |
| | 4. Admin ajusta preços e limites do dia |
| | 5. Admin publica cardápio |
| | 6. Admin clica "Enviar via WhatsApp" |
| **Pós-condições** | Cardápio publicado. Mensagens enviadas para clientes. |

### UC03 — Atualizar Status do Pedido (Kanban)
| Campo | Valor |
|---|---|
| **Ator principal** | Admin / User |
| **Pré-condições** | Pedido existe com status `received`. |
| **Fluxo principal** | 1. Usuário acessa Kanban |
| | 2. Usuário clica em "Preparando" no pedido |
| | 3. Sistema atualiza status para `preparing` |
| | 4. Sistema envia notificação WhatsApp ao cliente |
| | 5. Evento broadcast é disparado |
| **Pós-condições** | Pedido avança no fluxo. Cliente notificado. |

### UC04 — Gerar Fatura de Assinatura
| Campo | Valor |
|---|---|
| **Ator principal** | Root |
| **Pré-condições** | Restaurante possui plano ativo. |
| **Fluxo principal** | 1. Root acessa Faturamento → seleciona restaurante |
| | 2. Root clica "Gerar Fatura" |
| | 3. Sistema cria Invoice com valor do plano |
| | 4. Sistema gera PIX (QR Code + copia e cola) |
| | 5. Sistema exibe modal com dados PIX |
| | 6. Root pode imprimir/compartilhar |
| **Pós-condições** | Invoice criada com status `pending`. |

### UC05 — Confirmar Pagamento de Fatura
| Campo | Valor |
|---|---|
| **Ator principal** | Root |
| **Pré-condições** | Invoice existe com status `pending`. |
| **Fluxo principal** | 1. Root acessa fatura do restaurante |
| | 2. Root clica "Confirmar Pagamento" |
| | 3. Sistema marca invoice como `paid` |
| | 4. Sistema estende `paid_until` em +1 mês |
| | 5. Se estava expirado, subscription volta a `active` |
| **Pós-condições** | Invoice paga. Subscription renovada. |

### UC06 — Criar Novo Usuário
| Campo | Valor |
|---|---|
| **Ator principal** | Root |
| **Pré-condições** | Restaurante existe. |
| **Fluxo principal** | 1. Root acessa "Usuários" → "Novo Usuário" |
| | 2. Root preenche dados e seleciona restaurante |
| | 3. Sistema verifica `plan.max_users` do restaurante |
| | 4. Se limite não excedido → cria usuário |
| | 5. Se limite excedido → exibe erro |
| **Pós-condições** | Usuário criado (ou erro exibido). |

### UC07 — Acessar Relatórios
| Campo | Valor |
|---|---|
| **Ator principal** | Admin |
| **Pré-condições** | Plano do restaurante inclui feature `reports`. |
| **Fluxo principal** | 1. Admin acessa "Relatórios" |
| | 2. Sistema verifica `plan.hasFeature('reports')` |
| | 3. Se autorizado → exibe relatórios |
| | 4. Se negado → 403 |
| **Pós-condições** | Relatórios exibidos ou acesso bloqueado. |

### UC08 — Anonimizar Cliente
| Campo | Valor |
|---|---|
| **Ator principal** | Admin |
| **Pré-condições** | Cliente existe. |
| **Fluxo principal** | 1. Admin acessa perfil do cliente |
| | 2. Admin clica "Anonimizar" |
| | 3. Sistema confirma ação |
| | 4. Sistema remove dados pessoais |
| | 5. Preserva dados agregados |
| **Pós-condições** | Cliente anonimizado (irreversível). |

### UC09 — Imprimir Comanda
| Campo | Valor |
|---|---|
| **Ator principal** | Admin / User |
| **Pré-condições** | Pedido existe. Impressora configurada. |
| **Fluxo principal** | 1. Usuário acessa pedido |
| | 2. Usuário clica "Imprimir" |
| | 3. Sistema formata comanda (58mm ou 80mm) |
| | 4. Sistema envia para driver configurado |
| **Pós-condições** | Comanda impressa ou erro reportado. |

### UC10 — Configurar Restaurante
| Campo | Valor |
|---|---|
| **Ator principal** | Admin |
| **Pré-condições** | Usuário logado com role `admin`. |
| **Fluxo principal** | 1. Admin acessa "Configurações" |
| | 2. Sistema carrega grupos: geral, horários, notificações |
| | 3. Admin altera dados do restaurante |
| | 4. Admin salva (validação completa) |
| | 5. Sistema persiste dados |
| **Pós-condições** | Configurações atualizadas. |

---

## 6. Requisitos Não Funcionais (RNF)

| ID | Categoria | Descrição | Meta/Métrica |
|---|---|---|---|
| RNF01 | Desempenho | Tempo de resposta do webhook WhatsApp | < 2 segundos |
| RNF02 | Disponibilidade | Uptime do painel e webhook no horário de pico (09:00-15:00) | > 99,5% |
| RNF03 | Conformidade | Uso exclusivo da WhatsApp Cloud API oficial | 100% compliance Meta |
| RNF04 | Arquitetura | Isolamento multi-tenant | Row Level Security por tenant |
| RNF05 | Segurança | Criptografia de dados sensíveis em repouso | AES-256 (Laravel encrypted cast) |
| RNF06 | Qualidade | Cobertura de testes | > 120 testes, 285 assertions |
| RNF07 | Qualidade | Análise estática | PHPStan nível 6 |
| RNF08 | Qualidade | Estilo de código | PSR-12 (Pint) |
| RNF09 | Frontend | Compatibilidade de navegadores | Chrome, Firefox, Edge (2 últimas versões) |
| RNF10 | Privacidade | Conformidade LGPD | Anonimização, criptografia, páginas legais |
| RNF11 | Dados | Retenção de logs de atividade | 365 dias (configurável) |
| RNF12 | Implantação | Containerização | Docker multi-stage com healthcheck |

---

## 7. Glossário

| Termo | Definição |
|---|---|
| **Multi-tenancy** | Arquitetura onde múltiplos restaurantes (tenants) compartilham a mesma instância do sistema com isolamento de dados |
| **Feature Gating** | Mecanismo que libera/restringe funcionalidades com base no plano contratado |
| **Kanban** | Quadro visual de gestão de fluxo com colunas representando status |
| **WhatsApp Cloud API** | API oficial da Meta para integração de negócios com WhatsApp |
| **PIX** | Sistema de pagamento instantâneo brasileiro |
| **LGPD** | Lei Geral de Proteção de Dados Pessoais (Lei 13.709/2018) |
| **ESC/POS** | Sistema de comandos para impressoras térmicas fiscais |
| **Driver de impressão** | `raw`: log, `network`: socket TCP, `windows`: PowerShell, `linux`: lp |
| **Gateway de pagamento** | `mock`: simulação, `mercadopago`, `asaas`, `gerencianet` |
| **Sessão WhatsApp** | Máquina de estados que mantém o contexto do fluxo conversacional do cliente |
