<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php'); // o ../index.php dependiendo del nivel de carpeta
    exit();
}

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/userBaseModel.php';

use App\Model\BaseModel;

// Instancia del modelo apuntando a la tabla 'users'
$model = new BaseModel($pdo, 'users');

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'create':
                $data = [
                    'username' => trim($_POST['username']),
                    'email' => trim($_POST['email']),
                    'name' => trim($_POST['name']),
                    'password' => $_POST['password'],
                    'role_id' => (int)$_POST['role_id'],
                    'status' => $_POST['status']
                ];

                // Validaciones
                $errors = [];
                if (empty($data['username'])) $errors['username'] = 'El nombre de usuario es requerido';
                if (empty($data['email'])) $errors['email'] = 'El email es requerido';
                if (empty($data['name'])) $errors['name'] = 'El nombre completo es requerido';
                if (empty($data['password'])) $errors['password'] = 'La contraseña es requerida';
                if (empty($data['role_id'])) $errors['role_id'] = 'El rol es requerido';

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'El email no tiene un formato válido';
                }

                // Verificar si username/email ya existen
                if ($model->usernameExists($data['username'])) {
                    $errors['username'] = 'Este nombre de usuario ya está en uso';
                }
                if ($model->emailExists($data['email'])) {
                    $errors['email'] = 'Este email ya está registrado';
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                $result = $model->create($data);
                echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente']);
                break;

            case 'update':
                $id = (int)$_POST['id'];
                $data = [
                    'username' => trim($_POST['username']),
                    'email' => trim($_POST['email']),
                    'name' => trim($_POST['name']),
                    'role_id' => (int)$_POST['role_id'],
                    'status' => $_POST['status']
                ];

                // Solo actualizar contraseña si se proporcionó
                if (!empty($_POST['password'])) {
                    $data['password'] = $_POST['password'];
                }

                // Validaciones
                $errors = [];
                if (empty($data['username'])) $errors['username'] = 'El nombre de usuario es requerido';
                if (empty($data['email'])) $errors['email'] = 'El email es requerido';
                if (empty($data['name'])) $errors['name'] = 'El nombre completo es requerido';
                if (empty($data['role_id'])) $errors['role_id'] = 'El rol es requerido';

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'El email no tiene un formato válido';
                }

                // Verificar si username/email ya existen (excluyendo el usuario actual)
                if ($model->usernameExists($data['username'], $id)) {
                    $errors['username'] = 'Este nombre de usuario ya está en uso';
                }
                if ($model->emailExists($data['email'], $id)) {
                    $errors['email'] = 'Este email ya está registrado';
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                // Verificar si hay cambios reales comparando con los datos actuales
                $currentUser = $model->getById($id);
                if (!$currentUser) {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                    exit;
                }

                // Comparar datos actuales con los nuevos
                $hasChanges = false;
                $fieldsToCheck = ['username', 'email', 'name', 'role_id', 'status'];
                
                foreach ($fieldsToCheck as $field) {
                    if ($data[$field] != $currentUser[$field]) {
                        $hasChanges = true;
                        break;
                    }
                }

                // Verificar si se proporcionó una nueva contraseña
                if (!empty($_POST['password'])) {
                    $hasChanges = true;
                }

                // Si no hay cambios, retornar mensaje informativo
                if (!$hasChanges) {
                    echo json_encode(['success' => false, 'message' => 'No se han detectado cambios para actualizar', 'no_changes' => true]);
                    exit;
                }

                $result = $model->update($id, $data);
                echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
                break;

            case 'delete':
                $id = (int)($_POST['id'] ?? $_GET['id']);
                $result = $model->softDelete($id);
                echo json_encode(['success' => true, 'message' => 'Usuario desactivado exitosamente']);
                break;

            case 'get':
                $id = (int)$_GET['id'];
                $user = $model->getById($id);
                if ($user) {
                    echo json_encode(['success' => true, 'user' => $user]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }
                break;

            case 'details':
                $id = (int)$_GET['id'];
                $user = $model->getDetailedById($id);
                if ($user) {
                    echo json_encode(['success' => true, 'user' => $user]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
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
    'role_id' => $_GET['role_id'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? ''
];

// Obtener roles disponibles
$roles = $model->getRoles();

// Obtener total de resultados y datos de usuarios
$total = $model->count($search, $filters);
$users = $model->getAll($limit, $offset, $search, $filters);
// Contar usuarios activos (status = 'ACTIVE') con los mismos filtros y búsqueda
$activeFilters = $filters;
$activeFilters['status'] = 'ACTIVE';
$usersActives = $model->count($search, $activeFilters);
$totalPages = ceil($total / $limit);
?>


<!-- HTML-->

<!DOCTYPE html>
<html lang="es">

<?php
// Incluir archivo de configuración de la cabecera
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
                <!-- Header de la página de usuarios con breadcrumb -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <!-- Título y descripción de la página -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 mb-0.5">Gestión de Usuarios</h1>
                            <p class="text-xs text-gray-600">Administra y visualiza todos los usuarios registrados</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de búsqueda y filtros compacto -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <form method="get" class="space-y-2">
                        <!-- Búsqueda principal -->
                        <div class="flex flex-col md:flex-row md:items-end space-y-2 md:space-y-0 md:space-x-2">
                            <div class="flex-1">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                        placeholder="Buscar por nombre o email..."
                                        class="w-full pl-7 pr-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <div class="flex flex-row space-x-1">
                                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span>Buscar</span>
                                </button>
                                <button type="button" id="toggleFilters" class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded text-xs hover:bg-gray-50 transition-colors flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    <span>Filtros</span>
                                </button>
                                <?php if ($search || array_filter($filters)): ?>
                                    <a href="?" class="px-3 py-1.5 border border-gray-300 text-gray-700 rounded text-xs hover:bg-gray-50 transition-colors flex items-center space-x-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>Limpiar</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Panel de filtros (inicialmente oculto) -->
                        <div id="filtersPanel" class="hidden border-t border-gray-200 pt-2 mt-2">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                <!-- Filtro por Status -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                                    <select name="status" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <option value="ACTIVE" <?= $filters['status'] === 'ACTIVE' ? 'selected' : '' ?>>Activo</option>
                                        <option value="INACTIVE" <?= $filters['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>

                                <!-- Filtro por Rol -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Rol</label>
                                    <select name="role_id" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= $role['id'] ?>" <?= $filters['role_id'] == $role['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($role['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Fecha desde -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Desde</label>
                                    <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>"
                                        class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>

                                <!-- Fecha hasta -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Hasta</label>
                                    <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>"
                                        class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Estadísticas compactas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-blue-400 to-blue-500 rounded flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Total</p>
                                <p class="text-sm font-semibold text-gray-900"><?= $total ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-green-400 to-green-500 rounded flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Activos</p>
                                <p class="text-sm font-semibold text-gray-900"><?= $usersActives ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-purple-400 to-purple-500 rounded flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v10h6V6H9z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Página</p>
                                <p class="text-sm font-semibold text-gray-900"><?= $page ?>/<?= $totalPages ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de usuarios compacta -->
                <div class="bg-white rounded border border-gray-200 overflow-hidden mb-3">
                    <div class="px-3 py-2 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium text-gray-900">Lista de Usuarios</h3>
                            <div class="flex space-x-1">
                                <button class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors flex items-center space-x-1" onclick="openCreateModal()">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span>Nuevo</span>
                                </button>
                                <button class="px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                    Exportar
                                </button>

                            </div>
                        </div>
                    </div>

                    <?php if (empty($users)): ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">No se encontraron usuarios</h3>
                            <p class="text-xs text-gray-500">
                                <?php if ($search): ?>
                                    No hay usuarios que coincidan con "<?= htmlspecialchars($search) ?>"
                                <?php else: ?>
                                    No hay usuarios registrados en el sistema
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($users as $user): ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                                #<?= $user['id'] ?>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-7 h-7 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center mr-2">
                                                        <span class="text-white text-xs font-medium">
                                                            <?= strtoupper(substr(htmlspecialchars($user['name']), 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="text-xs font-medium text-gray-900">
                                                            <?= htmlspecialchars($user['name']) ?>
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            Usuario desde <?= date('M Y') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                                <?= htmlspecialchars($user['email']) ?>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <?php
                                                $status = strtolower($user['status']);
                                                if ($status === 'active') {
                                                    $badgeClass = 'bg-green-100 text-green-800';
                                                } elseif ($status === 'inactive') {
                                                    $badgeClass = 'bg-red-100 text-red-800';
                                                } else {
                                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                                }
                                                ?>
                                                <span class="px-1.5 py-0.5 text-xs font-medium rounded-full <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($user['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <?php
                                                $role = strtolower($user['role']);
                                                if ($role === 'admin') {
                                                    $roleBadgeClass = 'bg-blue-100 text-blue-800';
                                                } elseif ($role === 'user') {
                                                    $roleBadgeClass = 'bg-gray-100 text-gray-800';
                                                } elseif ($role === 'moderator') {
                                                    $roleBadgeClass = 'bg-purple-100 text-purple-800';
                                                } else {
                                                    $roleBadgeClass = 'bg-yellow-100 text-yellow-800';
                                                }
                                                ?>
                                                <span class="px-1.5 py-0.5 text-xs font-medium rounded-full <?= $roleBadgeClass ?>">
                                                    <?= htmlspecialchars($user['role']) ?>
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium space-x-1">
                                                <button class="action-button text-blue-600 hover:text-blue-900 transition-colors p-1 rounded"
                                                    onclick="openDetailModal(<?= $user['id'] ?>)"
                                                    title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-button text-green-600 hover:text-green-900 transition-colors p-1 rounded"
                                                    onclick="openEditModal(<?= $user['id'] ?>)"
                                                    title="Editar usuario">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-button text-red-600 hover:text-red-900 transition-colors p-1 rounded"
                                                    onclick="openDeleteModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>')"
                                                    title="Desactivar usuario">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
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
                    if ($filters['role_id']) $queryParams['role_id'] = $filters['role_id'];
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

                                if ($start > 1): ?>
                                    <a href="<?= buildUrl($queryParams, 1) ?>"
                                        class="px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                        1
                                    </a>
                                    <?php if ($start > 2): ?>
                                        <span class="px-2 py-1 text-xs text-gray-500">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <a href="<?= buildUrl($queryParams, $i) ?>"
                                        class="px-2 py-1 text-xs border rounded transition-colors <?= $i == $page ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($end < $totalPages): ?>
                                    <?php if ($end < $totalPages - 1): ?>
                                        <span class="px-2 py-1 text-xs text-gray-500">...</span>
                                    <?php endif; ?>
                                    <a href="<?= buildUrl($queryParams, $totalPages) ?>"
                                        class="px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                        <?= $totalPages ?>
                                    </a>
                                <?php endif; ?>

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
    </script>

</body>

</html>