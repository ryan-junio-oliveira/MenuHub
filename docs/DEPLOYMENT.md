# Implantação

## Docker (Produção)

### Estrutura

```
Dockerfile              → Multi-stage (composer → npm → php-fpm + nginx)
docker-compose.yml       → Serviço app com healthcheck
docker/nginx.conf        → Security headers, asset cache 1y
docker/supervisord.conf  → php-fpm + nginx + queue (2 proc) + scheduler
docker/entrypoint.sh     → Migrate + seed automático na primeira execução
```

### Stack do Container

- **Base:** PHP 8.2 FPM Alpine + Nginx
- **Queue Worker:** Supervisor com 2 processos
- **Scheduler:** Supervisor rodando `php artisan schedule:run` a cada minuto
- **Banco:** SQLite (volume montado para persistência)

### Deploy

```bash
# Build e start
docker compose up -d --build

# Verificar health
docker compose ps

# Logs
docker compose logs -f app

# Executar comandos
docker compose exec app php artisan queue:work --stop-when-empty

# Atualizar (após git pull)
docker compose up -d --build
```

### Segurança Nginx

- Headers: HSTS, X-Frame-Options, X-Content-Type-Options, CSP
- Bloqueio de acesso a `.env`, `*.sqlite`, `.git`, `storage/`
- Cache de assets estáticos por 1 ano
- Redirecionamento www → non-www
- Compressão gzip

## CI/CD (GitHub Actions)

`.github/workflows/ci.yml` — 4 jobs paralelos:

### lint
- PHP 8.2
- Pint (code style) — `--test`
- PHPStan level 6
- Cobertura de `app/`, `config/`, `database/`, `routes/`, `tests/`

### phpunit
- Matrix: PHP 8.2, 8.3, 8.4
- SQLite in-memory
- 120 testes, 285 assertions
- Testes paralelos (Paratest)

### frontend
- Node 20
- ESLint (resources/js/)
- Vite build

### docker
- Docker Buildx com cache GHA
- Constrói imagem de produção
- Verifica se o build é bem-sucedido

## Ambiente de Produção

### Variáveis Essenciais

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### Recomendações

- **Banco:** SQLite é aceitável para pequenos volumes. Para escala, migrar para MySQL.
- **Fila:** Database queue (padrão). Para alta escala, usar Redis.
- **Cache:** Database cache. Redis recomendado para produção.
- **Broadcasting:** Pusher (configurado). Alternativa: Laravel Reverb.
- **Horizon:** Opcional para monitoramento de filas.
- **Backup:** Backup diário do arquivo SQLite.

### Manutenção

```bash
# Limpar activity logs antigos
php artisan activitylog:clean

# Otimizar Laravel
php artisan optimize

# Cache de config
php artisan config:cache

# Cache de rotas
php artisan route:cache
```

## Segurança

Ver [SECURITY.md](SECURITY.md) para práticas de segurança e LGPD.
