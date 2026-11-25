<?php

namespace Tests\Feature;

use App\Models\Content\Article;
use App\Models\Content\Pillar;
use Database\Seeders\ContentHubSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_explore_hub_is_public(): void
    {
        $this->seed(ContentHubSeeder::class);

        $this->get(route('explore.index'))
            ->assertOk()
            ->assertSee('فهم الذات', false);
    }

    public function test_pillar_and_article_pages_render(): void
    {
        $this->seed(ContentHubSeeder::class);

        $pillar = Pillar::query()->where('slug', 'understanding-dreams')->firstOrFail();
        $article = Article::query()->where('slug', 'how-to-understand-your-dreams')->firstOrFail();

        $this->get(route('explore.pillar', $pillar))
            ->assertOk()
            ->assertSee($pillar->name, false);

        $this->get(route('explore.article', [$pillar, $article]))
            ->assertOk()
            ->assertSee($article->title, false);
    }

    public function test_feature_and_sitemap_are_available(): void
    {
        $this->get(route('explore.feature', 'dream-journal'))->assertOk();
        $this->get('/dream-journal')->assertRedirect();
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml');
    }

    public function test_trust_pages_are_public(): void
    {
        $this->get(route('editorial'))->assertOk();
        $this->get(route('ai-transparency'))->assertOk();
        $this->get(route('sources'))->assertOk();
    }

    public function test_related_articles_appear_on_article_page(): void
    {
        $this->seed(ContentHubSeeder::class);

        $pillar = Pillar::query()->where('slug', 'discover-yourself')->firstOrFail();
        $article = Article::query()->where('slug', 'what-does-it-mean-to-know-yourself')->firstOrFail();

        $this->get(route('explore.article', [$pillar, $article]))
            ->assertOk()
            ->assertSee(__('مقالات ذات صلة'), false);
    }

    public function test_unpublished_article_is_hidden(): void
    {
        $this->seed(ContentHubSeeder::class);
        $article = Article::query()->firstOrFail();
        $article->update(['editorial_status' => 'draft']);

        $this->get(route('explore.article', [$article->pillar, $article]))
            ->assertNotFound();
    }
}
