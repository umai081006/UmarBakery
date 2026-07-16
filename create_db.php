<?php
$envPath = __DIR__.'/.env';
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach($lines as $line) {
    if(strpos(trim($line), '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if(count($parts) == 2) {
        $env[trim($parts[0])] = trim($parts[1], '"\'');
    }
}

try {
    $pdo = new PDO('pgsql:host='.$env['DB_HOST'].';port='.$env['DB_PORT'], $env['DB_USERNAME'], $env['DB_PASSWORD']);
    $pdo->exec('CREATE DATABASE "'.$env['DB_DATABASE'].'"');
    echo "Database created!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
