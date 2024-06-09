<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_day_completeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->integer('day_number')->default(0);
            $table->boolean('is_passed_first_quiz')->default(false);
            $table->boolean('is_passed_quiz_story_1')->default(false);
            $table->boolean('is_passed_quiz_story_2')->default(false);
            $table->boolean('is_passed_quiz_story_3')->default(false);
            $table->boolean('is_passed_quiz_story_4')->default(false);
            $table->boolean('is_passed_test_2')->default(false);
            $table->json('vocabulary_ids')->nullable();
            $table->string('words_to_gen_story_1')->nullable();
            $table->string('words_to_gen_story_2')->nullable();
            $table->string('words_to_gen_story_3')->nullable();
            $table->string('words_to_gen_story_4')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_day_completeds');
    }
};
