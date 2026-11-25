<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ip-btn']) }}>
    {{ $slot }}
</button>
