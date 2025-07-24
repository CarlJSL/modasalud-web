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
            // Manejar tanto FormData como JSON
            if (isset($_POST['action'])) {
                // Datos vienen de FormData (con archivos)
                $client = is_string($_POST['client']) ? json_decode($_POST['client'], true) : $_POST['client'];
                $address = is_string($_POST['address']) ? json_decode($_POST['address'], true) : $_POST['address'];
                $isExistingClient = filter_var($_POST['is_existing_client'], FILTER_VALIDATE_BOOLEAN);
                $paymentMethod = $_POST['payment_method'] ?? 'CASH';
            } else {
                // Datos vienen de JSON (sin archivos)
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                
                if (!$data) {
                    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
                    exit;
                }
                
                $client = $data['client'];
                $address = $data['address'];
                $isExistingClient = $data['is_existing_client'];
                $paymentMethod = $data['payment_method'] ?? 'CASH';
            }

            // Obtener productos del carrito
            $cart_items = $cartModel->getSelectedItems($cart_token);
            if (empty($cart_items)) {
                echo json_encode(['success' => false, 'message' => 'No hay productos en el carrito']);
                exit;
            }

            // Calcular total
            $total = $cartModel->getCartTotal($cart_token);

            // Manejar subida de comprobante de pago si es necesario
            $proof_url = null;
            if (in_array($paymentMethod, ['PLIN', 'TRANSFER']) && isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/payment_proofs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = 'client_payment_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
                $uploadFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $uploadFile)) {
                    $proof_url = 'uploads/payment_proofs/' . $fileName;
                }
            }

            // Preparar items para la orden
            $orderItems = [];
            foreach ($cart_items as $item) {
                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }

            // Preparar datos de pago
            $paymentData = [
                'method' => $paymentMethod,
                'status' => 'PENDING',
                'proof_url' => $proof_url,
                'verification_code' => ($paymentMethod === 'YAPE') ? ($_POST['verification_code'] ?? null) : null
            ];

            if ($isExistingClient) {
                // Cliente existente
                $clientId = $client['id'];
                $addressId = null;

                // Manejar dirección
                if (isset($address['id'])) {
                    // Usar dirección existente
                    $addressId = $address['id'];
                } else {
                    // Crear nueva dirección para cliente existente
                    $stmt = $pdo->prepare("
                        INSERT INTO client_addresses (client_id, address, city, region, postal_code, phone, is_default, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, false, NOW(), NOW())
                    ");
                    $stmt->execute([
                        $clientId,
                        $address['address'],
                        $address['city'],
                        $address['region'],
                        $address['postal_code'] ?? '',
                        $address['phone'] ?? ''
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
                    'order_source' => 'WEB', // Indicar que es desde web
                    'items' => $orderItems,
                    'payment' => $paymentData
                ];

                $orderId = $orderModel->create($orderData);
            } else {
                // Cliente nuevo
                $orderData = [
                    'client' => [
                        'name' => $client['name'],
                        'email' => $client['email'],
                        'phone' => $client['phone'],
                        'dni' => $client['dni'],
                        'gender' => $client['gender'] ?? null,
                        'birth_date' => $client['birth_date'] ?? null
                    ],
                    'address' => [
                        'address' => $address['address'],
                        'city' => $address['city'],
                        'region' => $address['region'],
                        'postal_code' => $address['postal_code'] ?? '',
                        'phone' => $address['phone'] ?? ''
                    ],
                    'order' => [
                        'total_price' => $total,
                        'status' => 'PENDING',
                        'discount_amount' => 0,
                        'coupon_id' => null,
                        'created_by' => null, // Orden desde web
                        'order_source' => 'WEB' // Indicar que es desde web
                    ],
                    'items' => $orderItems,
                    'payment' => $paymentData
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
