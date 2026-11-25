<x-app-layout>
    <x-slot name="header"><h1 class="ip-section-title">{{ $pillar->name }}</h1></x-slot>
    <div class="ip-container max-w-3xl">
        <form method="POST" action="{{ route('admin.content.pillars.update', $pillar) }}" class="ip-card space-y-4 p-6">
            @csrf @method('PUT')
            <div><label class="ip-label">{{ __('الاسم') }}</label><input name="name" class="ip-input" value="{{ old('name', $pillar->name) }}" required></div>
            <div><label class="ip-label">{{ __('الوصف القصير') }}</label><input name="tagline" class="ip-input" value="{{ old('tagline', $pillar->tagline) }}"></div>
            <div><label class="ip-label">{{ __('مقتطف') }}</label><textarea name="excerpt" class="ip-input" rows="2">{{ old('excerpt', $pillar->excerpt) }}</textarea></div>
            <div><label class="ip-label">{{ __('المقدمة') }}</label><textarea name="intro" class="ip-input" rows="6">{{ old('intro', $pillar->intro) }}</textarea></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="ip-label">SEO Title</label><input name="seo_title" class="ip-input" value="{{ old('seo_title', $pillar->seo_title) }}"></div>
                <div><label class="ip-label">{{ __('الترتيب') }}</label><input type="number" name="sort_order" class="ip-input" value="{{ old('sort_order', $pillar->sort_order) }}"></div>
            </div>
            <div><label class="ip-label">Meta description</label><textarea name="meta_description" class="ip-input" rows="2">{{ old('meta_description', $pillar->meta_description) }}</textarea></div>
            <div><label class="ip-label">{{ __('مسار الصورة') }}</label><input name="featured_image_path" class="ip-input" value="{{ old('featured_image_path', $pillar->featured_image_path) }}"></div>
            <div>
                <label class="ip-label">{{ __('الحالة') }}</label>
                <select name="status" class="ip-input">
                    <option value="draft" @selected(old('status', $pillar->status) === 'draft')>draft</option>
                    <option value="published" @selected(old('status', $pillar->status) === 'published')>published</option>
                </select>
            </div>
            <button class="ip-btn">{{ __('حفظ') }}</button>
        </form>
    </div>
</x-app-layout>
