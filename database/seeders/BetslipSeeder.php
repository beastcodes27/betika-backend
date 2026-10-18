<?php

namespace Database\Seeders;

use App\Models\Betslip;
use App\Models\Bookmaker;
use App\Models\MatchSelection;
use App\Models\TipsterProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BetslipSeeder extends Seeder
{
    public function run(): void
    {
        $betika = Bookmaker::where('slug', 'betika')->first();
        $sportybet = Bookmaker::where('slug', 'sportybet')->first();
        $mozzart = Bookmaker::where('slug', 'mozzartbet')->first();
        $onexbet = Bookmaker::where('slug', '1xbet')->first();
        $betway = Bookmaker::where('slug', 'betway')->first();

        $captain = TipsterProfile::where('handle', '@captain_odds')->first();
        $goalmaster = TipsterProfile::where('handle', '@goal_master')->first();
        $sniper = TipsterProfile::where('handle', '@sniper_bets')->first();
        $vipelite = TipsterProfile::where('handle', '@vip_elite')->first();
        $draws = TipsterProfile::where('handle', '@draw_tactics')->first();

        $slips = [
            [
                'tipster_id' => $captain?->id,
                'bookmaker_id' => $betika?->id,
                'title' => 'Weekend 4-Fold Banker Combo',
                'slug' => 'weekend-4-fold-banker-combo',
                'category' => 'Banker',
                'booking_code' => 'BTK-94821',
                'total_odds' => 5.48,
                'matches_count' => 4,
                'validity_time' => 'Valid for 1h 35m',
                'amount' => 'Free',
                'is_free' => true,
                'confidence' => 89,
                'status' => 'active',
                'views_count' => 1240,
                'copies_count' => 820,
                'selections' => [
                    ['match' => 'Arsenal vs Chelsea', 'league' => 'Premier League', 'kickoff' => 'Today 19:30', 'pick' => 'Arsenal to Win (1)', 'odds' => 1.65, 'confidence' => 88],
                    ['match' => 'Real Madrid vs Sevilla', 'league' => 'La Liga', 'kickoff' => 'Today 21:00', 'pick' => 'Real Madrid Over 1.5 Team Goals', 'odds' => 1.50, 'confidence' => 90],
                    ['match' => 'Inter vs Juventus', 'league' => 'Serie A', 'kickoff' => 'Today 20:45', 'pick' => 'Both Teams to Score (BTTS)', 'odds' => 1.78, 'confidence' => 84],
                    ['match' => 'Bayern Munich vs Dortmund', 'league' => 'Bundesliga', 'kickoff' => 'Today 20:30', 'pick' => 'Over 2.5 Total Goals', 'odds' => 1.25, 'confidence' => 94],
                ],
            ],
            [
                'tipster_id' => $goalmaster?->id,
                'bookmaker_id' => $sportybet?->id,
                'title' => 'High Odds Goal Rush Ticket',
                'slug' => 'high-odds-goal-rush-ticket',
                'category' => 'Goals',
                'booking_code' => 'SB-38194',
                'total_odds' => 14.65,
                'matches_count' => 5,
                'validity_time' => 'Valid for 3h 10m',
                'amount' => 'Tshs 1,000',
                'is_free' => false,
                'confidence' => 76,
                'status' => 'active',
                'views_count' => 2410,
                'copies_count' => 640,
                'selections' => [
                    ['match' => 'Liverpool vs Aston Villa', 'league' => 'Premier League', 'kickoff' => 'Today 22:00', 'pick' => 'Over 3.5 Goals', 'odds' => 2.10, 'confidence' => 78],
                    ['match' => 'PSG vs Marseille', 'league' => 'Ligue 1', 'kickoff' => 'Today 21:45', 'pick' => 'Over 2.5 & BTTS', 'odds' => 1.95, 'confidence' => 80],
                    ['match' => 'Leverkusen vs Leipzig', 'league' => 'Bundesliga', 'kickoff' => 'Today 20:30', 'pick' => 'Over 2.5 Goals', 'odds' => 1.62, 'confidence' => 85],
                    ['match' => 'Napoli vs Roma', 'league' => 'Serie A', 'kickoff' => 'Today 21:00', 'pick' => 'Both Teams to Score', 'odds' => 1.82, 'confidence' => 75],
                    ['match' => 'Ajax vs Feyenoord', 'league' => 'Eredivisie', 'kickoff' => 'Today 21:00', 'pick' => 'Over 2.5 Goals', 'odds' => 1.50, 'confidence' => 88],
                ],
            ],
            [
                'tipster_id' => $sniper?->id,
                'bookmaker_id' => $mozzart?->id,
                'title' => 'Super Safe Double (91% Banker)',
                'slug' => 'super-safe-double-91-banker',
                'category' => 'Banker',
                'booking_code' => 'MOZ-11209',
                'total_odds' => 2.38,
                'matches_count' => 2,
                'validity_time' => 'Valid for 50m',
                'amount' => 'Free',
                'is_free' => true,
                'confidence' => 93,
                'status' => 'active',
                'views_count' => 3120,
                'copies_count' => 1950,
                'selections' => [
                    ['match' => 'Manchester City vs Wolves', 'league' => 'Premier League', 'kickoff' => 'Today 17:00', 'pick' => 'Man City Win & Over 1.5 Goals', 'odds' => 1.42, 'confidence' => 95],
                    ['match' => 'Barcelona vs Valencia', 'league' => 'La Liga', 'kickoff' => 'Today 18:30', 'pick' => 'Barcelona to Win (1)', 'odds' => 1.68, 'confidence' => 92],
                ],
            ],
            [
                'tipster_id' => $vipelite?->id,
                'bookmaker_id' => $onexbet?->id,
                'title' => 'Weekend 32.00 Mega Parlay',
                'slug' => 'weekend-32-00-mega-parlay',
                'category' => 'VIP',
                'booking_code' => '1X-88741',
                'total_odds' => 32.40,
                'matches_count' => 6,
                'validity_time' => 'Valid for 2h 20m',
                'amount' => 'Tshs 2,000',
                'is_free' => false,
                'confidence' => 72,
                'status' => 'active',
                'views_count' => 1890,
                'copies_count' => 410,
                'selections' => [
                    ['match' => 'Newcastle vs Tottenham', 'league' => 'Premier League', 'kickoff' => 'Today 19:30', 'pick' => 'Both Teams to Score & Over 2.5', 'odds' => 1.90, 'confidence' => 82],
                    ['match' => 'Atalanta vs AC Milan', 'league' => 'Serie A', 'kickoff' => 'Today 20:45', 'pick' => 'Atalanta Draw No Bet', 'odds' => 1.75, 'confidence' => 76],
                    ['match' => 'Atletico Madrid vs Bilbao', 'league' => 'La Liga', 'kickoff' => 'Today 22:00', 'pick' => 'Under 2.5 Goals', 'odds' => 1.85, 'confidence' => 79],
                    ['match' => 'Sporting CP vs Porto', 'league' => 'Primeira Liga', 'kickoff' => 'Today 21:30', 'pick' => 'Over 1.5 Total Goals', 'odds' => 1.35, 'confidence' => 90],
                    ['match' => 'Galatasaray vs Besiktas', 'league' => 'Super Lig', 'kickoff' => 'Today 20:00', 'pick' => 'Both Teams to Score', 'odds' => 1.72, 'confidence' => 78],
                    ['match' => 'Celtic vs Rangers', 'league' => 'Premiership', 'kickoff' => 'Today 19:00', 'pick' => 'Celtic to Win (1)', 'odds' => 1.95, 'confidence' => 75],
                ],
            ],
            [
                'tipster_id' => $draws?->id,
                'bookmaker_id' => $betway?->id,
                'title' => 'Weekend Betway Value Treble',
                'slug' => 'weekend-betway-value-treble',
                'category' => 'High Odds',
                'booking_code' => 'BTW-30948',
                'total_odds' => 8.85,
                'matches_count' => 3,
                'validity_time' => 'Valid for 4h 15m',
                'amount' => 'Tshs 1,000',
                'is_free' => false,
                'confidence' => 81,
                'status' => 'active',
                'views_count' => 980,
                'copies_count' => 310,
                'selections' => [
                    ['match' => 'Juventus vs Lazio', 'league' => 'Serie A', 'kickoff' => 'Today 21:45', 'pick' => 'Juventus to Win & Under 3.5 Goals', 'odds' => 2.10, 'confidence' => 83],
                    ['match' => 'Sevilla vs Real Sociedad', 'league' => 'La Liga', 'kickoff' => 'Today 20:00', 'pick' => 'Draw at Half Time (X HT)', 'odds' => 2.05, 'confidence' => 79],
                    ['match' => 'Frankfurt vs Stuttgart', 'league' => 'Bundesliga', 'kickoff' => 'Today 19:30', 'pick' => 'Both Teams to Score (BTTS)', 'odds' => 1.55, 'confidence' => 88],
                ],
            ],
        ];

        foreach ($slips as $data) {
            $selections = $data['selections'];
            unset($data['selections']);

            $slip = Betslip::updateOrCreate(['slug' => $data['slug']], $data);

            foreach ($selections as $index => $sel) {
                MatchSelection::updateOrCreate(
                    [
                        'betslip_id' => $slip->id,
                        'match' => $sel['match'],
                    ],
                    array_merge($sel, ['sort_order' => $index + 1])
                );
            }
        }
    }
}
