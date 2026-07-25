@props(['variant' => 'full', 'dark' => false, 'class' => ''])

@php
$sizes = [
    'full' => 'h-16',
    'icon' => 'h-12 w-12',
    'sm' => 'h-10',
    'xs' => 'h-8',
];
$sizeClass = $sizes[$variant] ?? $sizes['full'];
$src = $dark ? 'assets/img/logo_full_dark_removebg.png' : 'assets/img/logo_full_removebg.png';
@endphp

<img src="{{ asset($src) }}"
     alt="MenuHub"
     {{ $attributes->merge(['class' => "$sizeClass w-auto $class"]) }}>
