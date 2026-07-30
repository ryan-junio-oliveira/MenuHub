@component('mail::message')
# 🎉 Convite para Gerenciar {{ $restaurant->razao_social ?: $restaurant->name }}

Olá **{{ $admin->name }}**,

Você foi convidado a gerenciar o restaurante **{{ $restaurant->razao_social ?: $restaurant->name }}** no **MenuHub** — a plataforma inteligente de gestão para restaurantes.

---

### 🔑 Seus dados de acesso

| | |
|---|---|
| **Restaurante** | {{ $restaurant->razao_social ?: $restaurant->name }} |
| **Seu e-mail** | {{ $admin->email }} |
| **Perfil** | Administrador |

Para começar, clique no botão abaixo e defina sua senha:

@component('mail::button', ['url' => $setupUrl, 'color' => 'primary'])
🔗 Completar Cadastro
@endcomponent

---

### ✅ O que você poderá fazer

- Gerenciar o cardápio digital (categorias e pratos)
- Receber e gerenciar pedidos em tempo real
- Controlar entregas e pagamentos
- Acompanhar relatórios de vendas

> ⏳ Este link de convite expira em **7 dias**.  
> Se você não esperava este convite, por favor ignore este e-mail.

---

Atenciosamente,  
**Equipe MenuHub**

---

{{ config('app.url') }}
@endcomponent
