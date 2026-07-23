@props(['label', 'name', 'type' => 'text', 'placeholder' => '', 'value' => '', 'error' => null, 'required' => false, 'disabled' => false])

<div>
    @if($label ?? false)
        <x-input-label for="{{ $name }}" :value="$label" />
    @endif
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'input-field mt-1']) }}
    />
    @if($error)
        <x-input-error :messages="$error" class="mt-2" />
    @endif
</div>
