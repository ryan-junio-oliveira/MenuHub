@php
    $title = __('Acesso Negado');
    $code = '403';
    $message = __('Você não tem permissão para acessar esta página.');
    $icon = 'fa-ban';
@endphp

<x-error-layout :title="$title" :code="$code" :message="$message" :icon="$icon" />
