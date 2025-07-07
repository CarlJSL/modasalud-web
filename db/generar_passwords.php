<?php
// Script para generar contraseñas hasheadas para testing
echo "=== Generador de Contraseñas Hasheadas ===\n\n";

$passwords = [
    '123456',
    'admin123',
    'password',
    'test123'
];

foreach ($passwords as $password) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    echo "Contraseña: $password\n";
    echo "Hash: $hashed\n\n";
}

// Verificar que funciona
echo "=== Verificación ===\n";
$testPassword = '123456';
$testHash = password_hash($testPassword, PASSWORD_DEFAULT);
$isValid = password_verify($testPassword, $testHash);
echo "Contraseña '$testPassword' verifica correctamente: " . ($isValid ? 'SÍ' : 'NO') . "\n";
?>
