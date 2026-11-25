<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('onboarding_completed_at')->nullable()->after('is_active');
            $table->softDeletes();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('preferred_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('age_range')->nullable();
            $table->string('gender')->nullable();
            $table->string('cultural_background')->nullable();
            $table->string('religious_spiritual_background')->nullable();
            $table->json('interpretive_perspectives')->nullable();
            $table->json('growth_goals')->nullable();
            $table->string('emotional_state')->nullable();
            $table->unsignedTinyInteger('sleep_quality')->nullable();
            $table->text('life_circumstances')->nullable();
            $table->timestamps();
        });

        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10)->default('ar');
            $table->string('timezone')->default('UTC');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('weekly_report_enabled')->default(true);
            $table->boolean('monthly_report_enabled')->default(true);
            $table->json('dashboard_widgets')->nullable();
            $table->boolean('allow_ai_training_consent')->default(false);
            $table->boolean('share_anonymized_insights')->default(false);
            $table->timestamps();
        });

        Schema::create('dream_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('dream_date');
            $table->time('dream_time')->nullable();
            $table->string('title')->nullable();
            $table->longText('original_text')->nullable();
            $table->longText('corrected_text')->nullable();
            $table->unsignedTinyInteger('sleep_quality')->nullable();
            $table->unsignedTinyInteger('emotional_intensity')->nullable();
            $table->string('mood_upon_waking')->nullable();
            $table->text('personal_notes')->nullable();
            $table->string('status')->default('draft');
            $table->string('processing_status')->default('idle');
            $table->string('entry_method')->default('text');
            $table->string('language', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'dream_date']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('dream_audio_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_entry_id')->constrained()->cascadeOnDelete();
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dream_transcriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dream_audio_file_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('original_transcription')->nullable();
            $table->longText('edited_transcription')->nullable();
            $table->string('detected_language', 10)->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->string('provider')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('analysis_prompt_versions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('version');
            $table->longText('system_prompt');
            $table->longText('user_prompt_template')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('dream_analyses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('dream_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prompt_version_id')->nullable()->constrained('analysis_prompt_versions')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->json('payload')->nullable();
            $table->text('summary')->nullable();
            $table->decimal('analysis_confidence', 5, 2)->nullable();
            $table->string('schema_version')->default('1.0');
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
        });

        Schema::create('dream_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dream_analysis_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('sequence_order')->default(0);
            $table->string('event_type')->nullable();
            $table->text('description');
            $table->json('entities')->nullable();
            $table->timestamps();
        });

        Schema::create('dream_symbols', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('normalized_key')->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('dream_symbol_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dream_analysis_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dream_symbol_id')->constrained()->cascadeOnDelete();
            $table->text('context')->nullable();
            $table->json('interpretations')->nullable();
            $table->decimal('interpretation_confidence', 5, 2)->nullable();
            $table->string('evidence_level')->nullable();
            $table->timestamps();

            $table->index(['dream_symbol_id', 'dream_entry_id']);
        });

        Schema::create('dream_emotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dream_analysis_id')->nullable()->constrained()->nullOnDelete();
            $table->string('emotion');
            $table->string('source')->default('user'); // user|dream|ai
            $table->unsignedTinyInteger('intensity')->nullable();
            $table->text('context')->nullable();
            $table->timestamps();

            $table->index(['dream_entry_id', 'emotion']);
        });

        Schema::create('dream_cultural_interpretations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_analysis_id')->constrained()->cascadeOnDelete();
            $table->string('tradition')->nullable();
            $table->string('source_label')->nullable();
            $table->text('interpretation');
            $table->string('confidence_note')->nullable();
            $table->timestamps();
        });

        Schema::create('dream_context_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_analysis_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_dream_entry_id')->nullable()->constrained('dream_entries')->nullOnDelete();
            $table->string('connection_type')->nullable();
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedInteger('dream_count')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'week_start']);
        });

        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedInteger('dream_count')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'year', 'month']);
        });

        Schema::create('development_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('why_suggested')->nullable();
            $table->string('status')->default('active');
            $table->date('review_date')->nullable();
            $table->json('related_dream_ids')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('development_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('development_theme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('cadence'); // daily|weekly|monthly
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('development_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('development_theme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('note');
            $table->unsignedTinyInteger('progress_score')->nullable();
            $table->timestamps();
        });

        Schema::create('reflection_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dream_analysis_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('development_action_id')->nullable()->constrained()->nullOnDelete();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dream_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role'); // user|assistant|system
            $table->longText('content');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('analysis_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dream_analysis_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('relevance'); // relevant|partial|not_relevant|discuss
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider');
            $table->string('model')->nullable();
            $table->string('operation');
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);
            $table->decimal('estimated_cost_usd', 10, 6)->nullable();
            $table->string('status')->default('success');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['provider', 'created_at']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('analysis_feedback');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('reflection_answers');
        Schema::dropIfExists('development_progress');
        Schema::dropIfExists('development_actions');
        Schema::dropIfExists('development_themes');
        Schema::dropIfExists('monthly_reports');
        Schema::dropIfExists('weekly_reports');
        Schema::dropIfExists('dream_context_connections');
        Schema::dropIfExists('dream_cultural_interpretations');
        Schema::dropIfExists('dream_emotions');
        Schema::dropIfExists('dream_symbol_occurrences');
        Schema::dropIfExists('dream_symbols');
        Schema::dropIfExists('dream_events');
        Schema::dropIfExists('dream_analyses');
        Schema::dropIfExists('analysis_prompt_versions');
        Schema::dropIfExists('dream_transcriptions');
        Schema::dropIfExists('dream_audio_files');
        Schema::dropIfExists('dream_entries');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('user_profiles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'onboarding_completed_at', 'deleted_at']);
        });
    }
};
