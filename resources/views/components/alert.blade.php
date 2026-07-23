@props(['type' => 'info', 'message' => null, 'dismissible' => false])

@php
$colors = [
    'success' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300',
    'error' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300',
    'warning' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300',
    'info' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300',
];
$icons = [
    'success' => 'fa-regular fa-circle-check',
    'error' => 'fa-solid fa-circle-exclamation',
    'warning' => 'fa-solid fa-triangle-exclamation',
    'info' => 'fa-solid fa-circle-info',
];
@endphp

<div x-data="{ visible: true }" x-show="visible" x-transition.duration.300ms
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border p-4 ' . $colors[$type]]) }}>
    <i class="{{ $icons[$type] }} text-base flex-shrink-0 mt-0.5"></i>
    <div class="flex-1 text-sm font-medium">{{ $message ?? $slot }}</div>
    @if($dismissible)
        <button @click="visible = false" class="flex-shrink-0 hover:opacity-75">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    @endif
</div>
