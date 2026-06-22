<?php

header('Content-Type: application/json');

// Read .env file directly to see what was written by the entrypoint
$envFile = __DIR__ . '/../.env';
$envContents = file_exists($envFile) ? file_get_contents($envFile) : 'NOT FOUND';

// Parse .env into an array
$envParsed = [];
if (file_exists($envFile)) {
    foreach (explode("\n", $envContents) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            [$k, $v] = explode('=', $line, 2);
            $envParsed[trim($k)] = trim($v, '"\'');
        }
    }
}

$dbPath = $envParsed['DB_DATABASE'] ?? '';

$result = [
    'php_version' => PHP_VERSION,
    'env_file_exists' => file_exists($envFile),
    'env_file_size' => file_exists($envFile) ? filesize($envFile) : null,
    'env_app_key_set' => !empty($envParsed['APP_KEY']),
    'env_app_env' => $envParsed['APP_ENV'] ?? null,
    'env_db_database' => $dbPath,
    'env_session_driver' => $envParsed['SESSION_DRIVER'] ?? null,
    'db_file_exists' => $dbPath !== '' && file_exists($dbPath),
    'db_file_size' => $dbPath !== '' && file_exists($dbPath) ? filesize($dbPath) : null,
    'storage_writable' => is_writable(__DIR__ . '/../storage'),
    'error_log' => ini_get('error_log'),
    'env_raw' => $envContents,
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
