<?php
require_once __DIR__ . '/../../config/config.php';

try {
    // Debug: Verificar que las variables estén disponibles
    if (!isset($_ENV['POSTGRES_HOST']) || !isset($_ENV['POSTGRES_PORT']) || 
        !isset($_ENV['POSTGRES_DB']) || !isset($_ENV['POSTGRES_USER']) || 
        !isset($_ENV['POSTGRES_PASSWORD'])) {
        die("❌ Error: Variables de entorno no están disponibles");
    }
    
    $dsn = "pgsql:host=" . $_ENV['POSTGRES_HOST'] . ";port=" . $_ENV['POSTGRES_PORT'] . ";dbname=" . $_ENV['POSTGRES_DB'];
    $pdo = new PDO($dsn, $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Crear alias para compatibilidad
    $conn = $pdo;
    
    // Debug: Confirmar conexión exitosa
    // echo "✅ Conexión establecida correctamente\n";
    
} catch (PDOException $e) {
    die("❌ Error de conexión a la base de datos: " . $e->getMessage());
} catch (Exception $e) {
    die("❌ Error general: " . $e->getMessage());
}
