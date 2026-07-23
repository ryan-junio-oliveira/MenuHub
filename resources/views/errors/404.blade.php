@php
    $title = __('Página Não Encontrada');
    $code = '404';
    $message = __('A página que você procura não existe ou foi removida.');
    $icon = 'fa-search';
@endphp

<x-error-layout :title="$title" :code="$code" :message="$message" :icon="$icon" />
