<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('تعديل سؤال') }} {{ $question->question_key }}</h2></x-slot>
    <div class="ip-container max-w-3xl">
        <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="ip-card space-y-4 p-6">
            @csrf @method('PUT')
            <div>
                <label class="ip-label">{{ __('القسم') }}</label>
                <select name="section_id" class="ip-input">
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" @selected($section->id === $question->section_id)>{{ $section->localizedName() }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="ip-label">answer_type</label><input name="answer_type" class="ip-input" value="{{ $question->answer_type }}"></div>
            <div><label class="ip-label">display_order</label><input type="number" name="display_order" class="ip-input" value="{{ $question->display_order }}"></div>
            <div><label class="ip-label">ai_context_relevance</label><input name="ai_context_relevance" class="ip-input" value="{{ $question->ai_context_relevance }}"></div>
            <div><label class="ip-label">AR</label><textarea name="question_text_ar" class="ip-input" rows="2">{{ $question->translation('ar')?->question_text }}</textarea></div>
            <div><label class="ip-label">EN</label><textarea name="question_text_en" class="ip-input" rows="2">{{ $question->translation('en')?->question_text }}</textarea></div>
            <div><label class="ip-label">AR desc</label><textarea name="description_ar" class="ip-input" rows="2">{{ $question->translation('ar')?->description }}</textarea></div>
            <div><label class="ip-label">EN desc</label><textarea name="description_en" class="ip-input" rows="2">{{ $question->translation('en')?->description }}</textarea></div>
            <label class="text-sm"><input type="checkbox" name="is_active" value="1" @checked($question->is_active)> active</label>
            <label class="text-sm"><input type="checkbox" name="is_sensitive" value="1" @checked($question->is_sensitive)> sensitive</label>
            <label class="text-sm"><input type="checkbox" name="include_in_stage1" value="1" @checked($question->include_in_stage1)> stage1</label>
            <label class="text-sm"><input type="checkbox" name="is_required" value="1" @checked($question->is_required)> required</label>
            <button class="ip-btn">{{ __('حفظ') }}</button>
        </form>
    </div>
</x-app-layout>
