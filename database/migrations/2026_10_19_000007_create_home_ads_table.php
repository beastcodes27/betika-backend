<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bookmaker_id')->nullable()->constrained('bookmakers')->nullOnDelete();
            $table->string('badge');
            $table->string('title');
            $table->text('subtitle');
            $table->string('highlight');
            $table->string('cta_text')->default('View Promotion');
            $table->string('promo_code', 30)->nullable();
            $table->string('target_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('scroll_duration_seconds')->default(3);
            $table->integer('clicks_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_ads');
    }
};
