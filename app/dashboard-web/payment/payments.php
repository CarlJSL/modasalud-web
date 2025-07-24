<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php');
    exit();
}
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/paymentModel.php';

use App\Model\PaymentModel;

$model = new PaymentModel($pdo, 'payments');

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');
    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        switch ($action) {
            case 'update_status':
                $id = (int)($_POST['id'] ?? $_GET['id']);
                $rejected = isset($_POST['rejected']) && $_POST['rejected'] === 'on';
                $verified_by = $_SESSION['usuario_id'] ?? null;
                $proof_url = null;
                
                // Manejar subida de imagen si no está rechazado
                if (!$rejected && isset($_FILES['payment_image']) && $_FILES['payment_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../uploads/payment_proofs/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $fileName = 'payment_' . $id . '_' . time() . '.' . pathinfo($_FILES['payment_image']['name'], PATHINFO_EXTENSION);
                    $uploadFile = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['payment_image']['tmp_name'], $uploadFile)) {
                        $proof_url = 'uploads/payment_proofs/' . $fileName;
                    }
                }
                
                $result = $model->verifyOtherPayment($id, $rejected, $verified_by, $proof_url);
                $msg = $rejected ? 'Pago rechazado' : 'Pago verificado y marcado como pagado';
                echo json_encode(['success' => $result, 'message' => $msg]);
                break;
            case 'add_verification_code':
                $id = (int)($_POST['id'] ?? $_GET['id']);
                $code = $_POST['verification_code'] ?? $_GET['verification_code'];
                $status = $_POST['status'] ?? $_GET['status'] ?? '';
                $finalStatus = ($status === 'FAILED') ? 'FAILED' : 'PAID';
                $verified_by = $_SESSION['usuario_id'] ?? null;
                $result = $model->verifyYapePayment($id, $code, $verified_by, $finalStatus);
                $msg = $finalStatus === 'PAID' ? 'Pago verificado y marcado como pagado' : 'Pago rechazado';
                echo json_encode(['success' => $result, 'message' => $msg]);
                break;
            case 'get':
                $id = (int)$_GET['id'];
                $payment = $model->getById($id);
                if ($payment) {
                    // Obtener información adicional de la orden
                    $orderSql = "SELECT o.*, c.name as client_name, c.email as client_email, c.phone as client_phone 
                                FROM orders o 
                                LEFT JOIN clients c ON o.client_id = c.id 
                                WHERE o.id = :order_id";
                    $orderStmt = $pdo->prepare($orderSql);
                    $orderStmt->execute([':order_id' => $payment['order_id']]);
                    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Obtener información del administrador que verificó
                    if ($payment['verified_by']) {
                        $adminSql = "SELECT name FROM users WHERE id = :user_id";
                        $adminStmt = $pdo->prepare($adminSql);
                        $adminStmt->execute([':user_id' => $payment['verified_by']]);
                        $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
                        $payment['verified_by_name'] = $admin ? $admin['name'] : 'Usuario no encontrado';
                    }
                    
                    $payment['order_info'] = $order;
                }
                echo json_encode(['success' => true, 'payment' => $payment]);
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

// Filtros
$filters = [
    'order_id' => $_GET['order_id'] ?? '',
    'status' => $_GET['status'] ?? '',
    'method' => $_GET['method'] ?? '',
    'admin_verified' => $_GET['admin_verified'] ?? '',
    'order_status' => $_GET['order_status'] ?? ''
];

