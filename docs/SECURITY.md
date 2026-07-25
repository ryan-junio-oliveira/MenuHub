# Segurança e LGPD

## LGPD (Lei Geral de Proteção de Dados)

### Dados Sensíveis

Campos protegidos com **criptografia** (Laravel `encrypted` cast):

| Model | Campos |
|---|---|
| Customer | `phone`, `email`, `address` |

### Anonimização

O método `Customer::anonymize()`:
- Substitui `name` por `[Removido]`
- Define `phone`, `email`, `address`, `notes` como `null`
- Preserva dados agregados: `total_orders`, `total_spent`

Disparado pelo admin via botão "Anonimizar" no perfil do cliente.

### Páginas Legais

- `/privacidade` → Política de privacidade
- `/termos` → Termos de uso

Ambas acessíveis sem autenticação.

## Práticas de Segurança

### Autenticação e Sessão
- Sessão armazenada em banco de dados (`SESSION_DRIVER=database`)
- CSRF automático (Laravel)
- Rate limiting em rotas de login
- Verificação de e-mail (opcional, configurável)

### Controle de Acesso
- **Roles:** `root` (administrador global), `admin` (gerente), `user` (funcionário)
- **Middleware `CheckRole`:** aborts 403 se role não autorizada
- **Middleware `CheckSubscription`:** bloqueia expirados/cancelados
- **Middleware `CheckRestaurantActive`:** bloqueia restaurantes inativos
- **Scopes:** `TenantScope` isola dados entre restaurantes

### Proteção de Rotas
- Arquivos sensíveis bloqueados no Nginx (`.env`, `*.sqlite`, `.git`, `storage/`)
- Headers de segurança configurados (HSTS, X-Frame-Options, CSP)
- Cache de assets com hash (Vite)

### Activity Log
- Auditoria de alterações em clientes (`spatie/laravel-activitylog`)
- Log de mudanças de status de pedidos
- Retenção: 365 dias (configurável em `config/activitylog.php`)

### Práticas de Código
- PHPStan nível 6 — análise estática obrigatória no CI
- Pint (PSR-12) — estilo de código padronizado
- ESLint — lint de JavaScript
- Validação de entrada via Form Requests (13 classes)
- Prepared statements (Eloquent ORM)

## Variáveis de Ambiente Sensíveis

```env
APP_KEY=<gerado pelo Laravel>
WHATSAPP_API_TOKEN=<token privado>
SERVICES_PIX_MERCADOPAGO_TOKEN=<token privado>
SERVICES_PIX_ASAAS_KEY=<chave privada>
```

Nunca commitar `.env` ou expor esses valores.

## Recomendações

- Manter `APP_DEBUG=false` em produção
- Usar HTTPS obrigatório
- Configurar backup diário do banco SQLite
- Monitorar activity logs para acessos suspeitos
- Renovar periodicamente chaves de API (WhatsApp, gateways)
