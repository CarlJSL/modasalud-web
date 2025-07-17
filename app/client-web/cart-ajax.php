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

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'add':
            $product_id = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            
            if ($product_id <= 0 || $quantity <= 0) {
                $response['message'] = 'Datos inválidos';
                break;
            }
            
            // Verificar stock
            if (!$cartModel->checkStock($cart_token, $product_id, $quantity)) {
                $response['message'] = 'Stock insuficiente';
                break;
            }
            
            if ($cartModel->addToCart($cart_token, $product_id, $quantity)) {
                $response['success'] = true;
                $response['message'] = 'Producto agregado al carrito';
            } else {
                $response['message'] = 'Error al agregar producto';
            }
            break;
            
        case 'update_quantity':
            $product_id = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            
            if ($product_id <= 0 || $quantity <= 0) {
                $response['message'] = 'Datos inválidos';
                break;
            }
            
            // Verificar stock
            if (!$cartModel->checkStock($cart_token, $product_id, $quantity)) {
                $response['message'] = 'Stock insuficiente';
                break;
            }
            
            if ($cartModel->updateQuantity($cart_token, $product_id, $quantity)) {
                $response['success'] = true;
                $response['message'] = 'Cantidad actualizada';
                $response['total'] = $cartModel->getCartTotal($cart_token);
            } else {
                $response['message'] = 'Error al actualizar cantidad';
            }
            break;
            
        case 'toggle_selection':
            $product_id = (int)($_POST['product_id'] ?? 0);
            // Mejorar manejo de valores booleanos
            $selectedValue = $_POST['selected'] ?? '';
            $selected = false;
            
            // Convertir explícitamente a booleano para evitar errores
            if ($selectedValue === 'true' || $selectedValue === '1' || $selectedValue === true) {
                $selected = true;
            }
            
            if ($product_id <= 0) {
                $response['message'] = 'Datos inválidos';
                break;
            }
            
            try {
                if ($cartModel->toggleSelection($cart_token, $product_id, $selected)) {
                    $response['success'] = true;
                    $response['message'] = 'Selección actualizada';
                    $response['total'] = $cartModel->getCartTotal($cart_token);
                } else {
                    $response['message'] = 'Error al actualizar selección';
                }
            } catch (Exception $e) {
                $response['message'] = 'Error del servidor: ' . $e->getMessage();
            }
            break;
            
        case 'remove':
            $product_id = (int)($_POST['product_id'] ?? 0);
            
            if ($product_id <= 0) {
                $response['message'] = 'Datos inválidos';
                break;
            }
            
            if ($cartModel->deleteItem($cart_token, $product_id)) {
                $response['success'] = true;
                $response['message'] = 'Producto eliminado';
                $response['total'] = $cartModel->getCartTotal($cart_token);
            } else {
                $response['message'] = 'Error al eliminar producto';
            }
            break;
            
        case 'clear':
            if ($cartModel->clearCart($cart_token)) {
                $response['success'] = true;
                $response['message'] = 'Carrito vaciado';
                $response['total'] = 0;
            } else {
                $response['message'] = 'Error al vaciar carrito';
            }
            break;
            
        case 'get_items':
            $items = $cartModel->getCartItems($cart_token);
            $response['success'] = true;
            $response['items'] = $items;
            $response['total'] = $cartModel->getCartTotal($cart_token);
            break;
            
        default:
            $response['message'] = 'Acción no válida';
    }
} catch (Exception $e) {
    $response['message'] = 'Error del servidor: ' . $e->getMessage();
}

echo json_encode($response);
?>
