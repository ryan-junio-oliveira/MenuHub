# MenuHub (MarmitaBot SaaS)

Sistema SaaS multi-tenant para gestão de restaurantes, cardápios digitais e pedidos via WhatsApp.

**Stack:** Laravel 12 + SQLite + Blade + Alpine.js + Tailwind CSS

## Funcionalidades

- Cardápio digital com envio automático via WhatsApp
- Bot de WhatsApp para pedidos automatizados (fluxo conversacional)
- Gestão de pedidos com Kanban (tempo real)
- Pagamentos PIX (Mercado Pago / Asaas / Gerencianet)
- Gestão de entregas
- Relatórios financeiros, vendas por prato, combinações, horários de pico
- Previsão de demanda semanal
- Tags de clientes (segmentação)
- Impressão térmica (58mm / 80mm)
- Planos de assinatura (Essential, Pro, Enterprise)
- LGPD compliance (anonimização de dados, criptografia, páginas de privacidade/termos)
- Painel administrativo Root (gerenciar restaurantes, usuários, cobranças)

## Documentação

| Documento | Descrição |
|---|---|
| [Visão Geral da Arquitetura](docs/ARCHITECTURE.md) | Estrutura do sistema, camadas, padrões |
| [Guia de Instalação](docs/INSTALLATION.md) | Setup local, Docker, variáveis de ambiente |
| [Modelo de Dados](docs/DATABASE.md) | Esquema do banco, relacionamentos |
| [Módulos do Sistema](docs/MODULES.md) | Funcionalidades detalhadas por módulo |
| [Sistema de Planos](docs/PLANS.md) | Assinatura, limites, features, cobrança |
| [WhatsApp](docs/WHATSAPP.md) | Integração WhatsApp Cloud API, bot conversacional |
| [Pagamentos](docs/PAYMENTS.md) | Gateway de pagamentos PIX |
| [Implantação](docs/DEPLOYMENT.md) | Deploy em produção, CI/CD, Docker |
| [Segurança e LGPD](docs/SECURITY.md) | Privacidade, criptografia, anonimização |

## Início Rápido

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

Acesse `http://localhost:8000` — login padrão: `root@menuhub.com` / `password`

## Docker (Produção)

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

## Testes

```bash
php artisan test                  # Todos os testes
php artisan test --parallel       # Paralelo (recomendado)
php artisan test --coverage       # Com cobertura (xdebug)
```

## CI/CD

GitHub Actions: lint (Pint + PHPStan) → phpunit (120 testes) → frontend (ESLint + build) → docker (Buildx)

## Estrutura

```
app/
├── Http/Controllers/     Controladores
├── Models/                Modelos Eloquent (16)
├── Services/              Lógica de negócio (12 serviços)
├── Scopes/                Global scopes (multi-tenancy)
└── Http/Middleware/       Middleware (tenant, role, subscription)
database/
├── migrations/            Migrations (25)
└── seeders/               Seeds (PlanSeeder, DatabaseSeeder)
docker/                    Nginx + Supervisor + Entrypoint
resources/
├── views/                 Blade views (65+)
├── css/                   Tailwind CSS
└── js/                    Alpine.js + Three.js + GSAP
routes/                    web.php, auth.php, whatsapp.php
```

## Licença

MIT
