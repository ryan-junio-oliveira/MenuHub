@php
    $title = __('Serviço Indisponível');
    $code = '503';
    $message = __('O sistema está em manutenção. Volte em breve.');
    $icon = 'fa-tools';
@endphp

<x-error-layout :title="$title" :code="$code" :message="$message" :icon="$icon" />
