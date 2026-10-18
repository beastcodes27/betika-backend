<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('featured_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bookmaker_id')->nullable()->constrained('bookmakers')->nullOnDelete();
            $table->string('league')->default('UEFA CHAMPIONS LEAGUE');
            $table->string('kickoff')->default('Tonight 22:00');
            $table->string('venue')->default('Emirates Stadium');
            $table->string('home_team_name');
            $table->string('home_team_code', 10);
            $table->string('home_team_logo')->nullable();
            $table->string('home_team_color', 20)->default('#B91C1C');
            $table->string('home_team_form')->default('W-W-W-D');
            $table->decimal('home_odds', 6, 2)->default(1.95);
            $table->string('away_team_name');
            $table->string('away_team_code', 10);
            $table->string('away_team_logo')->nullable();
            $table->string('away_team_color', 20)->default('#1D4ED8');
            $table->string('away_team_form')->default('W-W-D-W');
            $table->decimal('away_odds', 6, 2)->default(3.40);
            $table->decimal('draw_odds', 6, 2)->default(3.25);
            $table->string('tipster_consensus')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_matches');
    }
};
