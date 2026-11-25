<?php

namespace App\Http\Controllers;

use App\Models\Content\Article;
use App\Models\Content\Pillar;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('explore.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('how-it-works'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('privacy'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('terms'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('editorial'), 'changefreq' => 'yearly', 'priority' => '0.4'],
            ['loc' => route('ai-transparency'), 'changefreq' => 'yearly', 'priority' => '0.4'],
            ['loc' => route('sources'), 'changefreq' => 'yearly', 'priority' => '0.4'],
        ]);

        foreach ([
            'dream-journal',
            'dream-analysis',
            'inner-profile',
            'patterns-and-insights',
            'personal-growth',
            'personal-reports',
            'mobile-app',
        ] as $feature) {
            $urls->push([
                'loc' => route('explore.feature', $feature),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ]);
        }

        Pillar::query()->published()->where('indexable', true)->each(function (Pillar $pillar) use ($urls): void {
            $urls->push([
                'loc' => $pillar->url(),
                'lastmod' => optional($pillar->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);
        });

        Article::query()->published()->where('indexable', true)->with('pillar')->each(function (Article $article) use ($urls): void {
            $urls->push([
                'loc' => $article->url(),
                'lastmod' => optional($article->updated_content_at ?? $article->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ]);
        });

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
