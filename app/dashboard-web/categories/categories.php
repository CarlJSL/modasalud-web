<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php'); // o ../index.php dependiendo del nivel de carpeta
    exit();
}

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/categoriesModel.php';

use App\Model\CategoriesModel;

// Instancia del modelo
$model = new CategoriesModel($pdo);

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        $type = $_POST['type'] ?? $_GET['type'] ?? 'category'; // category o subcategory

        switch ($action) {
            case 'create':
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'])
                ];

                // Validaciones
                $errors = [];
                if (empty($data['name'])) {
                    $errors['name'] = $type === 'category' ? 'El nombre de la categoría es requerido' : 'El nombre de la subcategoría es requerido';
                }

                // Verificar si el nombre ya existe
                if ($type === 'category') {
                    if ($model->categoryNameExists($data['name'])) {
                        $errors['name'] = 'Este nombre de categoría ya está en uso';
                    }
                } else {
                    if ($model->subcategoryNameExists($data['name'])) {
                        $errors['name'] = 'Este nombre de subcategoría ya está en uso';
                    }
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                if ($type === 'category') {
                    $result = $model->createCategory($data);
                    $message = 'Categoría creada exitosamente';
                } else {
                    $result = $model->createSubcategory($data);
                    $message = 'Subcategoría creada exitosamente';
                }

                echo json_encode(['success' => true, 'message' => $message]);
                break;

            case 'update':
                $id = (int)$_POST['id'];
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'])
                ];

                // Validaciones
                $errors = [];
                if (empty($data['name'])) {
                    $errors['name'] = $type === 'category' ? 'El nombre de la categoría es requerido' : 'El nombre de la subcategoría es requerido';
                }

                // Verificar si el nombre ya existe (excluyendo el registro actual)
                if ($type === 'category') {
                    if ($model->categoryNameExists($data['name'], $id)) {
                        $errors['name'] = 'Este nombre de categoría ya está en uso';
                    }
                } else {
                    if ($model->subcategoryNameExists($data['name'], $id)) {
                        $errors['name'] = 'Este nombre de subcategoría ya está en uso';
                    }
                }

                if (!empty($errors)) {
                    echo json_encode(['success' => false, 'message' => 'Errores de validación', 'errors' => $errors]);
                    exit;
                }

                // Verificar si hay cambios reales comparando con los datos actuales
                if ($type === 'category') {
                    $current = $model->getCategoryById($id);
                    $message = 'Categoría actualizada exitosamente';
                } else {
                    $current = $model->getSubcategoryById($id);
                    $message = 'Subcategoría actualizada exitosamente';
                }

                if (!$current) {
                    echo json_encode(['success' => false, 'message' => $type === 'category' ? 'Categoría no encontrada' : 'Subcategoría no encontrada']);
                    exit;
                }

                // Comparar datos actuales con los nuevos
                $hasChanges = false;
                $fieldsToCheck = ['name', 'description'];

                foreach ($fieldsToCheck as $field) {
                    if ($data[$field] != $current[$field]) {
                        $hasChanges = true;
                        break;
                    }
                }

                // Si no hay cambios, retornar mensaje informativo
                if (!$hasChanges) {
                    echo json_encode(['success' => false, 'message' => 'No se han detectado cambios para actualizar', 'no_changes' => true]);
                    exit;
                }

                if ($type === 'category') {
                    $result = $model->updateCategory($id, $data);
                } else {
                    $result = $model->updateSubcategory($id, $data);
                }

                echo json_encode(['success' => true, 'message' => $message]);
                break;

            case 'delete':
                $id = (int)($_POST['id'] ?? $_GET['id']);

                try {
                    if ($type === 'category') {
                        $result = $model->deleteCategory($id);
                        $message = 'Categoría eliminada exitosamente';
                    } else {
                        $result = $model->deleteSubcategory($id);
                        $message = 'Subcategoría eliminada exitosamente';
                    }
                    echo json_encode(['success' => true, 'message' => $message]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'get':
                $id = (int)$_GET['id'];
                if ($type === 'category') {
                    $item = $model->getCategoryById($id);
                } else {
                    $item = $model->getSubcategoryById($id);
                }

                if ($item) {
                    echo json_encode(['success' => true, 'item' => $item]);
                } else {
                    echo json_encode(['success' => false, 'message' => $type === 'category' ? 'Categoría no encontrada' : 'Subcategoría no encontrada']);
                }
                break;

            case 'details':
                $id = (int)$_GET['id'];
                if ($type === 'category') {
                    $item = $model->getCategoryDetailedById($id);
                } else {
                    $item = $model->getSubcategoryDetailedById($id);
                }

                if ($item) {
                    echo json_encode(['success' => true, 'item' => $item]);
                } else {
                    echo json_encode(['success' => false, 'message' => $type === 'category' ? 'Categoría no encontrada' : 'Subcategoría no encontrada']);
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
$activeTab = $_GET['tab'] ?? 'categories'; // categories o subcategories

// Filtros
$filters = [];

// Obtener datos según la pestaña activa
if ($activeTab === 'subcategories') {
    $total = $model->countSubcategories($search, $filters);
    $items = $model->getAllSubcategories($limit, $offset, $search, $filters);
} else {
    $total = $model->countCategories($search, $filters);
    $items = $model->getAllCategories($limit, $offset, $search, $filters);
}

$totalPages = ceil($total / $limit);

// Obtener estadísticas
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
                <!-- Header de la página de categorías con breadcrumb -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <!-- Título y descripción de la página -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 mb-0.5">Gestión de Categorías</h1>
                            <p class="text-xs text-gray-600">Administra categorías y subcategorías del sistema</p>
                        </div>
                    </div>
                </div>

                <!-- Pestañas de navegación -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <div class="flex space-x-1">
                        <a href="?tab=categories"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors <?= $activeTab === 'categories' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' ?>">
                            <i class="fas fa-folder mr-1"></i>
                            Categorías
                        </a>
                        <a href="?tab=subcategories"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors <?= $activeTab === 'subcategories' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' ?>">
                            <i class="fas fa-folder-open mr-1"></i>
                            Subcategorías
                        </a>
                    </div>
                </div>

                <!-- Formulario de búsqueda compacto -->
                <div class="bg-white rounded border border-gray-200 p-2 mb-2">
                    <form method="get" class="flex flex-col md:flex-row md:items-end space-y-2 md:space-y-0 md:space-x-2">
                        <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>">
                        <div class="flex-1">
                            <input type="text"
                                name="search"
                                value="<?= htmlspecialchars($search) ?>"
                                placeholder="Buscar por nombre o descripción..."
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="flex flex-row space-x-1">
                            <button type="submit"
                                class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-search mr-1"></i>Buscar
                            </button>
                            <button type="button"
                                onclick="openCreateModal('<?= $activeTab === 'subcategories' ? 'subcategory' : 'category' ?>')"
                                class="px-3 py-1.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-plus mr-1"></i>Nuevo
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Estadísticas compactas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2">
                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-blue-400 to-blue-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-folder text-white text-xs"></i>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900"><?= $stats['total_categories'] ?></div>
                                <div class="text-xs text-gray-500">Total Categorías</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-green-400 to-green-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-folder-open text-white text-xs"></i>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900"><?= $stats['total_subcategories'] ?></div>
                                <div class="text-xs text-gray-500">Total Subcategorías</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-purple-400 to-purple-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900"><?= $stats['categories_with_products'] ?></div>
                                <div class="text-xs text-gray-500">Cat. con Productos</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded border border-gray-200 p-2">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-gradient-to-br from-orange-400 to-orange-500 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-check-double text-white text-xs"></i>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900"><?= $stats['subcategories_with_products'] ?></div>
                                <div class="text-xs text-gray-500">Sub. con Productos</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla compacta -->
                <div class="bg-white rounded border border-gray-200 overflow-hidden mb-3">
                    <div class="px-3 py-2 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-medium text-gray-900">
                                <?= $activeTab === 'subcategories' ? 'Subcategorías' : 'Categorías' ?>
                                (<?= $total ?> registros)
                            </h3>
                        </div>
                    </div>

                    <?php if (empty($items)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-folder text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500 text-sm">No se encontraron <?= $activeTab === 'subcategories' ? 'subcategorías' : 'categorías' ?></p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descripción
                                        </th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Productos
                                        </th>
                                        <?php if ($activeTab === 'subcategories'): ?>
                                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Creado
                                            </th>
                                        <?php endif; ?>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($items as $item): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">
                                                #<?= $item['id'] ?>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="text-xs font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="text-xs text-gray-600 max-w-xs truncate">
                                                    <?= htmlspecialchars($item['description'] ?? 'Sin descripción') ?>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $item['product_count'] > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                                    <?= $item['product_count'] ?> productos
                                                </span>
                                            </td>
                                            <?php if ($activeTab === 'subcategories'): ?>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                                    <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                                                </td>
                                            <?php endif; ?>
                                            <td class="px-3 py-2 whitespace-nowrap text-center text-xs font-medium">
                                                <div class="flex justify-center space-x-1">
                                                    <button onclick="openDetailModal(<?= $item['id'] ?>, '<?= $activeTab === 'subcategories' ? 'subcategory' : 'category' ?>')"
                                                        class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                                        title="Ver detalles">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </button>
                                                    <button onclick="openEditModal(<?= $item['id'] ?>, '<?= $activeTab === 'subcategories' ? 'subcategory' : 'category' ?>')"
                                                        class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50"
                                                        title="Editar">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    <button onclick="openDeleteModal(<?= $item['id'] ?>, '<?= htmlspecialchars($item['name']) ?>', '<?= $activeTab === 'subcategories' ? 'subcategory' : 'category' ?>')"
                                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                                                        title="Eliminar">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
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
                    $queryParams['tab'] = $activeTab;

                    function buildUrl($queryParams, $page)
                    {
                        $params = $queryParams;
                        $params['page'] = $page;
                        return '?' . http_build_query($params);
                    }
                    ?>
                    <div class="bg-white rounded border border-gray-200 px-3 py-2">
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-700">
                                Mostrando <?= (($page - 1) * $limit) + 1 ?> - <?= min($page * $limit, $total) ?> de <?= $total ?> resultados
                            </div>
                            <div class="flex items-center space-x-1">
                                <?php if ($page > 1): ?>
                                    <a href="<?= buildUrl($queryParams, $page - 1) ?>"
                                        class="px-2 py-1 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($totalPages, $page + 2);
                                ?>

                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <?php if ($i === $page): ?>
                                        <span class="px-2 py-1 text-xs font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                                            <?= $i ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="<?= buildUrl($queryParams, $i) ?>"
                                            class="px-2 py-1 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="<?= buildUrl($queryParams, $page + 1) ?>"
                                        class="px-2 py-1 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        <i class="fas fa-chevron-right"></i>
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
                hasActiveFilters: <?= json_encode(!empty($search)) ?>
            });
        });
    </script>

</body>

</html>