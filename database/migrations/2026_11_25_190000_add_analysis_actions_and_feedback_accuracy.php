<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('development_actions', function (Blueprint $table) {
            $table->foreignId('dream_analysis_id')
                ->nullable()
                ->after('user_id')
                ->constrained('dream_analyses')
                ->nullOnDelete();
        });

        Schema::table('analysis_feedback', function (Blueprint $table) {
            $table->unsignedTinyInteger('accuracy_rating')->nullable()->after('relevance');
            $table->boolean('will_try_action')->nullable()->after('accuracy_rating');
        });
    }

    public function down(): void
    {
        Schema::table('analysis_feedback', function (Blueprint $table) {
            $table->dropColumn(['accuracy_rating', 'will_try_action']);
        });

        Schema::table('development_actions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dream_analysis_id');
        });
    }
};
