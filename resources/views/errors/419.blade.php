@php
    $title = __('Sessão Expirada');
    $code = '419';
    $message = __('Sua sessão expirou. Faça login novamente para continuar.');
    $icon = 'fa-hourglass-end';
@endphp

<x-error-layout :title="$title" :code="$code" :message="$message" :icon="$icon" />
