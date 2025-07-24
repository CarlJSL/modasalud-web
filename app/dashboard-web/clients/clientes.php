<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php');
    exit();
}

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/clienteModel.php';

use App\Model\ClienteModel;

// Instancia del modelo
$model = new ClienteModel($pdo, 'clients');

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'create':
                $data = [
                    'name' => trim($_POST['name']),
                    'email' => trim($_POST['email']),
                    'phone' => trim($_POST['phone']) ?: null,
                    'dni' => trim($_POST['dni']) ?: null,
                    'gender' => $_POST['gender'] ?: null,
                    'birth_date' => $_POST['birth_date'] ?: null,
                    'status' => $_POST['status'] ?? 'ACTIVE',
                    'address' => trim($_POST['address']) ?: null,
                    'city' => trim($_POST['city']) ?: null,
                    'region' => trim($_POST['region']) ?: null,
                    'postal_code' => trim($_POST['postal_code']) ?: null,
                    'address_phone' => trim($_POST['address_phone']) ?: null
                ];

                // Validaciones
                $errors = [];
                if (empty($data['name'])) $errors['name'] = 'El nombre es requerido';
                if (empty($data['email'])) $errors['email'] = 'El email es requerido';

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'El email no tiene un formato válido';
                }

                // Verificar si el email ya existe
                if ($model->findByEmail($data['email'])) {
                    $errors['email'] = 'Este email ya está registrado';
                }

                // Verificar DNI si se proporcionó
                if ($data['dni'] && $model->findByDni($data['dni'])) {
                    $errors['dni'] = 'Este DNI ya está registrado';
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                $result = $model->create($data);
                echo json_encode(['success' => true, 'message' => 'Cliente creado exitosamente', 'id' => $result]);
                break;

            case 'update':
                $id = (int)$_POST['id'];
                $data = [
                    'name' => trim($_POST['name']),
                    'email' => trim($_POST['email']),
                    'phone' => trim($_POST['phone']) ?: null,
                    'dni' => trim($_POST['dni']) ?: null,
                    'gender' => $_POST['gender'] ?: null,
                    'birth_date' => $_POST['birth_date'] ?: null,
                    'status' => $_POST['status']
                ];

                // Validaciones
                $errors = [];
                if (empty($data['name'])) $errors['name'] = 'El nombre es requerido';
                if (empty($data['email'])) $errors['email'] = 'El email es requerido';

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'El email no tiene un formato válido';
                }

                // Verificar si el email ya existe (excluyendo el cliente actual)
                $existingClient = $model->findByEmail($data['email']);
                if ($existingClient && $existingClient['id'] != $id) {
                    $errors['email'] = 'Este email ya está registrado';
                }

                // Verificar DNI si se proporcionó (excluyendo el cliente actual)
                if ($data['dni']) {
                    $existingDni = $model->findByDni($data['dni']);
                    if ($existingDni && $existingDni['id'] != $id) {
                        $errors['dni'] = 'Este DNI ya está registrado';
                    }
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                $result = $model->update($id, $data);
                echo json_encode(['success' => $result, 'message' => $result ? 'Cliente actualizado exitosamente' : 'Error al actualizar cliente']);
                break;

            case 'delete':
                $id = (int)($_POST['id'] ?? $_GET['id']);
                $result = $model->delete($id);
                echo json_encode(['success' => $result, 'message' => $result ? 'Cliente desactivado exitosamente' : 'Error al desactivar cliente']);
                break;

            case 'details':
                $id = (int)$_GET['id'];
                $client = $model->getDetailedById($id);
                if ($client) {
                    echo json_encode(['success' => true, 'client' => $client]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
                }
                break;

            case 'statistics':
                $stats = $model->getStatistics();
                echo json_encode(['success' => true, 'statistics' => $stats]);
                break;

            case 'top_customers':
                $limit = (int)($_GET['limit'] ?? 10);
                $topCustomers = $model->getTopCustomers($limit);
                echo json_encode(['success' => true, 'customers' => $topCustomers]);
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
$limit = 15;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';

// Filtros
$filters = [
    'status' => $_GET['status'] ?? '',
    'gender' => $_GET['gender'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'min_spent' => $_GET['min_spent'] ?? '',
    'min_orders' => $_GET['min_orders'] ?? '',
    'has_cart' => $_GET['has_cart'] ?? ''
];

// Obtener total de resultados y datos de clientes
$total = $model->count($search, $filters);
$clients = $model->getAll($limit, $offset, $search, $filters);

// Estadísticas generales
$statistics = $model->getStatistics();

$totalPages = ceil($total / $limit);
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
                <!-- Header de la página -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 mb-0.5">Gestión de Clientes</h1>
                            <p class="text-xs text-gray-600">Administra y visualiza todos los clientes registrados</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg px-3 py-2 border border-blue-200">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Total Clientes</p>
                                        <p class="text-lg font-bold text-blue-600"><?= number_format($statistics['total_clients']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de búsqueda y filtros -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <form method="get" class="space-y-2">
                        <div class="flex flex-col md:flex-row md:items-end space-y-2 md:space-y-0 md:space-x-2">
                            <div class="flex-1">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                        placeholder="Buscar por nombre, email, teléfono o DNI..."
                                        class="w-full pl-7 pr-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <div class="flex flex-row space-x-1">
                                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors flex items-center space-x-1">
                                    <i class="fas fa-search text-xs"></i>
                                    <span>Buscar</span>
                                </button>
                                <button type="button" id="toggleFilters" class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded text-xs hover:bg-gray-50 transition-colors flex items-center space-x-1">
                                    <i class="fas fa-filter text-xs"></i>
                                    <span>Filtros</span>
                                </button>
                                <?php if ($search || array_filter($filters)): ?>
                                    <a href="?" class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded text-xs hover:bg-gray-50 transition-colors flex items-center space-x-1">
                                        <i class="fas fa-times text-xs"></i>
                                        <span>Limpiar</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Panel de filtros -->
                        <div id="filtersPanel" class="hidden border-t border-gray-200 pt-2 mt-2">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                                    <select name="status" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <option value="ACTIVE" <?= $filters['status'] === 'ACTIVE' ? 'selected' : '' ?>>Activo</option>
                                        <option value="INACTIVE" <?= $filters['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Género</label>
                                    <select name="gender" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <option value="M" <?= $filters['gender'] === 'M' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="F" <?= $filters['gender'] === 'F' ? 'selected' : '' ?>>Femenino</option>
                                        <option value="Masculino" <?= $filters['gender'] === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="Femenino" <?= $filters['gender'] === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Gasto Mínimo</label>
                                    <input type="number" name="min_spent" value="<?= htmlspecialchars($filters['min_spent']) ?>"
                                        placeholder="0.00" step="0.01"
                                        class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Min. Órdenes</label>
                                    <input type="number" name="min_orders" value="<?= htmlspecialchars($filters['min_orders']) ?>"
                                        placeholder="0" min="0"
                                        class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
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
                                <i class="fas fa-users text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Total</p>
                                <p class="text-sm font-semibold text-gray-900"><?= number_format($statistics['total_clients']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-green-400 to-green-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-user-check text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Activos</p>
                                <p class="text-sm font-semibold text-gray-900"><?= number_format($statistics['active_clients']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-user-plus text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Nuevos (Mes)</p>
                                <p class="text-sm font-semibold text-gray-900"><?= number_format($statistics['new_this_month']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-purple-400 to-purple-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-calendar-day text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Hoy</p>
                                <p class="text-sm font-semibold text-gray-900"><?= number_format($statistics['new_today']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de clientes -->
                <div class="bg-white rounded border border-gray-200 overflow-hidden mb-3">
                    <div class="px-3 py-2 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium text-gray-900">Lista de Clientes</h3>
                            <div class="flex space-x-1">
                                <button class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors flex items-center space-x-1" onclick="openCreateModal()">
                                    <i class="fas fa-plus text-xs"></i>
                                    <span>Nuevo</span>
                                </button>
                                <button class="px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-download text-xs mr-1"></i>
                                    Exportar
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($clients)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">No se encontraron clientes</h3>
                            <p class="text-xs text-gray-500">
                                <?php if ($search): ?>
                                    No hay clientes que coincidan con "<?= htmlspecialchars($search) ?>"
                                <?php else: ?>
                                    No hay clientes registrados en el sistema
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Información</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actividad</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estadísticas</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($clients as $client): ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <!-- Cliente -->
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                                        <span class="text-white text-xs font-medium">
                                                            <?= strtoupper(substr(htmlspecialchars($client['name']), 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="text-xs font-medium text-gray-900">
                                                            <?= htmlspecialchars($client['name']) ?>
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            ID: #<?= $client['id'] ?>
                                                        </div>
                                                        <?php if ($client['age']): ?>
                                                            <div class="text-xs text-gray-500">
                                                                <?= $client['age'] ?> años
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Contacto -->
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="text-xs text-gray-900">
                                                    <div class="flex items-center mb-1">
                                                        <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                                        <?= htmlspecialchars($client['email']) ?>
                                                    </div>
                                                    <?php if ($client['phone']): ?>
                                                        <div class="flex items-center mb-1">
                                                            <i class="fas fa-phone text-gray-400 mr-1"></i>
                                                            <?= htmlspecialchars($client['phone']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($client['dni']): ?>
                                                        <div class="flex items-center">
                                                            <i class="fas fa-id-card text-gray-400 mr-1"></i>
                                                            <?= htmlspecialchars($client['dni']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- Información -->
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="text-xs">
                                                    <?php if ($client['gender']): ?>
                                                        <div class="flex items-center mb-1">
                                                            <i class="fas fa-venus-mars text-gray-400 mr-1"></i>
                                                            <span class="text-gray-700"><?= htmlspecialchars($client['gender']) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($client['default_address']): ?>
                                                        <div class="flex items-center mb-1">
                                                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                                            <span class="text-gray-700 truncate" title="<?= htmlspecialchars($client['default_address']) ?>">
                                                                <?= substr(htmlspecialchars($client['default_address']), 0, 20) ?>...
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                                        <span class="text-gray-700"><?= $client['time_since_registration'] ?></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Actividad -->
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="text-xs">
                                                    <div class="flex items-center mb-1">
                                                        <i class="fas fa-shopping-bag text-gray-400 mr-1"></i>
                                                        <span class="font-medium"><?= $client['total_orders'] ?></span>
                                                        <span class="text-gray-500 ml-1">órdenes</span>
                                                    </div>
                                                    <?php if ($client['cart_items_count'] > 0): ?>
                                                        <div class="flex items-center mb-1">
                                                            <i class="fas fa-shopping-cart text-orange-500 mr-1"></i>
                                                            <span class="font-medium text-orange-600"><?= $client['cart_items_count'] ?></span>
                                                            <span class="text-gray-500 ml-1">en carrito</span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-clock text-gray-400 mr-1"></i>
                                                        <span class="text-gray-700"><?= $client['time_since_last_activity'] ?></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Estadísticas -->
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="text-xs">
                                                    <div class="flex items-center mb-1">
                                                        <i class="fas fa-coins text-green-500 mr-1"></i>
                                                        <span class="font-bold text-green-600">S/ <?= number_format($client['total_spent'], 2) ?></span>
                                                    </div>
                                                    <?php if ($client['total_reviews'] > 0): ?>
                                                        <div class="flex items-center mb-1">
                                                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                                                            <span class="font-medium"><?= $client['avg_review_rating'] ?></span>
                                                            <span class="text-gray-500 ml-1">(<?= $client['total_reviews'] ?>)</span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $client['customer_tier']['color'] ?>">
                                                        <?= $client['customer_tier']['tier'] ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Estado -->
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $client['status_color'] ?>">
                                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                                    <?= htmlspecialchars($client['status']) ?>
                                                </span>
                                            </td>

                                            <!-- Acciones -->
                                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium space-x-1">
                                                <button class="text-blue-600 hover:text-blue-900 transition-colors p-1 rounded"
                                                    onclick="openDetailModal(<?= $client['id'] ?>)"
                                                    title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-green-600 hover:text-green-900 transition-colors p-1 rounded"
                                                    onclick="openEditModal(<?= $client['id'] ?>)"
                                                    title="Editar cliente">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($client['status'] === 'ACTIVE'): ?>
                                                    <button class="text-red-600 hover:text-red-900 transition-colors p-1 rounded"
                                                        onclick="openDeleteModal(<?= $client['id'] ?>, '<?= htmlspecialchars($client['name'], ENT_QUOTES) ?>')"
                                                        title="Desactivar cliente">
                                                        <i class="fas fa-user-slash"></i>
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

                <!-- Paginación -->
                <?php if ($totalPages > 1): ?>
                    <?php
                    $queryParams = [];
                    if ($search) $queryParams['search'] = $search;
                    foreach ($filters as $key => $value) {
                        if ($value !== '') $queryParams[$key] = $value;
                    }

                    function buildUrl($queryParams, $page)
                    {
                        $params = $queryParams;
                        $params['page'] = $page;
                        return '?' . http_build_query($params);
                    }
                    ?>
                    <div class="bg-white rounded border border-gray-200 px-3 py-2">
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                <?= ($offset + 1) ?>-<?= min($offset + $limit, $total) ?> de <?= $total ?>
                            </div>
                            <div class="flex space-x-1">
                                <?php if ($page > 1): ?>
                                    <a href="<?= buildUrl($queryParams, $page - 1) ?>"
                                        class="px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                        Ant
                                    </a>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($totalPages, $page + 2);

                                for ($i = $start; $i <= $end; $i++): ?>
                                    <a href="<?= buildUrl($queryParams, $i) ?>"
                                        class="px-2 py-1 text-xs border rounded transition-colors <?= $i == $page ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="<?= buildUrl($queryParams, $page + 1) ?>"
                                        class="px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                        Sig
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <?php include_once './../includes/footer.php'; ?>
                </div>
            </main>
        </div>
    </div>

    <?php include_once 'modales.php'; ?>

    <script src="../js/components.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle filtros
            const toggleFilters = document.getElementById('toggleFilters');
            const filtersPanel = document.getElementById('filtersPanel');

            if (toggleFilters && filtersPanel) {
                toggleFilters.addEventListener('click', function() {
                    filtersPanel.classList.toggle('hidden');
                });
            }

            // Mostrar filtros si hay filtros activos
            <?php if (array_filter($filters)): ?>
                if (filtersPanel) {
                    filtersPanel.classList.remove('hidden');
                }
            <?php endif; ?>
        });
    </script>
</body>

</html>
