@props([
    'label',
    'type' => 'text',
    'disabled' => false,
    'rows' => null,
    'value' => null,
])

@php
    $id = $attributes->get('id') ?: 'field_'.str_replace('.', '', uniqid('', true));
    $isTextarea = $rows !== null || $type === 'textarea';
    $alwaysFloat = in_array($type, ['date', 'time', 'datetime-local', 'month', 'week', 'number'], true);
    $controlClasses = 'ip-input ip-float-control';
    $placeholder = $attributes->get('placeholder');
    // Space placeholder enables :placeholder-shown for empty fields without visible hint text.
    $mergedPlaceholder = ($placeholder !== null && $placeholder !== '') ? $placeholder : ' ';
@endphp

<div @class([
    'ip-float',
    'ip-float--textarea' => $isTextarea,
    'ip-float--always' => $alwaysFloat,
])>
    @if ($isTextarea)
        <textarea
            id="{{ $id }}"
            @disabled($disabled)
            rows="{{ $rows ?? 4 }}"
            {{ $attributes
                ->except(['type'])
                ->merge([
                    'class' => $controlClasses,
                    'placeholder' => $mergedPlaceholder,
                    'id' => $id,
                ]) }}
        >{{ $value ?? $slot }}</textarea>
    @else
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            @disabled($disabled)
            @if ($value !== null) value="{{ $value }}" @endif
            {{ $attributes->merge([
                'class' => $controlClasses,
                'placeholder' => $mergedPlaceholder,
                'id' => $id,
            ]) }}
        >
    @endif

    <label class="ip-float-label" for="{{ $id }}">{{ $label }}</label>
</div>
