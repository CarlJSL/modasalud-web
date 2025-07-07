<?php

// 1. Cargar autoload de Composer
require_once __DIR__ . '/../app/vendor/autoload.php';

// 2. Cargar variables desde .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');

try {
    $dotenv->load();
} catch (Exception $e) {
    die("❌ No se pudo cargar el archivo .env: " . $e->getMessage());
}

// 3. Validar variables críticas
$requiredEnv = ['POSTGRES_DB', 'POSTGRES_USER', 'POSTGRES_PASSWORD', 'POSTGRES_PORT'];
$missing = [];

foreach ($requiredEnv as $env) {
    if (empty($_ENV[$env])) {
        $missing[] = $env;
    }
}

if (!empty($missing)) {
    die("❌ Faltan variables de entorno críticas: " . implode(', ', $missing));
}

// 4. Definir constantes con fallback y comentarios
define('DB_HOST', $_ENV['POSTGRES_HOST'] ?? 'db'); // nombre del servicio de Docker
define('DB_PORT', $_ENV['POSTGRES_PORT']);
define('DB_NAME', $_ENV['POSTGRES_DB']);
define('DB_USER', $_ENV['POSTGRES_USER']);
define('DB_PASS', $_ENV['POSTGRES_PASSWORD']);

// 5. Opcionales
define('APP_PORT', $_ENV['APP_PORT'] ?? '8000');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
