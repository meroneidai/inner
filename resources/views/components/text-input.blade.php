@props([
    'disabled' => false,
    'label' => null,
])

@if ($label)
    <x-floating-input
        :label="$label"
        :disabled="$disabled"
        {{ $attributes }}
    />
@else
    <input @disabled($disabled) {{ $attributes->merge(['class' => 'ip-input']) }}>
@endif
