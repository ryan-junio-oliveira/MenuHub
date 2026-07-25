# Guia de Instalação

## Requisitos

- PHP 8.2+
- Composer 2.x
- Node.js 20+ e npm
- SQLite (ou MySQL 8+)
- Extensões PHP: `sqlite3`, `mbstring`, `gd`, `xml`, `curl`, `fileinfo`

## Setup Local

```bash
# Clone
git clone <url> menuhub
cd menuhub

# Dependências PHP
composer install

# Dependências frontend
npm install

# Ambiente
cp .env.example .env
php artisan key:generate

# Banco SQLite
touch database/database.sqlite

# Migrations e seeds
php artisan migrate
php artisan db:seed

# Compilar assets
npm run build

# Iniciar
php artisan serve              # Terminal 1
npm run dev                    # Terminal 2 (Vite HMR)
```

**Acesso:** `http://localhost:8000`
- Root: `root@menuhub.com` / `password`
- Admin: `admin@marmitadoze.com` / `password`
- User: `joao@marmitadoze.com` / `password`

## Docker (Produção)

```bash
# Build e start
docker compose up -d --build

# Migrations (primeira execução)
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force

# Logs
docker compose logs -f

# Parar
docker compose down
```

O entrypoint (`docker/entrypoint.sh`) executa migrate + seed automaticamente na primeira execução.

## Variáveis de Ambiente

### Aplicação

| Variável | Descrição | Obrigatório |
|---|---|---|
| `APP_KEY` | Chave de criptografia | ✅ |
| `APP_URL` | URL pública | ✅ |
| `APP_ENV` | `local`, `production` | ✅ |
| `APP_DEBUG` | `true`/`false` | ✅ |
| `DB_CONNECTION` | `sqlite` (padrão) ou `mysql` | ✅ |

### WhatsApp

| Variável | Descrição |
|---|---|
| `WHATSAPP_API_TOKEN` | Token de acesso WhatsApp Cloud API |
| `WHATSAPP_PHONE_ID` | ID do número de telefone |
| `WHATSAPP_BUSINESS_ACCOUNT_ID` | ID da conta business |
| `WHATSAPP_VERIFY_TOKEN` | Token de verificação do webhook |

### Pagamentos

| Variável | Descrição |
|---|---|
| `SERVICES_PIX_GATEWAY` | `mock`, `mercadopago`, `asaas`, `gerencianet` |
| `SERVICES_PIX_MERCADOPAGO_TOKEN` | Access Token Mercado Pago |
| `SERVICES_PIX_ASAAS_KEY` | API Key Asaas |

### Impressão

| Variável | Descrição |
|---|---|
| `PRINTING_DRIVER` | `raw`, `network`, `windows`, `linux` |
| `PRINTING_PRINTER_NAME` | Nome da impressora |
| `PRINTING_HOST` | IP da impressora (modo network) |
| `PRINTING_PORT` | Porta (padrão 9100) |

## Testes

```bash
# Todos os testes
php artisan test

# Paralelo (recomendado)
php artisan test --parallel

# Com cobertura (requer xdebug)
php artisan test --coverage

# Arquivo específico
php artisan test tests/Feature/Controllers/OrderControllerTest.php
```

## Comandos Úteis

```bash
php artisan queue:work            # Processar fila
php artisan schedule:run          # Agendador (tasks)
php artisan activitylog:clean     # Limpar logs antigos (365 dias)
```
