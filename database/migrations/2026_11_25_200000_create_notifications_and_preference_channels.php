<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::table('user_preferences', function (Blueprint $table) {
            $table->boolean('in_app_notifications')->default(true)->after('email_notifications');
            $table->boolean('daily_care_enabled')->default(true)->after('in_app_notifications');
            $table->boolean('daily_reminder_enabled')->default(true)->after('daily_care_enabled');
            $table->boolean('analysis_ready_enabled')->default(true)->after('daily_reminder_enabled');
            $table->boolean('growth_reminder_enabled')->default(true)->after('analysis_ready_enabled');
            $table->unsignedTinyInteger('daily_care_hour')->default(8)->after('growth_reminder_enabled');
            $table->unsignedTinyInteger('daily_reminder_hour')->default(20)->after('daily_care_hour');
        });
    }

    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn([
                'in_app_notifications',
                'daily_care_enabled',
                'daily_reminder_enabled',
                'analysis_ready_enabled',
                'growth_reminder_enabled',
                'daily_care_hour',
                'daily_reminder_hour',
            ]);
        });

        Schema::dropIfExists('notifications');
    }
};
