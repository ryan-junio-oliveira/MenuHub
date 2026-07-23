@props(['variant' => 'primary', 'size' => 'md', 'disabled' => false])

@php
$base = 'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-900 active:scale-[0.97] disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100';
$sizes = ['sm' => 'px-3 py-2 text-xs', 'md' => 'px-4 py-2.5 text-sm', 'lg' => 'px-6 py-3 text-base'];
$variants = [
    'primary' => 'bg-primary-600 text-white shadow-sm hover:bg-primary-700 hover:shadow-md hover:shadow-primary-600/20 focus:ring-primary-500',
    'secondary' => 'border border-border dark:border-border-dark bg-card dark:bg-card-dark text-text-primary dark:text-text-dark shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 focus:ring-primary-500',
    'danger' => 'bg-red-500 text-white shadow-sm hover:bg-red-600 hover:shadow-md hover:shadow-red-500/20 focus:ring-red-400',
    'success' => 'bg-green-500 text-white shadow-sm hover:bg-green-600 hover:shadow-md hover:shadow-green-500/20 focus:ring-green-400',
    'ghost' => 'text-text-secondary hover:text-text-primary dark:text-slate-400 dark:hover:text-text-dark hover:bg-slate-100 dark:hover:bg-slate-700 focus:ring-primary-500',
];
$class = trim("$base {$sizes[$size]} {$variants[$variant]}");
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $class, 'disabled' => $disabled]) }}>
    {{ $slot }}
</button>
