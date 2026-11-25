<x-app-layout>
    <x-slot name="header">
        <h1 class="ip-section-title">{{ $article->exists ? __('تعديل مقال') : __('مقال جديد') }}</h1>
    </x-slot>
    <div class="ip-container max-w-3xl">
        <form method="POST" action="{{ $article->exists ? route('admin.content.articles.update', $article) : route('admin.content.articles.store') }}" class="ip-card space-y-4 p-6">
            @csrf
            @if($article->exists) @method('PUT') @endif
            <div>
                <label class="ip-label">{{ __('الركيزة') }}</label>
                <select name="pillar_id" class="ip-input" required>
                    @foreach($pillars as $pillar)
                        <option value="{{ $pillar->id }}" @selected((string) old('pillar_id', $article->pillar_id) === (string) $pillar->id)>{{ $pillar->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="ip-label">{{ __('العنوان') }}</label><input name="title" class="ip-input" value="{{ old('title', $article->title) }}" required></div>
            <div><label class="ip-label">Slug</label><input name="slug" class="ip-input" value="{{ old('slug', $article->slug) }}"></div>
            <div><label class="ip-label">{{ __('مقتطف') }}</label><textarea name="excerpt" class="ip-input" rows="2">{{ old('excerpt', $article->excerpt) }}</textarea></div>
            <div><label class="ip-label">{{ __('المحتوى') }}</label><textarea name="body" class="ip-input font-mono text-sm" rows="16" required>{{ old('body', $article->body) }}</textarea></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="ip-label">SEO Title</label><input name="seo_title" class="ip-input" value="{{ old('seo_title', $article->seo_title) }}"></div>
                <div>
                    <label class="ip-label">{{ __('اللغة') }}</label>
                    <select name="locale" class="ip-input">
                        <option value="ar" @selected(old('locale', $article->locale) === 'ar')>ar</option>
                        <option value="en" @selected(old('locale', $article->locale) === 'en')>en</option>
                    </select>
                </div>
            </div>
            <div><label class="ip-label">Meta description</label><textarea name="meta_description" class="ip-input" rows="2">{{ old('meta_description', $article->meta_description) }}</textarea></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="ip-label">{{ __('صورة') }}</label><input name="featured_image_path" class="ip-input" value="{{ old('featured_image_path', $article->featured_image_path) }}"></div>
                <div><label class="ip-label">Alt</label><input name="featured_image_alt" class="ip-input" value="{{ old('featured_image_alt', $article->featured_image_alt) }}"></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="ip-label">CTA label</label><input name="cta_label" class="ip-input" value="{{ old('cta_label', $article->cta_label) }}"></div>
                <div><label class="ip-label">CTA route</label><input name="cta_route" class="ip-input" value="{{ old('cta_route', $article->cta_route) }}" placeholder="register"></div>
            </div>
            <div>
                <label class="ip-label">{{ __('الحالة التحريرية') }}</label>
                <select name="editorial_status" class="ip-input">
                    @foreach(['draft','editorial_review','approved','published','needs_update'] as $status)
                        <option value="{{ $status }}" @selected(old('editorial_status', $article->editorial_status) === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <button class="ip-btn">{{ __('حفظ') }}</button>
        </form>
    </div>
</x-app-layout>
