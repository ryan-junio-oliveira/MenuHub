@php
    $title = __('Erro Interno do Servidor');
    $code = '500';
    $message = __('Ocorreu um erro inesperado. Nossa equipe foi notificada e estamos trabalhando na correção.');
    $icon = 'fa-bolt';
@endphp

<x-error-layout :title="$title" :code="$code" :message="$message" :icon="$icon" />
