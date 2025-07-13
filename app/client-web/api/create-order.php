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
require_once __DIR__ . '/../../dashboard-web/model/orderModel.php';
require_once __DIR__ . '/../utils/db_mapper.php';

use App\Model\OrderModel;

try {
    // Obtener datos JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Datos inválidos');
    }

    // Validar datos requeridos
    $required = ['client', 'address', 'items', 'total_price', 'payment_method'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    // Validar que hay productos
    if (empty($data['items'])) {
        throw new Exception('No hay productos en la orden');
    }

    // Validar datos del cliente
    $clientRequired = ['name', 'email', 'phone'];
    foreach ($clientRequired as $field) {
        if (empty($data['client'][$field])) {
            throw new Exception("Campo de cliente requerido: $field");
        }
    }
    
    // El DNI es importante pero puede no ser obligatorio para todos los clientes
    if (isset($data['client']['dni']) && empty($data['client']['dni'])) {
        $data['client']['dni'] = null; // Permitir DNI nulo
    }

    // Validar datos de dirección
    $addressRequired = ['address', 'city'];
    foreach ($addressRequired as $field) {
        if (empty($data['address'][$field])) {
            throw new Exception("Campo de dirección requerido: $field");
        }
    }

    $pdo->beginTransaction();

    // 1. Verificar si el cliente ya existe por email (principal) o DNI (si está disponible)
    $params = [$data['client']['email']];
    $sql = "SELECT id FROM clients WHERE email = ?";
    
    if (!empty($data['client']['dni'])) {
        $sql .= " OR dni = ?";
        $params[] = $data['client']['dni'];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $existingClient = $stmt->fetch();

    if ($existingClient) {
        $clientId = $existingClient['id'];
        
        // Actualizar datos del cliente existente
        $stmt = $pdo->prepare("
            UPDATE clients 
            SET name = ?, email = ?, phone = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $data['client']['name'],
            $data['client']['email'],
            $data['client']['phone'],
            $clientId
        ]);
    } else {
        // Crear nuevo cliente
        $stmt = $pdo->prepare("
            INSERT INTO clients (name, dni, email, phone, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 'ACTIVE', NOW(), NOW())
            RETURNING id
        ");
        
        // Asegurarse de que el DNI sea un valor válido o NULL
        $dni = !empty($data['client']['dni']) ? $data['client']['dni'] : null;
        
        $stmt->execute([
            $data['client']['name'],
            $dni,
            $data['client']['email'],
            $data['client']['phone']
        ]);
        $clientId = $stmt->fetchColumn();
    }

    // 2. Crear dirección de entrega
    $stmt = $pdo->prepare("
        INSERT INTO client_addresses (client_id, address, city, region, postal_code, phone, is_default, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, false, NOW(), NOW())
        RETURNING id
    ");
    $stmt->execute([
        $clientId,
        $data['address']['address'],
        $data['address']['city'],
        $data['address']['region'] ?? null,
        $data['address']['postal_code'] ?? null,
        $data['client']['phone'] // Usar el teléfono del cliente como teléfono de entrega
    ]);
    $addressId = $stmt->fetchColumn();

    // 3. Validar stock de productos
    foreach ($data['items'] as $item) {
        $stmt = $pdo->prepare("SELECT stock, price FROM products WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$item['productId']]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception("Producto no encontrado: ID {$item['productId']}");
        }

        if ($product['stock'] < $item['quantity']) {
            throw new Exception("Stock insuficiente para el producto ID {$item['productId']}");
        }

        // Verificar que el precio no haya cambiado significativamente
        $priceDiff = abs($product['price'] - $item['price']);
        if ($priceDiff > 0.01) {
            throw new Exception("El precio del producto ha cambiado. Por favor actualiza tu carrito.");
        }
    }

    // 4. Crear la orden
    $stmt = $pdo->prepare("
        INSERT INTO orders (client_id, address_id, total_price, status, created_at, order_source, created_by, discount_amount)
        VALUES (?, ?, ?, 'PENDING', NOW(), 'WEB', NULL, 0)
        RETURNING id
    ");
    $stmt->execute([$clientId, $addressId, $data['total_price']]);
    $orderId = $stmt->fetchColumn();

    // 5. Crear items de la orden y actualizar stock
    foreach ($data['items'] as $item) {
        // Insertar item de orden
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $orderId,
            $item['productId'],
            $item['quantity'],
            $item['price']
        ]);

        // Actualizar stock del producto
        $stmt = $pdo->prepare("
            UPDATE products 
            SET stock = stock - ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$item['quantity'], $item['productId']]);
    }

    // 6. Crear registro de pago (validando que el método sea un valor aceptado)
    $paymentMethod = validatePaymentMethod($data['payment_method']);
    
    $stmt = $pdo->prepare("
        INSERT INTO payments (order_id, method, status, paid_at, proof_url)
        VALUES (?, ?, 'PENDING', NULL, NULL)
    ");
    $stmt->execute([$orderId, $paymentMethod]);

    $pdo->commit();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Orden creada exitosamente',
        'order_id' => $orderId,
        'client_id' => $clientId
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Registrar el error para propósitos de depuración
    error_log("Error en create-order.php: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la orden: ' . $e->getMessage()
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Registrar el error para propósitos de depuración
    error_log("Error de base de datos en create-order.php: " . $e->getMessage());
    
    // Mensaje de error amigable para el usuario
    $errorMsg = 'Error al procesar la orden en la base de datos';
    
    // En ambiente de desarrollo, mostrar el mensaje real
    if (defined('APP_ENV') && $_ENV['APP_ENV'] === 'development') {
        $errorMsg .= ': ' . $e->getMessage();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $errorMsg
    ]);
}
?>
