@component('mail::message')

Olá, **{{ $admin->name }}**!

Você foi convidado a gerenciar o restaurante **{{ $restaurant->razao_social ?: $restaurant->name }}** no MenuHub.

Clique no botão abaixo para definir sua senha e completar o cadastro:

@component('mail::button', ['url' => $setupUrl, 'color' => 'primary'])
Completar Cadastro
@endcomponent

Este link expira em **7 dias**. Se você não esperava este convite, ignore este email.

---

Atenciosamente,  
Equipe **MenuHub**

@endcomponent