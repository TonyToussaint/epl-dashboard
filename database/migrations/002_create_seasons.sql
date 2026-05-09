CREATE TABLE IF NOT EXISTS seasons (
    id SERIAL PRIMARY KEY, 
    external_id INTEGER NOT NULL UNIQUE, 
    year INTEGER NOT NULL, 
    name VARCHAR(50) NOT NULL, 
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_seasons_year ON seasons(year);