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
        Schema::create('topic_users', function (Blueprint $table) {
            $table->id();
            $table->integer('topic_id')->nullable();
            $table->string('topic_name')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('data')->nullable();
            $table->json('history_chat')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_users');
    }
};
