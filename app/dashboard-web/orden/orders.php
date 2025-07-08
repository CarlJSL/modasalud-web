<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php'); // o ../index.php dependiendo del nivel de carpeta
    exit();
}
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/orderModel.php';

use App\Model\OrderModel;

// Instancia del modelo apuntando a la tabla 'orders'
$model = new OrderModel($pdo, 'orders');

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'create':
                // Validar que se enviaron datos requeridos para evitar warnings
                if (empty($_POST['is_new_client']) && empty($_POST['client_id']) && empty($_POST['items'])) {
                    echo json_encode(['success' => false, 'message' => 'Datos incompletos para crear orden']);
                    exit;
                }
                
                if ($_POST['is_new_client'] == '1') {
                    $data = [
                        'client' => [
                            'name' => $_POST['new_client_name'] ?? '',
                            'email' => $_POST['new_client_email'] ?? '',
                            'phone' => $_POST['new_client_phone'] ?? '',
                            'dni' => $_POST['search_dni'] ?? '',
                            'gender' => $_POST['new_client_gender'] ?? '',
                            'birth_date' => $_POST['new_client_birth'] ?? null
                        ],
                        'address' => [
                            'address' => $_POST['new_address'] ?? '',
                            'city' => $_POST['new_city'] ?? '',
                            'region' => $_POST['new_region'] ?? '',
                            'postal_code' => $_POST['new_postal_code'] ?? '',
                            'phone' => $_POST['new_delivery_phone'] ?? ''
                        ],
                        'order' => [
                            'total_price' => (float)$_POST['total_price'],
                            'status' => $_POST['status'] ?? 'PENDING',
                            'discount_amount' => (float)($_POST['discount_amount'] ?? 0),
                            'coupon_id' => !empty($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : null,
                            'created_by' => $_SESSION['usuario_id']
                        ],
                        'items' => json_decode($_POST['items'], true) ?? [],
                        'payment' => [
                            'method' => $_POST['payment_method'] ?? 'CASH',
                            'status' => $_POST['payment_status'] ?? 'PENDING',
                            'paid_at' => !empty($_POST['paid_at']) ? $_POST['paid_at'] : null,
                            'proof_url' => $_POST['proof_url'] ?? null
                        ]
                    ];

                    // Validaciones
                    $errors = [];

                    if (empty($data['client']['name'])) $errors['client_name'] = 'Nombre requerido';
                    if (empty($data['client']['email'])) $errors['client_email'] = 'Correo requerido';
                    if (empty($data['client']['dni'])) $errors['client_dni'] = 'DNI requerido';
                    if (empty($data['address']['address'])) $errors['address'] = 'Dirección requerida';
                    if (empty($data['address']['city'])) $errors['city'] = 'Ciudad requerida';
                    if (empty($data['address']['region'])) $errors['region'] = 'Región requerida';
                    if (empty($data['order']['total_price']) || $data['order']['total_price'] <= 0) $errors['total_price'] = 'El total debe ser mayor a 0';
                    if (empty($data['items'])) $errors['items'] = 'Debe agregar al menos un producto';

                    if (!empty($errors)) {
                        echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                        exit;
                    }

                    try {
                        $result = $model->createCompleteOrder($data);
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Orden creada exitosamente', 
                            'order_id' => $result,
                            'pdf_url' => '../pdf/orden_pdf.php?id=' . $result
                        ]);
                    } catch (Exception $e) {
                        error_log('Error al crear orden: ' . $e->getMessage());
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Error al crear orden: ' . $e->getMessage(), 
                            'error' => $e->getMessage(),
                            'debug_data' => $data // Para debugging
                        ]);
                    }
                    break;
                } else {
                    $data = [
                        'client_id' => (int)$_POST['client_id'],
                        'address_id' => (int)$_POST['address_id'],
                        'total_price' => (float)$_POST['total_price'],
                        'status' => $_POST['status'] ?? 'PENDING',
                        'discount_amount' => (float)($_POST['discount_amount'] ?? 0),
                        'coupon_id' => !empty($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : null,
                        'created_by' => $_SESSION['usuario_id'],
                        'items' => json_decode($_POST['items'], true) ?? [],
                        'payment' => [
                            'method' => $_POST['payment_method'] ?? 'CASH',
                            'status' => $_POST['payment_status'] ?? 'PENDING',
                            'paid_at' => !empty($_POST['paid_at']) ? $_POST['paid_at'] : null,
                            'proof_url' => $_POST['proof_url'] ?? null
                        ]
                    ];

                    $errors = [];
                    if (empty($data['client_id'])) $errors['client_id'] = 'El cliente es requerido';
                    if (empty($data['address_id'])) $errors['address_id'] = 'La dirección de entrega es requerida';
                    if (empty($data['total_price']) || $data['total_price'] <= 0) $errors['total_price'] = 'El total debe ser mayor a 0';
                    if (empty($data['items'])) $errors['items'] = 'Debe agregar al menos un producto';

                    if (!empty($errors)) {
                        echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                        exit;
                    }

                    $result = $model->create($data);
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Orden creada exitosamente', 
                        'order_id' => $result,
                        'pdf_url' => '../pdf/orden_pdf.php?id=' . $result
                    ]);
                    break;
                }
                break;
            case 'update':
                $id = (int)$_POST['id'];
                $data = [
                    'status' => $_POST['status'],
                    'total_price' => (float)$_POST['total_price'],
                    'discount_amount' => (float)($_POST['discount_amount'] ?? 0),
                    'coupon_id' => !empty($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : null
                ];

                // Agregar información de pago si se proporciona
                if (!empty($_POST['payment_method'])) {
                    $data['payment'] = [
                        'method' => $_POST['payment_method'],
                        'status' => $_POST['payment_status'] ?? 'PENDING',
                        'paid_at' => !empty($_POST['paid_at']) ? $_POST['paid_at'] : null,
                        'proof_url' => $_POST['proof_url'] ?? null
                    ];
                }

                // Validaciones
                $errors = [];
                if (empty($data['total_price']) || $data['total_price'] <= 0) $errors['total_price'] = 'El total debe ser mayor a 0';

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                // Verificar si hay cambios reales comparando con los datos actuales
                $currentOrder = $model->getById($id);
                if (!$currentOrder) {
                    echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
                    exit;
                }

                $result = $model->update($id, $data);
                echo json_encode(['success' => true, 'message' => 'Orden actualizada exitosamente']);
                break;

            case 'update_status':
                $id = (int)($_POST['id'] ?? $_GET['id']);
                $status = $_POST['status'] ?? $_GET['status'];

                $result = $model->updateStatus($id, $status);
                $statusNames = [
                    'PENDING' => 'Pendiente',
                    'COMPLETED' => 'Completada',
                    'CANCELLED' => 'Cancelada'
                ];
                $message = 'Estado de orden actualizado a: ' . ($statusNames[$status] ?? $status);

                echo json_encode(['success' => true, 'message' => $message]);
                break;

            case 'get':
                $id = (int)$_GET['id'];
                $order = $model->getById($id);
                if ($order) {
                    echo json_encode(['success' => true, 'order' => $order]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
                }
                break;

            case 'details':
                $id = (int)$_GET['id'];
                $order = $model->getDetailedById($id);
                if ($order) {
                    echo json_encode(['success' => true, 'order' => $order]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
                }
                break;

            case 'get_clients':
                $clients = $model->getClients();
                echo json_encode(['success' => true, 'clients' => $clients]);
                break;

            case 'get_client_addresses':
                $clientId = (int)$_GET['client_id'];
                $addresses = $model->getClientAddresses($clientId);
                echo json_encode(['success' => true, 'addresses' => $addresses]);
                break;

            case 'get_products':
                $products = $model->getProducts();
                echo json_encode(['success' => true, 'products' => $products]);
                break;

            case 'get_coupons':
                $coupons = $model->getCoupons();
                echo json_encode(['success' => true, 'coupons' => $coupons]);
                break;
            case 'search_client_by_dni':
                $dni = $_GET['dni'] ?? '';

                if (empty($dni)) {
                    echo json_encode(['success' => false, 'message' => 'DNI no proporcionado']);
                    exit;
                }

                // Buscar cliente por DNI
                $stmt = $pdo->prepare("SELECT id, name, email FROM clients WHERE dni = ?");
                $stmt->execute([$dni]);
                $client = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($client) {
                    echo json_encode(['success' => true, 'client' => $client]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
                }
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

    exit;
}

// Parámetros de búsqueda y paginación
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';

// Filtros
$filters = [
    'status' => $_GET['status'] ?? '',
    'payment_method' => $_GET['payment_method'] ?? '',
    'payment_status' => $_GET['payment_status'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'price_min' => $_GET['price_min'] ?? '',
    'price_max' => $_GET['price_max'] ?? ''
];

// Obtener datos para filtros
$orderStatuses = $model->getOrderStatuses();
$paymentMethods = $model->getPaymentMethods();
$paymentStatuses = $model->getPaymentStatuses();

// Obtener total de resultados y datos de órdenes
$total = $model->count($search, $filters);
$orders = $model->getAll($limit, $offset, $search, $filters);

// Contar órdenes pendientes con los mismos filtros y búsqueda
$pendingFilters = $filters;
$pendingFilters['status'] = 'PENDING';
$pendingOrders = $model->count($search, $pendingFilters);

$totalPages = ceil($total / $limit);

// Obtener estadísticas generales
$stats = $model->getGeneralStats();
?>

<!DOCTYPE html>
<html lang="es">

<?php
include_once './../includes/head.php';
?>

<body>
    <!-- Contenedor principal con navbar fijo y contenido con scroll -->
    <div class="flex h-screen">
        <!-- Incluir navegación lateral fija -->
        <div class="fixed inset-y-0 left-0 z-50">
            <?php include_once './../includes/navbar.php'; ?>
        </div>

        <!-- Contenedor principal del contenido con margen para el navbar -->
        <div class="flex-1 ml-64 flex flex-col min-h-screen">
            <!-- Incluir header superior fijo -->
            <div class="sticky top-0 z-40">
                <?php include_once './../includes/header.php'; ?>
            </div>

            <!-- Contenido principal dentro del Main con scroll -->
            <main class="flex-1 p-2 bg-gray-50 overflow-y-auto">
                <!-- Header de la página de órdenes con breadcrumb -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <!-- Título y descripción de la página -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 mb-0.5">Gestión de Ventas</h1>
                            <p class="text-xs text-gray-600">Administra, Visualiza y Realiza Ventas</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de búsqueda y filtros compacto -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <form method="get" class="space-y-2">
                        <!-- Búsqueda principal -->
                        <div class="flex flex-col md:flex-row md:items-end space-y-2 md:space-y-0 md:space-x-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Buscar órdenes</label>
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                    placeholder="Buscar por ID, cliente o email..."
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div class="flex flex-row space-x-1">
                                <button type="submit"
                                    class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search mr-1"></i>Buscar
                                </button>
                                <button type="button" id="toggleFilters"
                                    class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-filter mr-1"></i>Filtros
                                </button>
                                <button type="button" onclick="openCreateModal()"
                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <i class="fas fa-plus mr-1"></i>Nueva Orden
                                </button>
                            </div>
                        </div>

                        <!-- Panel de filtros (inicialmente oculto) -->
                        <div id="filtersPanel" class="hidden border-t border-gray-200 pt-2 mt-2">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                                    <select name="status" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <?php foreach ($orderStatuses as $key => $label): ?>
                                            <option value="<?= $key ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Método de Pago</label>
                                    <select name="payment_method" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <?php foreach ($paymentMethods as $key => $label): ?>
                                            <option value="<?= $key ?>" <?= $filters['payment_method'] === $key ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Estado de Pago</label>
                                    <select name="payment_status" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <?php foreach ($paymentStatuses as $key => $label): ?>
                                            <option value="<?= $key ?>" <?= $filters['payment_status'] === $key ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Fecha desde</label>
                                    <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Fecha hasta</label>
                                    <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Precio mínimo</label>
                                    <input type="number" name="price_min" value="<?= htmlspecialchars($filters['price_min']) ?>"
                                        step="0.01" placeholder="0.00"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Precio máximo</label>
                                    <input type="number" name="price_max" value="<?= htmlspecialchars($filters['price_max']) ?>"
                                        step="0.01" placeholder="0.00"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit"
                                        class="w-full px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        Aplicar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Estadísticas compactas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2">
                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-blue-400 to-blue-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-shopping-cart text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Total Órdenes</p>
                                <p class="text-sm font-semibold text-gray-900"><?= number_format($stats['total_orders']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-clock text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Pendientes</p>
                                <p class="text-sm font-semibold text-gray-900"><?= number_format($stats['pending_orders']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-green-400 to-green-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-check-circle text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Completadas</p>
                                <p class="text-sm font-semibold text-gray-900"><?= number_format($stats['completed_orders']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-purple-400 to-purple-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-dollar-sign text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Ventas Totales</p>
                                <p class="text-sm font-semibold text-gray-900">S/ <?= number_format($stats['total_sales'], 2) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de órdenes compacta -->
                <div class="bg-white rounded border border-gray-200 overflow-hidden mb-3">
                    <div class="px-3 py-2 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium text-gray-900">
                                Lista de Órdenes
                                <?php if ($search || array_filter($filters)): ?>
                                    <span class="text-xs text-gray-500">(<?= $total ?> resultados)</span>
                                <?php endif; ?>
                            </h3>
                            <div class="text-xs text-gray-500">
                                Mostrando <?= $offset + 1 ?> a <?= min($offset + $limit, $total) ?> de <?= $total ?> órdenes
                            </div>
                        </div>
                    </div>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-cart text-gray-300 text-2xl mb-2"></i>
                            <p class="text-gray-500 text-sm">No se encontraron órdenes</p>
                            <p class="text-gray-400 text-xs mt-1">Prueba ajustando los filtros de búsqueda</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pago</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado por</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 text-sm text-gray-900">
                                                #<?= $order['id'] ?>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900">
                                                <div>
                                                    <p class="font-medium"><?= htmlspecialchars($order['client_name']) ?></p>
                                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($order['client_email']) ?></p>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900">
                                                <div>
                                                    <p class="font-medium">S/ <?= number_format($order['total_price'], 2) ?></p>
                                                    <p class="text-xs text-gray-500"><?= $order['total_quantity'] ?> items</p>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm">
                                                <?php
                                                $statusColors = [
                                                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                                                    'COMPLETED' => 'bg-green-100 text-green-800',
                                                    'CANCELLED' => 'bg-red-100 text-red-800'
                                                ];
                                                ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                                    <?= $orderStatuses[$order['status']] ?? $order['status'] ?>
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-sm">
                                                <?php
                                                $paymentStatusColors = [
                                                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                                                    'PAID' => 'bg-green-100 text-green-800',
                                                    'FAILED' => 'bg-red-100 text-red-800'
                                                ];
                                                ?>
                                                <div>
                                                    <p class="font-medium"><?= $paymentMethods[$order['payment_method']] ?? 'N/A' ?></p>
                                                    <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium <?= $paymentStatusColors[$order['payment_status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                                        <?= $paymentStatuses[$order['payment_status']] ?? $order['payment_status'] ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900">
                                                <div>
                                                    <?php if (!empty($order['created_by_username'])): ?>
                                                        <p class="font-medium text-gray-900"><?= htmlspecialchars($order['created_by_username']) ?></p>
                                                        <?php if (!empty($order['created_by_email'])): ?>
                                                            <p class="text-xs text-gray-500"><?= htmlspecialchars($order['created_by_email']) ?></p>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <p class="text-xs text-gray-400 italic">Sistema</p>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900">
                                                <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                            </td>
                                            <td class="px-3 py-2 text-sm">
                                                <div class="flex space-x-1">
                                                    <button onclick="openDetailModal(<?= $order['id'] ?>)"
                                                        class="text-blue-600 hover:text-blue-900 text-xs p-1"
                                                        title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <button onclick="window.open('../pdf/orden_pdf.php?id=<?= $order['id'] ?>', '_blank')"
                                                        class="text-purple-600 hover:text-purple-900 text-xs p-1"
                                                        title="Ver PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </button>
                                                    
                                                    <?php if ($order['status'] != 'COMPLETED' && $order['status'] != 'CANCELLED'): ?>
                                                        <button onclick="openEditModal(<?= $order['id'] ?>)"
                                                            class="text-green-600 hover:text-green-900 text-xs p-1"
                                                            title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    <?php endif; ?>


                                                    <?php if ($order['status'] === 'PENDING'): ?>
                                                        <button
                                                            onclick="openOrderStatusModal(<?= $order['id'] ?>, 'COMPLETED')"
                                                            class="text-green-600 hover:text-green-900 text-xs p-1"
                                                            title="Marcar como completada">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button
                                                            onclick="openOrderStatusModal(<?= $order['id'] ?>, 'CANCELLED')"
                                                            class="text-red-600 hover:text-red-900 text-xs p-1"
                                                            title="Cancelar orden">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Paginación compacta -->
                <?php if ($totalPages > 1): ?>
                    <?php
                    // Construir parámetros para la paginación
                    $queryParams = [];
                    if ($search) $queryParams['search'] = $search;
                    if ($filters['status']) $queryParams['status'] = $filters['status'];
                    if ($filters['payment_method']) $queryParams['payment_method'] = $filters['payment_method'];
                    if ($filters['payment_status']) $queryParams['payment_status'] = $filters['payment_status'];
                    if ($filters['date_from']) $queryParams['date_from'] = $filters['date_from'];
                    if ($filters['date_to']) $queryParams['date_to'] = $filters['date_to'];

                    function buildUrl($queryParams, $page)
                    {
                        $params = $queryParams;
                        $params['page'] = $page;
                        return '?' . http_build_query($params);
                    }
                    ?>
                    <div class="bg-white rounded border border-gray-200 px-3 py-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <?php if ($page > 1): ?>
                                    <a href="<?= buildUrl($queryParams, $page - 1) ?>"
                                        class="px-2 py-1 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <span class="text-xs text-gray-700">
                                    Página <?= $page ?> de <?= $totalPages ?>
                                </span>

                                <?php if ($page < $totalPages): ?>
                                    <a href="<?= buildUrl($queryParams, $page + 1) ?>"
                                        class="px-2 py-1 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded hover:bg-gray-50">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="text-xs text-gray-500">
                                <?= $total ?> órdenes en total
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Footer incluido al final -->
                <div class="mt-4">
                    <?php include_once './../includes/footer.php'; ?>
                </div>
            </main>

        </div>
    </div>

    <!-- Incluir modales reutilizables -->
    <?php include_once 'modales.php'; ?>

    <!-- Incluir componentes JavaScript -->
    <script src="../js/components.js"></script>
    <script>
        // Inicializar componentes específicos de esta página
        document.addEventListener('DOMContentLoaded', function() {
            initializeComponents({
                hasActiveFilters: <?= json_encode(array_filter($filters) ? true : false) ?>
            });
        });

        // Función para actualizar estado de orden
        function updateOrderStatus(orderId, status) {
            if (confirm(`¿Está seguro de cambiar el estado de la orden #${orderId}?`)) {
                fetch('orders.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=update_status&id=${orderId}&status=${status}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al actualizar el estado');
                    });
            }
        }
    </script>

</body>

</html>