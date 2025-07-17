<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/carrtempModel.php';

use App\Model\CarrTempModel;

header('Content-Type: application/json');

// Obtener o crear token del carrito
if (!isset($_SESSION['cart_token'])) {
    $_SESSION['cart_token'] = bin2hex(random_bytes(16));
}

$cart_token = $_SESSION['cart_token'];
$cartModel = new CarrTempModel($pdo);

try {
    $cartItemCount = $cartModel->getCartItemCount($cart_token);
    echo json_encode([
        'success' => true,
        'count' => $cartItemCount
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener contador del carrito'
    ]);
}
?>
