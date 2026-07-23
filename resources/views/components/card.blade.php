@props(['padding' => '5', 'class' => ''])

<div {{ $attributes->merge(['class' => "card p-{$padding} {$class}"]) }}>
    {{ $slot }}
</div>
