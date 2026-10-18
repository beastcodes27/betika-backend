<?php

namespace Database\Seeders;

use App\Models\Bookmaker;
use App\Models\CommunityPoll;
use App\Models\FeaturedMatch;
use Illuminate\Database\Seeder;

class FeaturedMatchSeeder extends Seeder
{
    public function run(): void
    {
        $betika = Bookmaker::where('slug', 'betika')->first();

        $featured = FeaturedMatch::updateOrCreate(
            ['home_team_name' => 'Arsenal', 'away_team_name' => 'Chelsea'],
            [
                'bookmaker_id' => $betika?->id,
                'league' => 'UEFA CHAMPIONS LEAGUE • TONIGHT 22:00',
                'kickoff' => 'Tonight 22:00',
                'venue' => 'Emirates Stadium',
                'home_team_name' => 'Arsenal',
                'home_team_code' => 'ARS',
                'home_team_logo' => 'teams/arsenal.png',
                'home_team_color' => '#B91C1C',
                'home_team_form' => 'Form: W-W-W-D',
                'home_odds' => 1.95,
                'away_team_name' => 'Chelsea',
                'away_team_code' => 'CHE',
                'away_team_logo' => 'teams/chelsea.png',
                'away_team_color' => '#1D4ED8',
                'away_team_form' => 'Form: W-W-D-W',
                'away_odds' => 3.40,
                'draw_odds' => 3.25,
                'tipster_consensus' => '74% Predict Arsenal Win (1)',
                'is_active' => true,
            ]
        );

        CommunityPoll::updateOrCreate(
            ['featured_match_id' => $featured->id],
            [
                'home_votes_count' => 8420,
                'draw_votes_count' => 2140,
                'away_votes_count' => 3860,
                'total_votes_count' => 14420,
                'is_live' => true,
            ]
        );
    }
}
