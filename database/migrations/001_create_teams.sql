CREATE TABLE IF NOT EXISTS teams (
    id SERIAL PRIMARY KEY,
    external_id INTEGER NOT NULL UNIQUE, 
    name VARCHAR(100) NOT NULL,
    short_name VARCHAR(50),
    tla VARCHAR(3), 
    crest_url TEXT,
    created_at TIMESTAMP DEFAULT NOW()
); 

CREATE INDEX IF NOT EXISTS idx_teams_external_id ON teams(external_id);