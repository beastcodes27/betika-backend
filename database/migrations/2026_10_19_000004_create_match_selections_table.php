<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('betslip_id')->constrained('betslips')->cascadeOnDelete();
            $table->string('match');
            $table->string('home_team')->nullable();
            $table->string('away_team')->nullable();
            $table->string('league');
            $table->string('kickoff');
            $table->string('pick');
            $table->decimal('odds', 6, 2);
            $table->integer('confidence')->default(85);
            $table->enum('result', ['pending', 'won', 'lost', 'void'])->default('pending');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_selections');
    }
};
