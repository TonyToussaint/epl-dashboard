<?php

namespace Anthony\EplDashboard\Services;

use Anthony\EplDashboard\Api\ApiClient;
use PDO;

class StandingsService
{
    private ApiClient $api;
    private CacheService $cache;
    private PDO $pdo;

    public function __construct(ApiClient $api, CacheService $cache, PDO $pdo)
    {
        $this->api   = $api;
        $this->cache = $cache;
        $this->pdo   = $pdo;
    }

    public function getStandings(int $seasonId = 2021): array
    {
        $endpoint = "/competitions/PL/standings?season={$seasonId}";

        $cached = $this->cache->get($endpoint);

        if ($cached !== null) {
            return json_decode($cached, true);
        }

        $response = $this->api->get($endpoint);

        $this->cache->set($endpoint, json_encode($response));
        $this->saveStandings($response, $seasonId);

        return $response;
    }

    private function saveStandings(array $standings, int $seasonId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO standings 
                (season_id, team_id, position, played, won, drawn, lost,
                 goals_for, goals_against, goal_difference, points, form, fetched_at)
            SELECT
                :season_id,
                t.id,
                :position, :played, :won, :drawn, :lost,
                :goals_for, :goals_against, :goal_difference, :points, :form,
                NOW()
            FROM teams t
            WHERE t.external_id = :external_id
            ON CONFLICT (season_id, team_id)
            DO UPDATE SET
                position        = EXCLUDED.position,
                played          = EXCLUDED.played,
                won             = EXCLUDED.won,
                drawn           = EXCLUDED.drawn,
                lost            = EXCLUDED.lost,
                goals_for       = EXCLUDED.goals_for,
                goals_against   = EXCLUDED.goals_against,
                goal_difference = EXCLUDED.goal_difference,
                points          = EXCLUDED.points,
                form            = EXCLUDED.form,
                fetched_at      = NOW()'
        );

        // Fix 1 - add foreach loop here, looping over $standings['standings'][0]['table']
        foreach ($standings['standings'][0]['table'] as $row) {

            // Fix 2 - call $stmt->execute() inside the loop with the correct array
            // map each :placeholder to the right value from $row
            // hint: $row['team']['id'] is the external_id
            // hint: $row['draw'] is what the API calls drawn games
            $stmt->execute([
                ':season_id'       => $seasonId,
                ':position'        => $row['position'],        // 1
                ':external_id'     => $row['team']['id'],
                ':played'          => $row['playedGames'],    // 30
                ':won'             => $row['won'],             // 21
                ':drawn'           => $row['draw'],            // 5
                ':lost'            => $row['lost'],            // 4
                ':points'          => $row['points'],          // 68
                ':goals_for'       => $row['goalsFor'],        // 72
                ':goals_against'   => $row['goalsAgainst'],    // 30
                ':goal_difference' => $row['goalDifference'],  // 42
                ':form'            => $row['form'],            // "WWDWW"
            ]);
        }
        // close foreach here
    }
}
