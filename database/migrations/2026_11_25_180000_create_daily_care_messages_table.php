<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_care_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('care_date');
            $table->string('emoji', 16)->default('💛');
            $table->string('title');
            $table->text('body');
            $table->string('tone', 40)->default('warm');
            $table->string('focus', 40)->nullable();
            $table->string('source', 40)->default('rule');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'care_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_care_messages');
    }
};
