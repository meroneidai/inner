@props(['value'])

<label {{ $attributes->merge(['class' => 'ip-label']) }}>
    {{ $value ?? $slot }}
</label>
