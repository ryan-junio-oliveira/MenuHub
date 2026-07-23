@php
    $title = __('Não Autorizado');
    $code = '401';
    $message = __('Você precisa estar autenticado para acessar esta página.');
    $icon = 'fa-lock';
@endphp

<x-error-layout :title="$title" :code="$code" :message="$message" :icon="$icon" />
