@php
    $title = __('Muitas Requisições');
    $code = '429';
    $message = __('Você fez muitas requisições. Aguarde um momento e tente novamente.');
    $icon = 'fa-clock';
@endphp

<x-error-layout :title="$title" :code="$code" :message="$message" :icon="$icon" />
