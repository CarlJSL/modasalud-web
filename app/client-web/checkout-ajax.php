<?php

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/carrtempModel.php';
require_once __DIR__ . '/../dashboard-web/model/orderModel.php';

use App\Model\CarrTempModel;
use App\Model\OrderModel;

// Soporte para peticiones JSON
if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') === 0) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (is_array($input)) {
        $_POST = $input;
    }
}

if (!isset($_SESSION['cart_token'])) {
    echo json_encode(['success' => false, 'message' => 'No hay sesión de carrito']);
    exit;
}

$cart_token = $_SESSION['cart_token'];
$cartModel = new CarrTempModel($pdo);
$orderModel = new OrderModel($pdo);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'verify_email':
            $email = $_POST['email'] ?? '';
            
            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Email requerido']);
                exit;
            }

            // Buscar cliente por email
            $stmt = $pdo->prepare("SELECT id, name, email, phone, dni, gender, birth_date FROM clients WHERE email = ? AND status = 'ACTIVE'");
            $stmt->execute([$email]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($client) {
                echo json_encode([
                    'success' => true, 
                    'client' => $client,
                    'message' => 'Cliente encontrado'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cliente no encontrado',
                    'new_client' => true
                ]);
            }
            break;

        case 'get_client_addresses':
            $clientId = $_GET['client_id'] ?? '';
            
            if (empty($clientId)) {
                echo json_encode(['success' => false, 'message' => 'ID de cliente requerido']);
                exit;
            }

            $addresses = $orderModel->getClientAddresses($clientId);
            echo json_encode([
                'success' => true,
                'addresses' => $addresses
            ]);
            break;

        case 'create_order':
            // Leer datos JSON del body
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
                exit;
            }

            // Obtener productos del carrito
            $cart_items = $cartModel->getSelectedItems($cart_token);
            if (empty($cart_items)) {
                echo json_encode(['success' => false, 'message' => 'No hay productos en el carrito']);
                exit;
            }

            // Calcular total
            $total = $cartModel->getCartTotal($cart_token);

            // Preparar items para la orden
            $orderItems = [];
            foreach ($cart_items as $item) {
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }

            if ($data['is_existing_client']) {
                // Cliente existente
                $clientId = $data['client']['id'];
                $addressId = null;

                // Manejar dirección
                if (isset($data['address']['id'])) {
                    // Usar dirección existente
                    $addressId = $data['address']['id'];
                } else {
                    // Crear nueva dirección para cliente existente
                    $stmt = $pdo->prepare("
                        INSERT INTO client_addresses (client_id, address, city, region, postal_code, phone, is_default, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, false, NOW(), NOW())
                    ");
                    $stmt->execute([
                        $clientId,
                        $data['address']['address'],
                        $data['address']['city'],
                        $data['address']['region'],
                        $data['address']['postal_code'] ?? '',
                        $data['address']['phone'] ?? ''
                    ]);
                    $addressId = $pdo->lastInsertId();
                }

                // Crear orden con cliente existente
                $orderData = [
                    'client_id' => $clientId,
                    'address_id' => $addressId,
                    'total_price' => $total,
                    'status' => 'PENDING',
                    'discount_amount' => 0,
                    'coupon_id' => null,
                    'created_by' => null, // Orden desde web
                    'items' => $orderItems,
                    'payment' => [
                        'method' => $data['payment_method'],
                        'status' => 'PENDING'
                    ]
                ];

                $orderId = $orderModel->create($orderData);
            } else {
                // Cliente nuevo
                $orderData = [
                    'client' => [
                        'name' => $data['client']['name'],
                        'email' => $data['client']['email'],
                        'phone' => $data['client']['phone'],
                        'dni' => $data['client']['dni'],
                        'gender' => $data['client']['gender'] ?? null,
                        'birth_date' => $data['client']['birth_date'] ?? null
                    ],
                    'address' => [
                        'address' => $data['address']['address'],
                        'city' => $data['address']['city'],
                        'region' => $data['address']['region'],
                        'postal_code' => $data['address']['postal_code'] ?? '',
                        'phone' => $data['address']['phone'] ?? ''
                    ],
                    'order' => [
                        'total_price' => $total,
                        'status' => 'PENDING',
                        'discount_amount' => 0,
                        'coupon_id' => null,
                        'created_by' => null // Orden desde web
                    ],
                    'items' => $orderItems,
                    'payment' => [
                        'method' => $data['payment_method'],
                        'status' => 'PENDING'
                    ]
                ];

                $orderId = $orderModel->createCompleteOrder($orderData);
            }

            // Limpiar carrito después de crear la orden
            $cartModel->clearCart($cart_token);

            echo json_encode([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'order_id' => $orderId
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
