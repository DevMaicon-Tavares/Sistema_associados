<?php

header('Content-Type: application/json');

$dbPath = getenv('DB_DATABASE') ?: '';

$result = [
    'php_version' => PHP_VERSION,
    'app_env' => getenv('APP_ENV'),
    'app_debug' => getenv('APP_DEBUG'),
    'app_url' => getenv('APP_URL'),
    'db_connection' => getenv('DB_CONNECTION'),
    'db_database' => $dbPath,
    'db_file_exists' => $dbPath !== '' && file_exists($dbPath),
    'db_file_size' => $dbPath !== '' && file_exists($dbPath) ? filesize($dbPath) : null,
    'db_directory' => $dbPath !== '' ? dirname($dbPath) : null,
    'db_directory_writable' => $dbPath !== '' ? is_writable(dirname($dbPath)) : null,
    'storage_exists' => is_dir(__DIR__ . '/../storage'),
    'storage_writable' => is_writable(__DIR__ . '/../storage'),
    'vendor_exists' => is_dir(__DIR__ . '/../vendor'),
    'error_log' => ini_get('error_log'),
    'cwd' => getcwd(),
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
