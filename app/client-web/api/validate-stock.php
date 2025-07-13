<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../utils/db_mapper.php';

try {
    // Obtener datos JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['items']) || !is_array($data['items'])) {
        throw new Exception('Datos de productos inválidos');
    }

    $invalidItems = [];
    $updatedItems = [];

    // Verificar cada producto
    foreach ($data['items'] as $item) {
        if (!isset($item['productId']) || !isset($item['quantity'])) {
            continue;
        }

        $stmt = $pdo->prepare("
            SELECT id, name, price, stock, status 
            FROM products 
            WHERE id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$item['productId']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            // Producto no encontrado
            $invalidItems[] = [
                'productId' => $item['productId'],
                'reason' => 'not_found',
                'message' => 'Producto no disponible'
            ];
            continue;
        }

        if ($product['status'] !== 'ACTIVE' && $product['status'] !== 'ON_SALE') {
            // Producto no está activo
            $invalidItems[] = [
                'productId' => $item['productId'],
                'reason' => 'inactive',
                'message' => 'Producto no disponible actualmente'
            ];
            continue;
        }

        if ($product['stock'] < $item['quantity']) {
            // Stock insuficiente
            $invalidItems[] = [
                'productId' => $item['productId'],
                'reason' => 'insufficient_stock',
                'message' => 'Stock insuficiente. Disponible: ' . $product['stock'],
                'availableStock' => $product['stock']
            ];
            continue;
        }

        // Verificar si el precio ha cambiado
        $priceChanged = false;
        if (isset($item['price']) && abs($product['price'] - $item['price']) > 0.01) {
            $priceChanged = true;
        }

        // Añadir a lista de items actualizados
        $updatedItems[] = [
            'productId' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'stock' => $product['stock'],
            'quantity' => min($item['quantity'], $product['stock']), // Ajustar cantidad si es necesario
            'priceChanged' => $priceChanged
        ];
    }

    echo json_encode([
        'success' => true,
        'valid' => empty($invalidItems),
        'invalidItems' => $invalidItems,
        'updatedItems' => $updatedItems
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos'
    ]);
    // Log detallado para depuración
    error_log('Error en validate-stock.php: ' . $e->getMessage());
}
?>
