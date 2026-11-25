@props(['question', 'value' => null])

@php
    $name = 'answers['.$question->id.']';
    $type = $question->answer_type;
    $options = $question->options();
    $current = old('answers.'.$question->id, $value);
    $locale = app()->getLocale();

    $optionMeta = function ($option) use ($locale): array {
        if (! is_array($option)) {
            return ['value' => (string) $option, 'label' => (string) $option, 'emoji' => ''];
        }
        $value = (string) ($option['value'] ?? $option['key'] ?? '');
        $label = (string) ($option['label_'.$locale] ?? $option['label_ar'] ?? $option['label_en'] ?? $option['label'] ?? $value);

        return [
            'value' => $value,
            'label' => $label,
            'emoji' => (string) ($option['emoji'] ?? ''),
        ];
    };
@endphp

<div class="space-y-3 rounded-3xl bg-white/80 p-5 ring-1 ring-beige/80 shadow-sm">
    <label class="block font-medium text-charcoal">
        {{ $question->text() }}
        @if($question->is_sensitive)
            <span class="ip-chip ms-2">🔒 {{ __('حسّاس / اختياري') }}</span>
        @endif
        @if(! $question->is_required)
            <span class="text-xs font-normal text-charcoal/50">({{ __('اختياري') }})</span>
        @endif
    </label>
    @if($question->description())
        <p class="ip-muted text-pretty">{{ $question->description() }}</p>
    @endif

    @if($type === 'short_text')
        <x-floating-input name="{{ $name }}" :label="__('اكتب باختصار…')" :value="is_array($current) ? '' : $current" />

    @elseif(in_array($type, ['age_range', 'country'], true))
        <div class="flex flex-wrap gap-2">
            @foreach($options as $option)
                @php $meta = $optionMeta($option); @endphp
                <label class="ip-choice {{ (string) $current === $meta['value'] ? 'ip-choice-active' : '' }}">
                    <input type="radio" name="{{ $name }}" value="{{ $meta['value'] }}" class="sr-only" @checked((string) $current === $meta['value'])>
                    @if($meta['emoji'])<span class="text-lg" aria-hidden="true">{{ $meta['emoji'] }}</span>@endif
                    <span>{{ $meta['label'] }}</span>
                </label>
            @endforeach
        </div>

    @elseif($type === 'long_text')
        <p class="text-xs text-charcoal/50">{{ __('إن أمكن اختر من الوسوم أدناه؛ الكتابة اختيارية للتفاصيل.') }}</p>
        @if(count($options))
            <div class="mb-2 flex flex-wrap gap-2" x-data="{ picked: @js(is_array($current) ? $current : []) }">
                @foreach($options as $option)
                    @php $meta = $optionMeta($option); @endphp
                    <button type="button"
                            class="ip-choice"
                            :class="picked.includes(@js($meta['value'])) ? 'ip-choice-active' : ''"
                            @click="
                                if (picked.includes(@js($meta['value']))) picked = picked.filter(v => v !== @js($meta['value']));
                                else picked.push(@js($meta['value']));
                                $refs.extra.value = picked.join(' · ');
                            ">
                        @if($meta['emoji'])<span aria-hidden="true">{{ $meta['emoji'] }}</span>@endif
                        <span>{{ $meta['label'] }}</span>
                    </button>
                @endforeach
                <input type="hidden" name="{{ $name }}" x-ref="extra" value="{{ is_array($current) ? implode(' · ', $current) : $current }}">
            </div>
        @else
            <x-floating-input type="textarea" name="{{ $name }}" :label="__('اختياري…')" :rows="3" :value="is_array($current) ? '' : $current" />

        @endif

    @elseif($type === 'scale')
        <div class="flex flex-wrap gap-2">
            @foreach($options as $option)
                @php $meta = $optionMeta($option); @endphp
                <label class="ip-choice flex-col min-w-[4.5rem] {{ (string) $current === $meta['value'] ? 'ip-choice-active' : '' }}">
                    <input type="radio" name="{{ $name }}" value="{{ $meta['value'] }}" class="sr-only" @checked((string) $current === $meta['value'])>
                    <span class="text-2xl" aria-hidden="true">{{ $meta['emoji'] ?: $meta['value'] }}</span>
                    <span class="text-xs">{{ $meta['label'] }}</span>
                </label>
            @endforeach
        </div>

    @elseif($type === 'multiple_choice')
        <div class="flex flex-wrap gap-2">
            @foreach($options as $option)
                @php
                    $meta = $optionMeta($option);
                    $checked = is_array($current) && in_array($meta['value'], $current, true);
                @endphp
                <label class="ip-choice {{ $checked ? 'ip-choice-active' : '' }}">
                    <input type="checkbox" name="{{ $name }}[]" value="{{ $meta['value'] }}" class="sr-only" @checked($checked)
                           onchange="this.closest('label').classList.toggle('ip-choice-active', this.checked)">
                    @if($meta['emoji'])<span class="text-lg" aria-hidden="true">{{ $meta['emoji'] }}</span>@endif
                    <span>{{ $meta['label'] }}</span>
                </label>
            @endforeach
        </div>

    @elseif(in_array($type, ['single_choice', 'yes_no'], true))
        <div class="flex flex-wrap gap-2">
            @foreach($options as $option)
                @php $meta = $optionMeta($option); @endphp
                <label class="ip-choice {{ (string) $current === $meta['value'] ? 'ip-choice-active' : '' }}">
                    <input type="radio" name="{{ $name }}" value="{{ $meta['value'] }}" class="sr-only" @checked((string) $current === $meta['value'])
                           onchange="this.closest('.flex').querySelectorAll('label').forEach(l=>l.classList.remove('ip-choice-active')); this.closest('label').classList.add('ip-choice-active')">
                    @if($meta['emoji'])<span class="text-lg" aria-hidden="true">{{ $meta['emoji'] }}</span>@endif
                    <span>{{ $meta['label'] }}</span>
                </label>
            @endforeach
            <label class="ip-choice {{ $current === null || $current === '' ? 'ip-choice-active' : '' }}">
                <input type="radio" name="{{ $name }}" value="" class="sr-only" @checked($current === null || $current === '')
                       onchange="this.closest('.flex').querySelectorAll('label').forEach(l=>l.classList.remove('ip-choice-active')); this.closest('label').classList.add('ip-choice-active')">
                <span>{{ __('تخطي') }}</span>
            </label>
        </div>
    @else
        <div class="flex flex-wrap gap-2">
            @forelse($options as $option)
                @php $meta = $optionMeta($option); @endphp
                <label class="ip-choice {{ (string) $current === $meta['value'] ? 'ip-choice-active' : '' }}">
                    <input type="radio" name="{{ $name }}" value="{{ $meta['value'] }}" class="sr-only" @checked((string) $current === $meta['value'])>
                    @if($meta['emoji'])<span>{{ $meta['emoji'] }}</span>@endif
                    <span>{{ $meta['label'] }}</span>
                </label>
            @empty
                <x-floating-input name="{{ $name }}" :label="__('إجابتك')" :value="is_array($current) ? '' : $current" />
            @endforelse
        </div>
    @endif
</div>
