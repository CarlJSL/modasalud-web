<?php
// Archivo para crear la tabla cart_items_temp si no existe
require_once __DIR__ . '/../conexion/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS cart_items_temp (
        id SERIAL PRIMARY KEY,
        cart_token VARCHAR(100) NOT NULL,
        product_id INTEGER NOT NULL,
        quantity INTEGER DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        selected BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "Tabla cart_items_temp creada exitosamente o ya existe.\n";
    
    // Crear índices para mejorar el rendimiento
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_cart_token ON cart_items_temp(cart_token)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_product_id ON cart_items_temp(product_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_cart_product ON cart_items_temp(cart_token, product_id)");
    
    echo "Índices creados exitosamente.\n";
    
} catch (PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage() . "\n";
}
?>
