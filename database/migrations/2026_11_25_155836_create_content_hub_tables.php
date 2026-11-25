<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('role_title')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();
        });

        Schema::create('content_pillars', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('locale', 5)->default('ar')->index();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('tagline')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('intro')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('og_image_path')->nullable();
            $table->string('featured_image_path')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_route')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('draft')->index(); // draft|published
            $table->boolean('indexable')->default(true);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
        });

        Schema::create('content_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_id')->constrained('content_pillars')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('content_authors')->nullOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('content_authors')->nullOnDelete();
            $table->string('slug');
            $table->string('locale', 5)->default('ar')->index();
            $table->string('title');
            $table->string('excerpt', 500)->nullable();
            $table->longText('body');
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('primary_keyword')->nullable();
            $table->json('related_keywords')->nullable();
            $table->string('search_intent')->nullable(); // informational|navigational|commercial|transactional
            $table->string('featured_image_path')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->string('og_image_path')->nullable();
            $table->string('cta_type')->nullable(); // journey|journal|profile|reflection|how|app
            $table->string('cta_label')->nullable();
            $table->string('cta_route')->nullable();
            $table->unsignedSmallInteger('reading_time_minutes')->nullable();
            $table->string('editorial_status')->default('idea')->index();
            // idea|research|outline|draft|editorial_review|fact_check|seo_review|image_review|approved|published|needs_update
            $table->boolean('indexable')->default(true);
            $table->boolean('is_ai_assisted')->default(false);
            $table->boolean('requires_safety_notice')->default(false);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('updated_content_at')->nullable();
            $table->timestamps();

            $table->unique(['pillar_id', 'slug', 'locale']);
            $table->index(['locale', 'editorial_status', 'published_at']);
        });

        Schema::create('content_article_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('content_articles')->cascadeOnDelete();
            $table->foreignId('related_article_id')->constrained('content_articles')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['article_id', 'related_article_id']);
        });

        Schema::create('content_faqs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('faqable');
            $table->string('locale', 5)->default('ar');
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('content_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('content_articles')->cascadeOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('source_type')->nullable(); // research|book|org|primary_text|other
            $table->text('citation')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_references');
        Schema::dropIfExists('content_faqs');
        Schema::dropIfExists('content_article_relations');
        Schema::dropIfExists('content_articles');
        Schema::dropIfExists('content_pillars');
        Schema::dropIfExists('content_authors');
    }
};
