<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content\Article;
use App\Models\Content\Pillar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContentArticleController extends Controller
{
    public function index(): View
    {
        return view('admin.content.articles.index', [
            'articles' => Article::query()->with('pillar')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.content.articles.edit', [
            'article' => new Article([
                'editorial_status' => 'draft',
                'locale' => 'ar',
            ]),
            'pillars' => Pillar::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function edit(Article $article): View
    {
        return view('admin.content.articles.edit', [
            'article' => $article,
            'pillars' => Pillar::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $article = $this->persist($request, new Article);

        return redirect()
            ->route('admin.content.articles.edit', $article)
            ->with('status', __('تم إنشاء المقال.'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $this->persist($request, $article);

        return back()->with('status', __('تم حفظ المقال.'));
    }

    private function persist(Request $request, Article $article): Article
    {
        $data = $request->validate([
            'pillar_id' => ['required', 'exists:content_pillars,id'],
            'title' => ['required', 'string', 'max:220'],
            'slug' => ['nullable', 'string', 'max:220'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'seo_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'editorial_status' => ['required', 'string', 'max:40'],
            'featured_image_path' => ['nullable', 'string', 'max:255'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_route' => ['nullable', 'string', 'max:120'],
            'locale' => ['required', 'in:ar,en'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        if ($data['slug'] === '') {
            $data['slug'] = Str::slug(Str::limit($data['title'], 40, '')).'-'.Str::random(4);
        }

        if ($data['editorial_status'] === 'published' && ! $article->published_at) {
            $data['published_at'] = now();
        }

        $data['updated_content_at'] = now();
        $article->fill($data);
        $article->reading_time_minutes = $article->estimateReadingTime();
        $article->save();

        return $article;
    }
}
