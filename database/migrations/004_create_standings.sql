CREATE TABLE IF NOT EXISTS standings (
    id               SERIAL PRIMARY KEY,
    season_id        INTEGER NOT NULL REFERENCES seasons(id) ON DELETE CASCADE,
    team_id          INTEGER NOT NULL REFERENCES teams(id),
    position         INTEGER NOT NULL,
    played           INTEGER NOT NULL DEFAULT 0,
    won              INTEGER NOT NULL DEFAULT 0,
    drawn            INTEGER NOT NULL DEFAULT 0,
    lost             INTEGER NOT NULL DEFAULT 0,
    goals_for        INTEGER NOT NULL DEFAULT 0,
    goals_against    INTEGER NOT NULL DEFAULT 0,
    goal_difference  INTEGER NOT NULL DEFAULT 0,
    points           INTEGER NOT NULL DEFAULT 0,
    form             VARCHAR(10),
    fetched_at       TIMESTAMP DEFAULT NOW(),
    UNIQUE(season_id, team_id)
);

CREATE INDEX IF NOT EXISTS idx_standings_season_id ON standings(season_id);
CREATE INDEX IF NOT EXISTS idx_standings_position  ON standings(position);