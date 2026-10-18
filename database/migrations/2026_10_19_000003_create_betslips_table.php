<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('betslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipster_id')->constrained('tipster_profiles')->cascadeOnDelete();
            $table->foreignId('bookmaker_id')->constrained('bookmakers')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', ['Banker', 'High Odds', 'Goals', 'Combo', 'VIP'])->default('Banker');
            $table->string('booking_code');
            $table->decimal('total_odds', 8, 2);
            $table->integer('matches_count')->default(1);
            $table->string('validity_time')->default('Valid for 2 hours');
            $table->timestamp('expires_at')->nullable();
            $table->string('amount')->default('Free');
            $table->boolean('is_free')->default(true);
            $table->integer('confidence')->default(85);
            $table->enum('status', ['active', 'won', 'lost', 'void', 'archived'])->default('active');
            $table->integer('views_count')->default(0);
            $table->integer('copies_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('betslips');
    }
};
