<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('preferred_language', 10)->nullable()->after('preferred_name');
            $table->string('country_of_origin')->nullable()->after('cultural_background');
            $table->string('country_of_residence')->nullable()->after('country_of_origin');
            $table->string('profile_status')->default('not_started')->after('life_circumstances');
            $table->unsignedTinyInteger('onboarding_stage')->default(0)->after('profile_status');
            $table->string('current_section_slug')->nullable()->after('onboarding_stage');
            $table->timestamp('profile_last_reviewed_at')->nullable()->after('current_section_slug');
            $table->unsignedTinyInteger('completion_percent')->default(0)->after('profile_last_reviewed_at');
        });

        Schema::create('profile_sections', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->string('stage')->default('deeper'); // initial|deeper|continuous
            $table->boolean('is_sensitive')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('profile_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('profile_sections')->cascadeOnDelete();
            $table->string('question_key')->unique();
            $table->string('answer_type');
            $table->json('options_json')->nullable();
            $table->json('validation_rules_json')->nullable();
            $table->json('conditional_rules_json')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_sensitive')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('include_in_stage1')->default(false);
            $table->unsignedInteger('display_order')->default(0);
            $table->string('profile_dimension')->nullable();
            $table->string('ai_context_relevance')->default('medium');
            $table->string('version')->default('1.0');
            $table->timestamps();

            $table->index(['section_id', 'display_order']);
            $table->index(['is_active', 'include_in_stage1']);
        });

        Schema::create('profile_question_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('profile_questions')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->text('question_text');
            $table->text('description')->nullable();
            $table->json('options_json')->nullable();
            $table->timestamps();

            $table->unique(['question_id', 'locale']);
        });

        Schema::create('profile_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('profile_questions')->cascadeOnDelete();
            $table->string('question_version')->default('1.0');
            $table->longText('answer_value')->nullable();
            $table->json('answer_metadata_json')->nullable();
            $table->boolean('excluded_from_ai')->default(false);
            $table->boolean('is_draft')->default(false);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
            $table->index(['user_id', 'answered_at']);
        });

        Schema::create('profile_answer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('profile_questions')->cascadeOnDelete();
            $table->longText('previous_value')->nullable();
            $table->longText('new_value')->nullable();
            $table->string('change_source')->default('user');
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['user_id', 'question_id', 'changed_at']);
        });

        Schema::create('profile_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('consent_type');
            $table->boolean('granted')->default(false);
            $table->string('purpose')->nullable();
            $table->string('policy_version')->default('1.0');
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'consent_type']);
        });

        Schema::create('profile_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version_number')->default(1);
            $table->text('profile_summary')->nullable();
            $table->json('structured_profile_json')->nullable();
            $table->string('generated_by')->default('system');
            $table->json('source_answer_ids')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'version_number']);
        });

        Schema::create('profile_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('dimension')->nullable();
            $table->text('observation');
            $table->json('evidence_json')->nullable();
            $table->string('source_type'); // user_reported|user_confirmed|ai_hypothesis|historical|unknown
            $table->string('source_id')->nullable();
            $table->string('status')->default('pending');
            $table->string('confidence_level')->nullable();
            $table->string('user_confirmation_status')->default('pending');
            $table->timestamp('first_observed_at')->nullable();
            $table->timestamp('last_observed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'source_type']);
        });

        Schema::create('profile_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('development_dimension')->nullable();
            $table->date('target_date')->nullable();
            $table->string('status')->default('active');
            $table->unsignedTinyInteger('importance')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('profile_review_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('review_type'); // weekly|monthly|quarterly|onboarding
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('answers_json')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'review_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_review_sessions');
        Schema::dropIfExists('profile_goals');
        Schema::dropIfExists('profile_observations');
        Schema::dropIfExists('profile_snapshots');
        Schema::dropIfExists('profile_consents');
        Schema::dropIfExists('profile_answer_histories');
        Schema::dropIfExists('profile_answers');
        Schema::dropIfExists('profile_question_translations');
        Schema::dropIfExists('profile_questions');
        Schema::dropIfExists('profile_sections');

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_language',
                'country_of_origin',
                'country_of_residence',
                'profile_status',
                'onboarding_stage',
                'current_section_slug',
                'profile_last_reviewed_at',
                'completion_percent',
            ]);
        });
    }
};
