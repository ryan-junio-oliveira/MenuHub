@props(['variant' => 'full', 'dark' => false, 'class' => ''])

@php
$classes = [
    'full' => 'h-[7.5rem] w-auto',
    'icon' => 'h-[7.5rem] w-[7.5rem]',
    'sm' => 'h-24 w-auto',
    'xs' => 'h-[4.5rem] w-auto',
];
$sizeClass = $classes[$variant] ?? $classes['full'];
$src = $dark ? 'assets/img/logo_full_dark_removebg.png' : 'assets/img/logo_full_removebg.png';
@endphp

<img src="{{ asset($src) }}"
     alt="MenuHub"
     {{ $attributes->merge(['class' => "$sizeClass $class"]) }}>
