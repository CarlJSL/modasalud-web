<?php

session_start();

// Control de acceso basado en roles y permisos
require_once __DIR__ . '/../includes/access_control.php';

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
                <!-- Header de la página con estadísticas -->
                <div class="bg-white rounded border border-gray-200 p-4 mb-4">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900 mb-1">Órdenes Pendientes</h1>
                            <p class="text-sm text-gray-600">Gestiona y notifica el estado de las órdenes pendientes</p>
                        </div>
                        <div class="flex space-x-2">
                            <div class="bg-yellow-50 px-4 py-2 rounded-lg border border-yellow-200">
                                <p class="text-xs text-yellow-600 font-medium">Pendientes de Revisión</p>
                                <p class="text-lg font-semibold text-yellow-700"><?= number_format($total) ?></p>
                            </div>
                            <div class="bg-green-50 px-4 py-2 rounded-lg border border-green-200">
                                <p class="text-xs text-green-600 font-medium">Pagos Confirmados</p>
                                <p class="text-lg font-semibold text-green-700"><?= $model->countByPaymentStatus('PAID', 'PENDING') ?></p>
                            </div>
                            <div class="bg-red-50 px-4 py-2 rounded-lg border border-red-200">
                                <p class="text-xs text-red-600 font-medium">Pagos Fallidos</p>
                                <p class="text-lg font-semibold text-red-700"><?= $model->countByPaymentStatus('FAILED', 'PENDING') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulario de búsqueda -->
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <form method="get" class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="<?= htmlspecialchars($search) ?>" 
                                       placeholder="Buscar por ID, nombre o email del cliente..." 
                                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <button type="submit" class="absolute right-3 top-2 text-blue-600 hover:text-blue-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <?php if ($search): ?>
                            <a href="?" class="text-sm text-gray-600 hover:text-gray-900 flex items-center space-x-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>Limpiar búsqueda</span>
                            </a>
                        <?php endif; ?>
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
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            <div class="flex items-center space-x-1">
                                                <span>ID</span>
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cliente
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($orders as $order): ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">#<?= $order['id'] ?></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8 bg-gray-200 rounded-full flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-600">
                                                            <?= strtoupper(substr($order['client_name'], 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['client_name']) ?></div>
                                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($order['client_email']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">S/ <?= number_format($order['total_price'], 2) ?></div>
                                                <div class="text-xs text-gray-500">
                                                    <?= $order['total_items'] ?? 0 ?> productos
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                                <div class="text-xs text-gray-500"><?= date('H:i', strtotime($order['created_at'])) ?> hrs</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center space-x-3 justify-end">
                                                    <?php if ($order['payment_status'] === 'PAID'): ?>
                                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Pagado</span>
                                                        <button
                                                            onclick="openOrderStatusModal(<?= $order['id'] ?>, 'COMPLETED')"
                                                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Aceptar
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($order['payment_status'] === 'FAILED'): ?>
                                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Fallido</span>
                                                        <button
                                                            onclick="openOrderStatusModal(<?= $order['id'] ?>, 'CANCELLED')"
                                                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                            Rechazar
                                                        </button>
                                                    <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginación -->
                        <?php if ($totalPages > 1): ?>
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Anterior
                                    </a>
                                <?php endif; ?>
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Siguiente
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Mostrando
                                        <span class="font-medium"><?= ($offset + 1) ?></span>
                                        a
                                        <span class="font-medium"><?= min($offset + $limit, $total) ?></span>
                                        de
                                        <span class="font-medium"><?= $total ?></span>
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <?php if ($page > 1): ?>
                                            <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Anterior</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php
                                        $startPage = max(1, min($page - 2, $totalPages - 4));
                                        $endPage = min($totalPages, max($page + 2, 5));
                                        
                                        for ($i = $startPage; $i <= $endPage; $i++):
                                        ?>
                                            <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?= $i === $page ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' ?>">
                                                <?= $i ?>
                                            </a>
                                        <?php endfor; ?>

                                        <?php if ($page < $totalPages): ?>
                                            <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Siguiente</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <?php include_once 'modales.php'; ?>
    </script>
</body>

</html>