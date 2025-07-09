<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php'); // o ../index.php dependiendo del nivel de carpeta
    exit();
}


require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/productModel.php';

use App\Model\ProductModel;

// Instancia del modelo apuntando a la tabla 'products'
$model = new ProductModel($pdo, 'products');

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'create':
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'price' => (float)$_POST['price'],
                    'category' => trim($_POST['category']),
                    'subcategory' => trim($_POST['subcategory']),
                    'stock' => (int)$_POST['stock'],
                    'size' => $_POST['size'],
                    'status' => $_POST['status']
                ];

                // Validaciones
                $errors = [];
                if (empty($data['name'])) $errors['name'] = 'El nombre del producto es requerido';
                if (empty($data['price']) || $data['price'] <= 0) $errors['price'] = 'El precio debe ser mayor a 0';
                if (empty($data['category'])) $errors['category'] = 'La categoría es requerida';
                if (empty($data['subcategory'])) $errors['subcategory'] = 'La subcategoría es requerida';
                if ($data['stock'] < 0) $errors['stock'] = 'El stock no puede ser negativo';

                // Verificar si el nombre ya existe
                if ($model->nameExists($data['name'], $data['size'], $data['description'])) {
                    $errors['name'] = 'Este nombre de producto ya está en uso';
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                $result = $model->create($data);
                echo json_encode(['success' => true, 'message' => 'Producto creado exitosamente']);
                break;

            case 'update':
                $id = (int)$_POST['id'];
                $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'price' => (float)$_POST['price'],
                    'category' => trim($_POST['category']),
                    'subcategory' => trim($_POST['subcategory']),
                    'stock' => $stock,
                    'size' => $_POST['size'],
                ];

                if ($stock > 0) {
                    $data['status'] = $_POST['status']; // valor por defecto si no viene
                } else {
                    $data['status'] = 'OUT_OF_STOCK'; // asignación automática
                }
                // Validaciones
                $errors = [];
                if (empty($data['name'])) $errors['name'] = 'El nombre del producto es requerido';
                if (empty($data['price']) || $data['price'] <= 0) $errors['price'] = 'El precio debe ser mayor a 0';
                if (empty($data['category'])) $errors['category'] = 'La categoría es requerida';
                if (empty($data['subcategory'])) $errors['subcategory'] = 'La subcategoría es requerida';
                if ($data['stock'] < 0) $errors['stock'] = 'El stock no puede ser negativo';

                // Verificar si el nombre ya existe (excluyendo el producto actual)
                if ($model->nameExists($data['name'], $data['size'], $data['description'], $id)) {
                    $errors['name'] = 'Este nombre de producto ya está en uso';
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                // Verificar si hay cambios reales comparando con los datos actuales
                $currentProduct = $model->getById($id);
                if (!$currentProduct) {
                    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                    exit;
                }

                // Comparar datos actuales con los nuevos
                $hasChanges = false;
                $fieldsToCheck = ['name', 'description', 'price', 'category', 'stock', 'size', 'status'];

                foreach ($fieldsToCheck as $field) {
                    if ($data[$field] != $currentProduct[$field]) {
                        $hasChanges = true;
                        break;
                    }
                }

                // Si no hay cambios, retornar mensaje informativo
                if (!$hasChanges) {
                    echo json_encode(['success' => false, 'message' => 'No se han detectado cambios para actualizar', 'no_changes' => true]);
                    exit;
                }

                $result = $model->update($id, $data);
                echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente']);
                break;

            case 'delete':
                $id = (int)($_POST['id'] ?? $_GET['id']);
                $statusnum = (int)($_POST['st'] ?? $_GET['st']);
                $result = $model->softDelete($id, $statusnum);
                $message = $statusnum === 1 ? 'Producto activado exitosamente' : 'Producto desactivado exitosamente';

                echo json_encode(['success' => true, 'message' => $message]);
                break;

            case 'get':
                $id = (int)$_GET['id'];
                $product = $model->getById($id);
                if ($product) {
                    echo json_encode(['success' => true, 'product' => $product]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                }
                break;

            case 'details':
                $id = (int)$_GET['id'];
                $product = $model->getDetailedById($id);
                if ($product) {
                    echo json_encode(['success' => true, 'product' => $product]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
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
    'category' => $_GET['category'] ?? '',
    'size' => $_GET['size'] ?? '',
    'stock_status' => $_GET['stock_status'] ?? '',
    'price_min' => $_GET['price_min'] ?? '',
    'price_max' => $_GET['price_max'] ?? ''
];

// Obtener categorías disponibles
$categories = $model->getCategories();
$subCategories = $model->getSubCategories();


// Obtener tallas disponibles
$sizes = $model->getSizes();

// Obtener total de resultados y datos de productos
$total = $model->count($search, $filters);
$products = $model->getAll($limit, $offset, $search, $filters);
// Contar productos activos (status = 'ACTIVE') con los mismos filtros y búsqueda
$activeFilters = $filters;
$activeFilters['status'] = 'ACTIVE';
$productsActives = $model->count($search, $activeFilters);
$totalPages = ceil($total / $limit);
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
                <!-- Header de la página de productos con breadcrumb -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <!-- Título y descripción de la página -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 mb-0.5">Gestión de Productos</h1>
                            <p class="text-xs text-gray-600">Administra y visualiza todos los productos registrados</p>
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
                                <!-- Filtro por Status -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                                    <select name="status" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <option value="ACTIVE" <?= $filters['status'] === 'ACTIVE' ? 'selected' : '' ?>>Activo</option>
                                        <option value="INACTIVE" <?= $filters['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inactivo</option>
                                        <option value="DISCONTINUED" <?= $filters['status'] === 'DISCONTINUED' ? 'selected' : '' ?>>Descontinuado</option>
                                        <option value="OUT_OF_STOCK" <?= $filters['status'] === 'OUT_OF_STOCK' ? 'selected' : '' ?>>Sin Stock</option>
                                        <option value="ON_SALE" <?= $filters['status'] === 'ON_SALE' ? 'selected' : '' ?>>En Oferta</option>
                                    </select>
                                </div>

                                <!-- Filtro por Categoría -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Categoría</label>
                                    <select name="category" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todas</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['name'] ?>" <?= $filters['category'] == $category['name'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Filtro por Talla -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Talla</label>
                                    <select name="size" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todas</option>
                                        <?php foreach ($sizes as $size): ?>
                                            <option value="<?= $size['name'] ?>" <?= $filters['size'] == $size['name'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($size['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Filtro por Stock -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Stock</label>
                                    <select name="stock_status" class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Todos</option>
                                        <option value="in_stock" <?= $filters['stock_status'] === 'in_stock' ? 'selected' : '' ?>>En Stock</option>
                                        <option value="low_stock" <?= $filters['stock_status'] === 'low_stock' ? 'selected' : '' ?>>Stock Bajo</option>
                                        <option value="out_of_stock" <?= $filters['stock_status'] === 'out_of_stock' ? 'selected' : '' ?>>Sin Stock</option>
                                    </select>
                                </div>
                                <!-- Precio mínimo -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Precio mínimo</label>
                                    <input type="number" name="price_min" step="0.01" value="<?= htmlspecialchars($filters['price_min']) ?>"
                                        class="w-full text-xs border border-gray-300 rounded py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>

                                <!-- Precio máximo -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Precio máximo</label>
                                    <input type="number" name="price_max" step="0.01" value="<?= htmlspecialchars($filters['price_max']) ?>"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
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
                                <p class="text-sm font-semibold text-gray-900"><?= $productsActives ?></p>
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

                <!-- Tabla de productos compacta -->
                <div class="bg-white rounded border border-gray-200 overflow-hidden mb-3">
                    <div class="px-3 py-2 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium text-gray-900">Lista de Productos</h3>
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

                    <?php if (empty($products)): ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">No se encontraron productos</h3>
                            <p class="text-xs text-gray-500">
                                <?php if ($search): ?>
                                    No hay productos que coincidan con "<?= htmlspecialchars($search) ?>"
                                <?php else: ?>
                                    No hay productos registrados en el sistema
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Talla</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($products as $product): ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                                #<?= $product['id'] ?>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden">
                                                    <?php if (!empty($product['main_image'])): ?>
                                                        <img src="../../<?= htmlspecialchars($product['main_image']) ?>" 
                                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                                             class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                            <i class="fas fa-image text-gray-400 text-lg"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <!-- Info del producto -->
                                                    <div>
                                                        <div class="text-sm font-semibold text-gray-900 leading-tight">
                                                            <?= htmlspecialchars($product['name']) ?>
                                                        </div>
                                                        <div class="flex flex-wrap gap-x-2 text-xs text-gray-500">
                                                            <span class="inline-flex items-center px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded-full">
                                                                <i class="fas fa-tag mr-1"></i>
                                                                <?= !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Sin categoría' ?>
                                                            </span>
                                                            <span class="inline-flex items-center px-1.5 py-0.5 bg-purple-50 text-purple-700 rounded-full">
                                                                <i class="fas fa-layer-group mr-1"></i>
                                                                <?= !empty($product['product_category_name']) ? htmlspecialchars($product['product_category_name']) : 'Sin Subcategoría' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                                <?= htmlspecialchars($product['size']) ?: 'Sin talla' ?>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-xs font-semibold text-green-600">
                                                S/ <?= number_format($product['price'], 2) ?>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <?php
                                                $stock = (int)$product['stock'];
                                                if ($stock <= 5) {
                                                    $stockClass = 'bg-red-100 text-red-800';
                                                } elseif ($stock <= 20) {
                                                    $stockClass = 'bg-yellow-100 text-yellow-800';
                                                } else {
                                                    $stockClass = 'bg-green-100 text-green-800';
                                                }
                                                ?>
                                                <span class="px-1.5 py-0.5 text-xs font-medium rounded-full <?= $stockClass ?>">
                                                    <?= $stock ?>
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <?php
                                                $status = strtolower($product['status']);
                                                if ($status === 'active') {
                                                    $badgeClass = 'bg-green-100 text-green-800';
                                                } elseif ($status === 'inactive') {
                                                    $badgeClass = 'bg-red-100 text-red-800';
                                                } elseif ($status === 'discontinued') {
                                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                                } elseif ($status === 'out_of_stock') {
                                                    $badgeClass = 'bg-red-100 text-red-800';
                                                } elseif ($status === 'on_sale') {
                                                    $badgeClass = 'bg-purple-100 text-purple-800';
                                                } else {
                                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                                }
                                                ?>
                                                <span class="px-1.5 py-0.5 text-xs font-medium rounded-full <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($product['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium space-x-1">
                                                <button class="action-button text-blue-600 hover:text-blue-900 transition-colors p-1 rounded"
                                                    onclick="openDetailModal(<?= $product['id'] ?>)"
                                                    title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <button class="action-button text-purple-600 hover:text-purple-900 transition-colors p-1 rounded"
                                                    onclick="openImageManagerModal(<?= $product['id'] ?>)"
                                                    title="Gestionar imágenes">
                                                    <i class="fas fa-images"></i>
                                                </button>

                                                <?php
                                                if ($product['status'] != 'INACTIVE') { ?>
                                                    <button class="action-button text-green-600 hover:text-green-900 transition-colors p-1 rounded"
                                                        onclick="openEditModal(<?= $product['id'] ?>)"
                                                        title="Editar producto">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <button
                                                        class="action-button text-red-600 hover:text-red-900 transition-colors p-1 rounded"
                                                        onclick="openDeleteModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>', 0)"
                                                        title="Desactivar producto">
                                                        <i class="fas fa-toggle-off text-secondary"></i>
                                                    </button>
                                                <?php } ?>
                                                <?php if ($product['status'] == 'INACTIVE') { ?>
                                                    <button class="action-button text-green-600 hover:text-green-900 transition-colors p-1 rounded"
                                                        onclick="openDeleteModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>', 1)"
                                                        title="Activar producto">
                                                        <i class="fas fa-toggle-on text-success"></i>
                                                    </button>
                                                <?php } ?>





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
                    if ($filters['category']) $queryParams['category'] = $filters['category'];
                    if ($filters['size']) $queryParams['size'] = $filters['size'];
                    if ($filters['stock_status']) $queryParams['stock_status'] = $filters['stock_status'];

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
    <script src="image_manager.js"></script>
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