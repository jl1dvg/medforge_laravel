<?php
// config/database.php

static $pdo = null;

if ($pdo instanceof PDO) {
    return $pdo;
}

$host = getenv('DB_HOST') ?: '';
$port = getenv('DB_PORT') ?: '3306';
$db = getenv('DB_DATABASE') ?: (getenv('DB_NAME') ?: '');
$user = getenv('DB_USERNAME') ?: (getenv('DB_USER') ?: '');
$pass = getenv('DB_PASSWORD') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';
$timezone = getenv('DB_TIMEZONE') ?: '-05:00';

$required = [
    'DB_HOST' => $host,
    'DB_DATABASE|DB_NAME' => $db,
    'DB_USERNAME|DB_USER' => $user,
];

$missing = [];
foreach ($required as $key => $value) {
    if ($value === '') {
        $missing[] = $key;
    }
}

if ($missing !== []) {
    die('Faltan variables de entorno para la conexión DB: ' . implode(', ', $missing));
}

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $db, $charset);

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET time_zone = '" . addslashes($timezone) . "'");
} catch (PDOException $e) {
    die('Error en la conexión a la base de datos: ' . $e->getMessage());
}

return $pdo;
