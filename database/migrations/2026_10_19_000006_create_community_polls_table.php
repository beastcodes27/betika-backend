<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_match_id')->constrained('featured_matches')->cascadeOnDelete();
            $table->integer('home_votes_count')->default(8420);
            $table->integer('draw_votes_count')->default(2140);
            $table->integer('away_votes_count')->default(3860);
            $table->integer('total_votes_count')->default(14420);
            $table->boolean('is_live')->default(true);
            $table->timestamps();
        });

        Schema::create('community_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_match_id')->constrained('featured_matches')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('device_id')->nullable()->index();
            $table->enum('vote_choice', ['1', 'X', '2']);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_poll_votes');
        Schema::dropIfExists('community_polls');
    }
};
