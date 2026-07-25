# MenuHub — Documentação

Índice completo da documentação do sistema.

## Sumário

| Documento | Descrição |
|---|---|
| [Visão Geral da Arquitetura](ARCHITECTURE.md) | Estrutura do sistema, camadas, padrões de arquitetura |
| [Guia de Instalação](INSTALLATION.md) | Setup local, Docker, variáveis de ambiente |
| [Modelo de Dados](DATABASE.md) | Esquema do banco, relacionamentos entre entidades |
| [Módulos do Sistema](MODULES.md) | Funcionalidades detalhadas por módulo |
| [Sistema de Planos](PLANS.md) | Assinatura, limites, controle de features, fluxo de cobrança |
| [Integração WhatsApp](WHATSAPP.md) | WhatsApp Cloud API, bot conversacional, webhook |
| [Pagamentos](PAYMENTS.md) | Gateway de pagamentos PIX, integração Mercado Pago/Asaas |
| [Implantação](DEPLOYMENT.md) | Deploy em produção, CI/CD, Docker |

## Convenções

- **Linguagem:** PHP 8.2+, Blade templates, Alpine.js
- **Banco de dados:** SQLite (padrão), suporte a MySQL
- **Estilo de código:** PSR-12 (Pint + PHPStan level 6)
- **Frontend:** Tailwind CSS 3 + Vite
- **Testes:** PHPUnit + Paratest (120 testes, 285 assertions)
- **Idioma:** Português brasileiro (pt_BR)
