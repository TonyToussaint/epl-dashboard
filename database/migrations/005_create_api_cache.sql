CREATE TABLE IF NOT EXISTS api_cache (
    id           SERIAL PRIMARY KEY,
    endpoint     VARCHAR(255) NOT NULL UNIQUE,
    response_body TEXT NOT NULL,
    http_status  INTEGER NOT NULL DEFAULT 200,
    fetched_at   TIMESTAMP DEFAULT NOW(),
    expires_at   TIMESTAMP NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_api_cache_endpoint   ON api_cache(endpoint);
CREATE INDEX IF NOT EXISTS idx_api_cache_expires_at ON api_cache(expires_at);