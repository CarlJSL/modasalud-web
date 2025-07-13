<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../../dashboard-web/model/productModel.php';

use App\Model\ProductModel;

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID de producto no proporcionado');
    }
    
    $productId = (int)$_GET['id'];
    $productModel = new ProductModel($pdo, 'products');
    
    // Obtener detalles del producto
    $product = $productModel->getDetailedById($productId);
    
    if (!$product) {
        throw new Exception('Producto no encontrado');
    }
    
    echo json_encode([
        'success' => true,
        'product' => $product
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener el producto: ' . $e->getMessage()
    ]);
}
?>
