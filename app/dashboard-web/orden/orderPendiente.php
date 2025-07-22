<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php');
    exit();
}
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/orderModel.php';

use App\Model\OrderModel;

$model = new OrderModel($pdo, 'orders');

// Filtros para solo órdenes pendientes
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$filters = [
    'status' => 'PENDING',
    'payment_status' => ['PAID', 'FAILED'],
];
$total = $model->count($search, $filters);
$orders = $model->getAll($limit, $offset, $search, $filters);
$totalPages = ceil($total / $limit);

// Función para enviar correo (aceptar/cancelar)
function sendOrderStatusMail($orderId, $status, $tracking = '', $reason = '')
{
    $clave = $_POST['clave'] ?? '';
    $empresa = $_POST['empresa'] ?? '';
    $link = $_POST['link'] ?? '';
    require_once __DIR__ . '/../pdf/order_pdf_correo.php';
    if (function_exists('generateAndSendOrderPdf')) {
        generateAndSendOrderPdf($GLOBALS['pdo'], $orderId, $status, $tracking, $reason, $clave, $empresa, $link);
    }
}

// Manejar acción de cambio de estado y envío de correo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update_status') {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $extraData = isset($_POST['extraData']) ? json_decode($_POST['extraData'], true) : [];
    $tracking = $extraData['tracking'] ?? '';
    $reason = $extraData['motivo'] ?? '';
    $clave = $extraData['clave'] ?? '';
    $empresa = $extraData['empresa'] ?? '';
    $link = $extraData['link'] ?? '';
    $model->updateStatus($id, $status, $extraData);
    echo json_encode(['success' => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<?php include_once './../includes/head.php'; ?>

<body>
    <div class="flex h-screen">
        <div class="fixed inset-y-0 left-0 z-50">
            <?php include_once './../includes/navbar.php'; ?>
        </div>
        <div class="flex-1 ml-64 flex flex-col min-h-screen">
            <div class="sticky top-0 z-40">
                <?php include_once './../includes/header.php'; ?>
            </div>
            <main class="flex-1 p-2 bg-gray-50 overflow-y-auto">
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 mb-0.5">Órdenes Pendientes</h1>
                            <p class="text-xs text-gray-600">Gestiona y notifica el estado de las órdenes pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded border border-gray-200 overflow-hidden mb-3">
                    <div class="px-3 py-2 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-sm font-medium text-gray-900">Lista de Órdenes Pendientes</h3>
                    </div>
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-cart text-gray-300 text-2xl mb-2"></i>
                            <p class="text-gray-500 text-sm">No hay órdenes pendientes</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 text-sm text-gray-900">#<?= $order['id'] ?></td>
                                            <td class="px-3 py-2 text-sm text-gray-900">
                                                <div>
                                                    <p class="font-medium"><?= htmlspecialchars($order['client_name']) ?></p>
                                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($order['client_email']) ?></p>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-900">S/ <?= number_format($order['total_price'], 2) ?></td>
                                            <td class="px-3 py-2 text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                            <td class="px-3 py-2 text-sm">
                                                <?php if ($order['payment_status'] === 'PAID'): ?>
                                                    <button
                                                        onclick="openOrderStatusModal(<?= $order['id'] ?>, 'COMPLETED')"
                                                        class="text-green-600 hover:text-green-900 text-xs p-1"
                                                        title="Marcar como completada">
                                                        <i class="fas fa-check"></i> Aceptar y Notificar
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($order['payment_status'] === 'FAILED'): ?>
                                                    <button
                                                        onclick="openOrderStatusModal(<?= $order['id'] ?>, 'CANCELLED')"
                                                        class="text-red-600 hover:text-red-900 text-xs p-1"
                                                        title="Cancelar orden">
                                                        <i class="fas fa-times"></i> Rechazar y Notificar
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <?php include_once 'modales.php'; ?>
    </script>
</body>

</html>