// Obtener el total de pagos filtrados
// Intentar contar manualmente si no existe método count
$allPayments = $model->getAll(0, 0, $filters); // 0 para obtener todos
$total = is_array($allPayments) ? count($allPayments) : 0;
$totalPages = max(1, ceil($total / $limit));
$payments = $model->getAll($limit, $offset, $filters);
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
                            <h1 class="text-lg font-semibold text-gray-900 mb-0.5">Gestión de Pagos</h1>
                            <p class="text-xs text-gray-600">Administra y verifica pagos</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded border border-gray-200 overflow-hidden mb-3">
                    <div class="px-3 py-2 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium text-gray-900">Lista de Pagos</h3>
                            <div class="flex flex-col md:flex-row md:gap-4 gap-2 items-center">
                                <form method="GET" class="flex flex-wrap gap-2 items-center w-full">
                                    <div class="flex flex-col">
                                        <label for="order_id" class="text-xs text-gray-500 mb-0.5">ID Orden</label>
                                        <input type="text" name="order_id" id="order_id" value="<?= htmlspecialchars($_GET['order_id'] ?? '') ?>" placeholder="Buscar por ID de Orden" class="border rounded px-2 py-1 text-xs text-gray-700 w-32 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="status" class="text-xs text-gray-500 mb-0.5">Estado Pago</label>
                                        <select name="status" id="status" class="border rounded px-2 py-1 text-xs text-gray-700 w-32 bg-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition appearance-none">
                                            <option value="">Todos</option>
                                            <option value="PAID" <?= isset($_GET['status']) && $_GET['status'] === 'PAID' ? 'selected' : '' ?>>Pagado</option>
                                            <option value="PENDING" <?= isset($_GET['status']) && $_GET['status'] === 'PENDING' ? 'selected' : '' ?>>Pendiente</option>
                                            <option value="FAILED" <?= isset($_GET['status']) && $_GET['status'] === 'FAILED' ? 'selected' : '' ?>>Fallido</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="order_status" class="text-xs text-gray-500 mb-0.5">Estado Orden</label>
                                        <select name="order_status" id="order_status" class="border rounded px-2 py-1 text-xs text-gray-700 w-32 bg-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition appearance-none">
                                            <option value="">Todos</option>
                                            <option value="PENDING" <?= isset($_GET['order_status']) && $_GET['order_status'] === 'PENDING' ? 'selected' : '' ?>>Pendiente</option>
                                            <option value="COMPLETED" <?= isset($_GET['order_status']) && $_GET['order_status'] === 'COMPLETED' ? 'selected' : '' ?>>Completada</option>
                                            <option value="CANCELLED" <?= isset($_GET['order_status']) && $_GET['order_status'] === 'CANCELLED' ? 'selected' : '' ?>>Cancelada</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="method" class="text-xs text-gray-500 mb-0.5">Método</label>
                                        <select name="method" id="method" class="border rounded px-2 py-1 text-xs text-gray-700 w-32 bg-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition appearance-none">
                                            <option value="">Todos</option>
                                            <option value="YAPE" <?= isset($_GET['method']) && $_GET['method'] === 'YAPE' ? 'selected' : '' ?>>Yape</option>
                                            <option value="PLIN" <?= isset($_GET['method']) && $_GET['method'] === 'PLIN' ? 'selected' : '' ?>>Plin</option>
                                            <option value="TRANSFER" <?= isset($_GET['method']) && $_GET['method'] === 'TRANSFER' ? 'selected' : '' ?>>Transferencia</option>
                                            <option value="CASH" <?= isset($_GET['method']) && $_GET['method'] === 'CASH' ? 'selected' : '' ?>>Efectivo</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="admin_verified" class="text-xs text-gray-500 mb-0.5">Verificado</label>
                                        <select name="admin_verified" id="admin_verified" class="border rounded px-2 py-1 text-xs text-gray-700 w-32 bg-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition appearance-none">
                                            <option value="">Todos</option>
                                            <option value="1" <?= isset($_GET['admin_verified']) && $_GET['admin_verified'] === '1' ? 'selected' : '' ?>>Sí</option>
                                            <option value="0" <?= isset($_GET['admin_verified']) && $_GET['admin_verified'] === '0' ? 'selected' : '' ?>>No</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="px-3 py-1 text-xs bg-blue-500 text-white rounded shadow hover:bg-blue-600 transition">Filtrar</button>
                                    <a href="payments.php" class="px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded shadow hover:bg-gray-400 transition">Ver Todo</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php if (empty($payments)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-credit-card text-gray-300 text-2xl mb-2"></i>
                            <p class="text-gray-500 text-sm">No se encontraron pagos</p>
                            <p class="text-gray-400 text-xs mt-1">Prueba ajustando los filtros de búsqueda</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <!--<th class="px-2 py-1 text-xs text-gray-600">ID</th>-->
                                        <th class="px-2 py-1 text-xs text-gray-600">Orden / Cliente</th>
                                        <th class="px-2 py-1 text-xs text-gray-600">Total (S/)</th>
                                        <th class="px-2 py-1 text-xs text-gray-600">Estado Orden</th>
                                        <th class="px-2 py-1 text-xs text-gray-600">Fecha Orden</th>
                                        <th class="px-2 py-1 text-xs text-gray-600">Cupón</th>
                                        <th class="px-2 py-1 text-xs text-gray-600">Método</th>
                                        <th class="px-2 py-1 text-xs text-gray-600">Estado Pago</th>
                                        <th class="px-2 py-1 text-xs text-gray-600">Verificado</th>
                                        <th class="px-2 py-1 text-xs text-gray-600">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <!--<td class="px-2 py-1 text-xs text-gray-700">#<?= $payment['id'] ?></td>-->
                                            <td class="px-2 py-1 text-xs text-gray-700">
                                                <div class="flex flex-col">
                                                    <span class="font-semibold text-blue-700"><i class="fas fa-file-invoice-dollar text-blue-400 mr-1"></i>#<?= $payment['order_id'] ?></span>
                                                    <span class="text-xs text-gray-600"><i class="fas fa-user-circle text-gray-400 mr-1"></i><?= htmlspecialchars($payment['client_name'] ?? '') ?></span>
                                                </div>
                                            </td>
                                            <td class="px-2 py-1 text-xs text-gray-700">
                                                <span class="inline-block bg-yellow-100 text-yellow-800 font-semibold rounded px-2 py-0.5">S/ <?= number_format($payment['total_price'] ?? 0, 2) ?></span>
                                            </td>
                                            <td class="px-2 py-1 text-xs">
                                                <?php
                                                $status = $payment['order_status'] ?? '';
                                                $badgeColor = 'bg-gray-200 text-gray-700';
                                                if ($status === 'COMPLETED') $badgeColor = 'bg-green-100 text-green-700';
                                                elseif ($status === 'PENDING') $badgeColor = 'bg-yellow-100 text-yellow-700';
                                                elseif ($status === 'CANCELLED') $badgeColor = 'bg-red-100 text-red-700';
                                                ?>
                                                <span class="inline-block px-2 py-0.5 rounded <?= $badgeColor ?> font-semibold">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </td>
                                            <td class="px-2 py-1 text-xs text-gray-700">
                                                <?php
                                                $rawDate = $payment['order_created_at'] ?? '';
                                                $dateObj = null;
                                                if ($rawDate) {
                                                    // Elimina la zona horaria si existe
                                                    $rawDate = preg_replace('/\+.*/', '', $rawDate);
                                                    $dateObj = date_create($rawDate);
                                                }
                                                ?>
                                                <?php if ($dateObj): ?>
                                                    <span class="inline-block bg-blue-50 text-blue-800 rounded px-2 py-0.5 mr-1">
                                                        <?= date_format($dateObj, 'd/m/Y') ?>
                                                    </span>
                                                    <span class="inline-block bg-gray-100 text-gray-700 rounded px-2 py-0.5">
                                                        <?= date_format($dateObj, 'H:i:s') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-2 py-1 text-xs text-gray-700"><?= htmlspecialchars($payment['coupon_code'] ?? '-') ?></td>
                                            <td class="px-2 py-1 text-xs text-gray-700">
                                                <?php
                                                $methodMap = [
                                                    'YAPE' => ['Yape', 'fas fa-mobile-alt'],
                                                    'PLIN' => ['Plin', 'fas fa-mobile-alt'],
                                                    'TRANSFER' => ['Transferencia', 'fas fa-university'],
                                                    'CASH' => ['Efectivo', 'fas fa-money-bill-wave']
                                                ];
                                                $m = $methodMap[$payment['method']] ?? [htmlspecialchars($payment['method']), 'fas fa-question'];
                                                ?>
                                                <span class="inline-flex items-center">
                                                    <i class="<?= $m[1] ?> mr-1 text-gray-400"></i><?= $m[0] ?>
                                                </span>
                                            </td>
                                            <td class="px-2 py-1 text-xs">
                                                <?php
                                                $statusMap = [
                                                    'PAID' => ['Pagado', 'bg-green-100 text-green-700', 'fas fa-check-circle'],
                                                    'PENDING' => ['Pendiente', 'bg-yellow-100 text-yellow-700', 'fas fa-clock'],
                                                    'FAILED' => ['Fallido', 'bg-red-100 text-red-700', 'fas fa-times-circle']
                                                ];
                                                $s = $statusMap[$payment['status']] ?? [htmlspecialchars($payment['status']), 'bg-gray-100 text-gray-700', 'fas fa-question'];
                                                ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded <?= $s[1] ?> font-semibold">
                                                    <i class="<?= $s[2] ?> mr-1"></i><?= $s[0] ?>
                                                </span>
                                            </td>

                                            <td class="px-2 py-1 text-xs text-gray-700">
                                                <?php if ($payment['admin_verified']): ?>
                                                    <div class="flex flex-col text-center">
                                                        <span class="text-green-600 font-bold"><i class="fas fa-lock mr-1"></i>Sí</span>
                                                        <?php if ($payment['method'] === 'YAPE' && !empty($payment['verification_code'])): ?>
                                                            <span class="text-xs text-gray-600">
                                                                Código de seguridad: <?= htmlspecialchars($payment['verification_code']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>

                                                <?php else: ?><div class="text-center"><span class="text-red-600 font-bold">No</span></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-2 py-1 text-xs text-gray-700">
                                                <?php if (($payment['order_status'] ?? '') === 'PENDING' && ($payment['status'] ?? '') === 'PENDING'): ?>
                                                    <?php if ($payment['method'] === 'YAPE'): ?>
                                                        <button onclick="triggerVerificationModal(<?= $payment['id'] ?>)" class="px-2 py-1 text-xs bg-green-500 text-white rounded">Verificar</button>
                                                    <?php else: ?>
                                                        <button onclick="triggerOtherPaymentModal(<?= $payment['id'] ?>, '<?= $payment['status'] ?>')" class="px-2 py-1 text-xs bg-blue-500 text-white rounded">Verificar / Subir Imagen</button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <button onclick="viewPaymentDetails(<?= $payment['id'] ?>)" title="Ver detalles" class="px-2 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                                                        <i class="fas fa-eye text-xs"></i> Ver
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <!-- Paginación -->
                    <div class="flex justify-end items-center gap-2 px-3 py-2 bg-gray-50 border-t border-gray-200">
                        <?php
                        $prevPage = $page > 1 ? $page - 1 : 1;
                        $nextPage = $page < $totalPages ? $page + 1 : $totalPages;
                        $queryString = $_GET;
                        ?>
                        <a href="?<?= http_build_query(array_merge($queryString, ['page' => $prevPage])) ?>" class="px-2 py-1 text-xs bg-gray-200 rounded <?= $page == 1 ? 'pointer-events-none opacity-50' : 'hover:bg-gray-300' ?>">Anterior</a>
                        <span class="text-xs text-gray-700">Página <?= $page ?> de <?= $totalPages ?></span>
                        <a href="?<?= http_build_query(array_merge($queryString, ['page' => $nextPage])) ?>" class="px-2 py-1 text-xs bg-gray-200 rounded <?= $page == $totalPages ? 'pointer-events-none opacity-50' : 'hover:bg-gray-300' ?>">Siguiente</a>
                    </div>
                </div>
                <!-- Modales -->
                <?php include_once 'modales.php'; ?>
            </main>
        </div>
    </div>
    <script src="../js/components.js"></script>
    <?php include_once 'modales.php'; ?>
    <script>
    function triggerOtherPaymentModal(paymentId) {
        document.getElementById('paymentIdInput').value = paymentId;
        document.getElementById('otherPaymentRejected').checked = false;
        document.getElementById('otherPaymentImage').value = '';
        document.getElementById('otherPaymentImage').parentElement.style.display = '';
        document.getElementById('paymentStatusModal').classList.remove('hidden');
        document.getElementById('paymentStatusModal').classList.add('flex');
    }

    function triggerVerificationModal(paymentId) {
        document.getElementById('verificationPaymentIdInput').value = paymentId;
        document.getElementById('verificationCodeInput').value = '';
        document.getElementById('yapeRejectedCheckbox').checked = false;
        document.getElementById('yapeVerificationInputDiv').style.display = '';
        document.getElementById('verificationModal').classList.remove('hidden');
        document.getElementById('verificationModal').classList.add('flex');
    }

    function viewPaymentDetails(paymentId) {
        fetch(`payments.php?action=get&id=${paymentId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openPaymentDetailsModal(data.payment);
            } else {
                alert('Error al cargar los detalles del pago');
            }
        });
    }
    </script>
</body>

</html>