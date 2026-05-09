<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host     = $_ENV['DB_HOST'];
$port     = $_ENV['DB_PORT'];
$name     = $_ENV['DB_NAME'];
$user     = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$name",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Connected to database successfully.\n\n";

    $migrations = glob(__DIR__ . '/database/migrations/*.sql');
    sort($migrations);

    foreach ($migrations as $file) {
        $filename = basename($file);
        echo "Running: $filename ... ";

        $sql = file_get_contents($file);
        $pdo->exec($sql);

        echo "done.\n";
    }

    echo "\nAll migrations completed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
