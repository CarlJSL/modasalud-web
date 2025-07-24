<?php
session_start();

// Control de acceso basado en roles y permisos
require_once __DIR__ . '/../includes/access_control.php';

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/rolesModel.php';

use App\Model\RolesModel;

// Instancia del modelo
$rolesModel = new RolesModel($pdo);

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'create':
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'])
                ];

                // Procesar permisos si se proporcionan
                if (!empty($_POST['permissions'])) {
                    $permissions = [];
                    foreach ($_POST['permissions'] as $tableName => $perms) {
                        $permissions[] = [
                            'table_name' => $tableName,
                            'can_create' => isset($perms['create']),
                            'can_read' => isset($perms['read']),
                            'can_update' => isset($perms['update']),
                            'can_delete' => isset($perms['delete'])
                        ];
                    }
                    $data['permissions'] = $permissions;
                }

                // Validaciones
                $errors = [];
                if (empty($data['name'])) $errors['name'] = 'El nombre del rol es requerido';

                // Verificar si el nombre ya existe
                if ($rolesModel->findByName($data['name'])) {
                    $errors['name'] = 'Este nombre de rol ya está en uso';
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'errors' => $errors]);
                    exit;
                }

                $id = $rolesModel->create($data);
                echo json_encode(['success' => true, 'message' => 'Rol creado exitosamente', 'id' => $id]);
                break;

            case 'update':
                $id = (int)$_POST['id'];
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'])
                ];

                // Procesar permisos si se proporcionan
                if (!empty($_POST['permissions'])) {
                    $permissions = [];
                    foreach ($_POST['permissions'] as $tableName => $perms) {
                        $permissions[] = [
                            'table_name' => $tableName,
                            'can_create' => isset($perms['create']),
                            'can_read' => isset($perms['read']),
                            'can_update' => isset($perms['update']),
                            'can_delete' => isset($perms['delete'])
                        ];
                    }
                    $data['permissions'] = $permissions;
                }

                // Validaciones
                $errors = [];
                if (empty($data['name'])) $errors['name'] = 'El nombre del rol es requerido';

                // Verificar si el nombre ya existe (excepto el rol actual)
                $existingRole = $rolesModel->findByName($data['name']);
                if ($existingRole && $existingRole['id'] != $id) {
                    $errors['name'] = 'Este nombre de rol ya está en uso';
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'errors' => $errors]);
                    exit;
                }

                $result = $rolesModel->update($id, $data);
                echo json_encode(['success' => true, 'message' => 'Rol actualizado exitosamente']);
                break;

            case 'delete':
                $id = (int)($_POST['id'] ?? $_GET['id']);
                $result = $rolesModel->delete($id);
                echo json_encode(['success' => true, 'message' => 'Rol eliminado exitosamente']);
                break;

            case 'get':
                $id = (int)$_GET['id'];
                $role = $rolesModel->getById($id);
                if ($role) {
                    echo json_encode(['success' => true, 'role' => $role]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Rol no encontrado']);
                }
                break;

            case 'get_detailed':
                $id = (int)$_GET['id'];
                $role = $rolesModel->getDetailedById($id);
                if ($role) {
                    echo json_encode(['success' => true, 'role' => $role]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Rol no encontrado']);
                }
                break;

            case 'get_tables':
                $tables = $rolesModel->getAvailableTables();
                echo json_encode(['success' => true, 'tables' => $tables]);
                break;

            case 'get_statistics':
                $stats = $rolesModel->getStatistics();
                echo json_encode(['success' => true, 'statistics' => $stats]);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Configuración de paginación
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Parámetros de búsqueda y filtros
$search = $_GET['search'] ?? '';
$filters = [
    'permission_level' => $_GET['permission_level'] ?? '',
    'min_users' => $_GET['min_users'] ?? '',
    'user_status' => $_GET['user_status'] ?? '',
    'table_name' => $_GET['table_name'] ?? ''
];

// Filtrar solo valores no vacíos
$filters = array_filter($filters, function($value) {
    return $value !== '';
});

// Obtener datos
$roles = $rolesModel->getAll($limit, $offset, $search, $filters);
$totalRoles = $rolesModel->count($search, $filters);
$totalPages = ceil($totalRoles / $limit);

// Obtener estadísticas generales
$statistics = $rolesModel->getStatistics();

// Obtener tablas disponibles para filtros
$availableTables = $rolesModel->getAvailableTables();
?>

<!DOCTYPE html>
<html lang="es">

<?php include_once './../includes/head.php'; ?>

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
                <!-- Header de la página de roles con estadísticas -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 mb-0.5">Gestión de Roles y Permisos</h1>
                            <p class="text-xs text-gray-600">Administra roles de usuario y sus permisos del sistema</p>
                        </div>
                        <button onclick="openCreateRoleModal()" class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors flex items-center space-x-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Nuevo Rol</span>
                        </button>
                    </div>
                </div>

                <!-- Panel de estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2">
                    <div class="bg-white p-3 rounded border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500">Total Roles</p>
                                <p class="text-lg font-semibold text-gray-900"><?= number_format($statistics['total_roles'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500">Permisos Activos</p>
                                <p class="text-lg font-semibold text-gray-900"><?= number_format($statistics['total_permissions'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500">Usuarios con Roles</p>
                                <p class="text-lg font-semibold text-gray-900"><?= number_format($statistics['total_users_with_roles'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded border border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500">Tablas con Permisos</p>
                                <p class="text-lg font-semibold text-gray-900"><?= number_format($statistics['tables_with_permissions'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de búsqueda y filtros -->
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
                                        placeholder="Buscar por nombre o descripción..."
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
                                <!-- Filtro por Nivel de Permisos -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nivel de Permisos</label>
                                    <select name="permission_level" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <option value="TOTAL" <?= $filters['permission_level'] === 'TOTAL' ? 'selected' : '' ?>>Total</option>
                                        <option value="MODERADO" <?= $filters['permission_level'] === 'MODERADO' ? 'selected' : '' ?>>Moderado</option>
                                        <option value="LECTURA" <?= $filters['permission_level'] === 'LECTURA' ? 'selected' : '' ?>>Lectura</option>
                                        <option value="LIMITADO" <?= $filters['permission_level'] === 'LIMITADO' ? 'selected' : '' ?>>Limitado</option>
                                        <option value="SIN_PERMISOS" <?= $filters['permission_level'] === 'SIN_PERMISOS' ? 'selected' : '' ?>>Sin Permisos</option>
                                    </select>
                                </div>

                                <!-- Filtro por Mínimo de Usuarios -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Mínimo Usuarios</label>
                                    <input type="number" name="min_users" value="<?= htmlspecialchars($filters['min_users'] ?? '') ?>"
                                        placeholder="Ej: 5"
                                        class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>

                                <!-- Filtro por Estado de Usuarios -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Estado Usuarios</label>
                                    <select name="user_status" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <option value="active" <?= $filters['user_status'] === 'active' ? 'selected' : '' ?>>Solo Activos</option>
                                        <option value="inactive" <?= $filters['user_status'] === 'inactive' ? 'selected' : '' ?>>Solo Inactivos</option>
                                    </select>
                                </div>

                                <!-- Filtro por Tabla -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tabla</label>
                                    <select name="table_name" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todas</option>
                                        <?php foreach ($availableTables as $table): ?>
                                            <option value="<?= $table ?>" <?= $filters['table_name'] === $table ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($table) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabla de roles -->
                <div class="bg-white rounded border border-gray-200 overflow-hidden">
                    <!-- Header de la tabla -->
                    <div class="px-2 py-2 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium text-gray-900">
                                Roles (<?= number_format($totalRoles) ?> total)
                            </h3>
                            <div class="flex items-center space-x-2">
                                <select onchange="changeLimit(this.value)" class="text-xs border border-gray-300 rounded py-1 px-2">
                                    <option value="10" <?= $limit === 10 ? 'selected' : '' ?>>10 por página</option>
                                    <option value="25" <?= $limit === 25 ? 'selected' : '' ?>>25 por página</option>
                                    <option value="50" <?= $limit === 50 ? 'selected' : '' ?>>50 por página</option>
                                    <option value="100" <?= $limit === 100 ? 'selected' : '' ?>>100 por página</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de la tabla -->
                    <div class="overflow-x-auto table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">Rol</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Nivel Permisos</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Permisos</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">Usuarios</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Tablas Gestionadas</th>
                                    <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($roles)): ?>
                                    <tr>
                                        <td colspan="6" class="px-2 py-4 text-center text-xs text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.489.904-6.077 2.383m1.77-14.769A7.962 7.962 0 0112 1c2.34 0 4.489.904 6.077 2.383M5.5 6.5h.01m0 9h.01M18.5 6.5h.01m0 9h.01"></path>
                                                </svg>
                                                <p class="text-sm font-medium text-gray-900">No se encontraron roles</p>
                                                <p class="text-gray-500">Intenta ajustar los filtros de búsqueda</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($roles as $role): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-2 py-2">
                                                <div class="expand-cell">
                                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($role['name']) ?></div>
                                                    <?php if ($role['description']): ?>
                                                        <div class="text-xs text-gray-500 truncate-text" title="<?= htmlspecialchars($role['description']) ?>"><?= htmlspecialchars($role['description']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $role['permission_level_color'] ?>">
                                                    <?= ucfirst(strtolower(str_replace('_', ' ', $role['permission_level']))) ?>
                                                </span>
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex items-center space-x-1">
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-800">
                                                            C: <?= $role['create_permissions'] ?>
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-blue-100 text-blue-800">
                                                            R: <?= $role['read_permissions'] ?>
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">
                                                            U: <?= $role['update_permissions'] ?>
                                                        </span>
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-red-100 text-red-800">
                                                            D: <?= $role['delete_permissions'] ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">Total: <?= $role['total_permissions'] ?></div>
                                            </td>
                                            <td class="px-2 py-2 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm text-gray-900"><?= $role['total_users'] ?> total</div>
                                                        <div class="text-xs text-gray-500">
                                                            <?= $role['active_users'] ?> activos
                                                            <?php if ($role['inactive_users'] > 0): ?>
                                                                | <?= $role['inactive_users'] ?> inactivos
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ($role['user_activity_ratio'] > 0): ?>
                                                            <div class="flex items-center mt-1">
                                                                <div class="w-8 bg-gray-200 rounded-full h-1">
                                                                    <div class="bg-green-600 h-1 rounded-full" style="width: <?= $role['user_activity_ratio'] ?>%"></div>
                                                                </div>
                                                                <span class="ml-1 text-xs text-gray-500"><?= $role['user_activity_ratio'] ?>%</span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-2 py-2">
                                                <div class="text-xs text-gray-900 expand-cell">
                                                    <?php if ($role['tables_with_permissions']): ?>
                                                        <div class="truncate-text" title="<?= htmlspecialchars($role['tables_with_permissions']) ?>">
                                                            <?= htmlspecialchars($role['tables_with_permissions']) ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-gray-400 italic">Sin tablas asignadas</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-2 py-2 text-right text-xs font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <button onclick="viewRoleDetails(<?= $role['id'] ?>)" 
                                                        class="bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md text-blue-600 hover:text-blue-900 transition-colors" title="Ver detalles">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </button>
                                                    <button onclick="editRole(<?= $role['id'] ?>)" 
                                                        class="bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-md text-indigo-600 hover:text-indigo-900 transition-colors" title="Editar">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button onclick="deleteRole(<?= $role['id'] ?>, '<?= htmlspecialchars($role['name']) ?>')" 
                                                        class="bg-red-50 hover:bg-red-100 p-1.5 rounded-md text-red-600 hover:text-red-900 transition-colors" title="Eliminar">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <?php if ($totalPages > 1): ?>
                        <div class="bg-white px-2 py-2 flex items-center justify-between border-t border-gray-200">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <?php if ($page > 1): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                                       class="relative inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Anterior
                                    </a>
                                <?php endif; ?>
                                <?php if ($page < $totalPages): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                                       class="ml-2 relative inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Siguiente
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs text-gray-700">
                                        Mostrando <span class="font-medium"><?= (($page - 1) * $limit) + 1 ?></span> 
                                        a <span class="font-medium"><?= min($page * $limit, $totalRoles) ?></span> 
                                        de <span class="font-medium"><?= $totalRoles ?></span> resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <!-- Botón anterior -->
                                        <?php if ($page > 1): ?>
                                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                                               class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-gray-300 bg-white text-xs font-medium text-gray-500 hover:bg-gray-50">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Números de página -->
                                        <?php
                                        $start = max(1, $page - 2);
                                        $end = min($totalPages, $page + 2);
                                        
                                        for ($i = $start; $i <= $end; $i++): ?>
                                            <?php if ($i == $page): ?>
                                                <span class="relative inline-flex items-center px-2 py-1 border border-gray-300 bg-blue-50 text-xs font-medium text-blue-600">
                                                    <?= $i ?>
                                                </span>
                                            <?php else: ?>
                                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                                                   class="relative inline-flex items-center px-2 py-1 border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                    <?= $i ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endfor; ?>

                                        <!-- Botón siguiente -->
                                        <?php if ($page < $totalPages): ?>
                                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                                               class="relative inline-flex items-center px-1 py-1 rounded-r-md border border-gray-300 bg-white text-xs font-medium text-gray-500 hover:bg-gray-50">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Incluir modales -->
    <?php include_once './modales.php'; ?>

    <!-- Scripts -->
    <script>
        // Toggle filtros
        document.getElementById('toggleFilters').addEventListener('click', function() {
            const panel = document.getElementById('filtersPanel');
            panel.classList.toggle('hidden');
        });

        // Cambiar límite de resultados
        function changeLimit(limit) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('limit', limit);
            urlParams.set('page', '1'); // Reset a la primera página
            window.location.search = urlParams.toString();
        }

        // Funciones para los modales
        function openCreateRoleModal() {
            document.getElementById('roleModalTitle').textContent = 'Crear Nuevo Rol';
            document.getElementById('roleForm').reset();
            document.getElementById('roleId').value = '';
            clearPermissions();
            loadAvailableTables();
            document.getElementById('roleModal').classList.remove('hidden');
        }

        function editRole(id) {
            fetch(`?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('roleModalTitle').textContent = 'Editar Rol';
                        document.getElementById('roleId').value = data.role.id;
                        document.getElementById('roleName').value = data.role.name;
                        document.getElementById('roleDescription').value = data.role.description || '';
                        loadRolePermissions(id);
                        document.getElementById('roleModal').classList.remove('hidden');
                    } else {
                        alert('Error al cargar el rol: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar el rol');
                });
        }

        function viewRoleDetails(id) {
            fetch(`?action=get_detailed&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showRoleDetails(data.role);
                        document.getElementById('roleDetailModal').classList.remove('hidden');
                    } else {
                        alert('Error al cargar los detalles: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los detalles');
                });
        }

        function deleteRole(id, name) {
            // Mostrar modal de confirmación
            document.getElementById('deleteRoleId').value = id;
            document.getElementById('deleteRoleTitle').textContent = 'Eliminar Rol';
            document.getElementById('deleteRoleMessage').textContent = `¿Estás seguro de que quieres eliminar el rol "${name}"? Esta acción no se puede deshacer.`;
            document.getElementById('deleteRoleModal').classList.remove('hidden');
            
            // Configurar el botón de confirmación
            document.getElementById('confirmDeleteButton').onclick = function() {
                document.getElementById('deleteRoleModal').classList.add('hidden');
                
                fetch('?action=delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al eliminar el rol', 'error');
                });
            };
        }

        // Mostrar filtros si hay filtros activos
        <?php if (array_filter($filters)): ?>
            document.getElementById('filtersPanel').classList.remove('hidden');
        <?php endif; ?>
    </script>
</body>
</html>
