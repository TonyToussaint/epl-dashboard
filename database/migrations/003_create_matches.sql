CREATE TABLE IF NOT EXISTS matches (
    id           SERIAL PRIMARY KEY,
    external_id  INTEGER NOT NULL UNIQUE,
    season_id    INTEGER NOT NULL REFERENCES seasons(id) ON DELETE CASCADE,
    home_team_id INTEGER NOT NULL REFERENCES teams(id),
    away_team_id INTEGER NOT NULL REFERENCES teams(id),
    home_score   INTEGER,
    away_score   INTEGER,
    status       VARCHAR(20) NOT NULL DEFAULT 'SCHEDULED',
    matchday     INTEGER,
    match_date   TIMESTAMP,
    fetched_at   TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_matches_season_id  ON matches(season_id);
CREATE INDEX IF NOT EXISTS idx_matches_status     ON matches(status);
CREATE INDEX IF NOT EXISTS idx_matches_match_date ON matches(match_date);
CREATE INDEX IF NOT EXISTS idx_matches_matchday   ON matches(matchday);