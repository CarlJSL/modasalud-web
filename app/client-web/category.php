<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/productModel.php';
require_once __DIR__ . '/../dashboard-web/model/categoriesModel.php';

use App\Model\ProductModel;
use App\Model\CategoriesModel;

$productModel = new ProductModel($pdo, 'products');
$categoriesModel = new CategoriesModel($pdo);

// Verificar que se proporcionó un ID de categoría
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: categories.php");
    exit;
}

$categoryId = (int)$_GET['id'];

// Obtener información de la categoría
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: categories.php");
    exit;
}

// Obtener subcategorías de esta categoría que tienen productos activos
$stmt = $pdo->prepare("
    SELECT DISTINCT pc.* 
    FROM product_categories pc
    JOIN product_category_mapping pcm ON pc.id = pcm.product_category_id
    JOIN products p ON p.id = pcm.product_id
    WHERE pcm.category_id = ? AND p.status = 'ACTIVE' AND p.deleted_at IS NULL
    ORDER BY pc.name ASC
");
$stmt->execute([$categoryId]);
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si hay un filtro de subcategoría
$subcategoryFilter = null;
if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
    $subcategoryFilter = (int)$_GET['subcategory'];
}

// Configuración de paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Filtros para productos
$filters = ['status' => 'ACTIVE'];

// Agregar filtro personalizado para categoría por ID
$filters['_custom_category_filter'] = "pcm.category_id = $categoryId";

// Si hay un filtro de subcategoría, aplicarlo
if ($subcategoryFilter) {
    $filters['_custom_subcategory_filter'] = "pcm.product_category_id = $subcategoryFilter";
}

// Obtener productos usando ProductModel
// Agregar más filtros si se proporcionan en la URL
if (isset($_GET['price']) && $_GET['price']) {
    $price = $_GET['price'];
    switch ($price) {
        case '0-50':
            $filters['price_min'] = 0;
            $filters['price_max'] = 50;
            break;
        case '50-100':
            $filters['price_min'] = 50;
            $filters['price_max'] = 100;
            break;
        case '100-200':
            $filters['price_min'] = 100;
            $filters['price_max'] = 200;
            break;
        case '200+':
            $filters['price_min'] = 200;
            break;
    }
}

if (isset($_GET['size']) && $_GET['size']) {
    $filters['size'] = $_GET['size'];
}

if (isset($_GET['stock_status']) && $_GET['stock_status'] != 'all') {
    $filters['stock_status'] = $_GET['stock_status'];
}

// Obtener productos usando ProductModel
$search = isset($_GET['search']) ? $_GET['search'] : '';
$products = $productModel->getAll($limit, $offset, $search, $filters);

// Obtener conteo total para paginación
$totalProducts = $productModel->count($search, $filters);
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['name']) ?> - Moda y Salud</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-green-50 via-emerald-100 to-blue-50 shadow-sm sticky top-0 z-50 border-b border-green-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo y empresa -->
                <div class="flex items-center gap-3">
                    <img src="../img/tienda.png" alt="Moda y Salud" class="w-12 h-12 rounded-full border-2 border-green-400 shadow">
                    <div>
                        <h1 class="text-2xl font-bold text-green-700 tracking-tight">Moda & Salud</h1>
                        <span class="text-xs text-gray-500 font-medium">Tu tienda de bienestar y estilo</span>
                    </div>
                </div>
                <!-- Navegación -->
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-green-800 font-semibold hover:text-green-600 transition">Inicio</a>
                    <a href="categories.php" class="text-green-800 font-semibold hover:text-green-600 transition border-b-2 border-green-500">Categorías</a>
                    <a href="about.php" class="text-green-800 font-semibold hover:text-green-600 transition">Nosotros</a>
                    <a href="contact.php" class="text-green-800 font-semibold hover:text-green-600 transition">Contacto</a>
                </nav>
                <!-- Carrito -->
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="relative p-2 text-green-800 hover:text-green-600 transition">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                        <span id="cartCount" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">0</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero de la categoría -->
    <div class="bg-gradient-to-r from-blue-500 to-green-500 py-12 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-white mb-4"><?= htmlspecialchars($category['name']) ?></h1>
                <?php if (!empty($category['description'])): ?>
                <p class="text-lg text-white opacity-90 max-w-3xl mx-auto"><?= htmlspecialchars($category['description']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Navegación de migas de pan -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="index.php" class="text-gray-700 hover:text-green-600">
                            <i class="fas fa-home mr-2"></i>Inicio
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="categories.php" class="text-gray-700 hover:text-green-600">
                                Categorías
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-500"><?= htmlspecialchars($category['name']) ?></span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Filtros adicionales -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-filter mr-2 text-green-600"></i> Filtrar Productos
                </h2>
                <form method="get" action="category.php">
                    <input type="hidden" name="id" value="<?= $categoryId ?>">
                    <?php if ($subcategoryFilter): ?>
                    <input type="hidden" name="subcategory" value="<?= $subcategoryFilter ?>">
                    <?php endif; ?>
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 mb-4">
                        <div class="flex-1 mb-4 md:mb-0">
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Buscar productos</label>
                            <div class="relative">
                                <input id="searchInput" name="search" type="text" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" placeholder="Buscar por nombre o descripción..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 pr-10">
                                <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 flex-1">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                                <select name="price" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                    <option value="">Cualquier precio</option>
                                    <option value="0-50" <?= isset($_GET['price']) && $_GET['price'] == '0-50' ? 'selected' : '' ?>>S/ 0 - S/ 50</option>
                                    <option value="50-100" <?= isset($_GET['price']) && $_GET['price'] == '50-100' ? 'selected' : '' ?>>S/ 50 - S/ 100</option>
                                    <option value="100-200" <?= isset($_GET['price']) && $_GET['price'] == '100-200' ? 'selected' : '' ?>>S/ 100 - S/ 200</option>
                                    <option value="200+" <?= isset($_GET['price']) && $_GET['price'] == '200+' ? 'selected' : '' ?>>Más de S/ 200</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Talla</label>
                                <select name="size" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                    <option value="">Todas las tallas</option>
                                    <option value="S" <?= isset($_GET['size']) && $_GET['size'] == 'S' ? 'selected' : '' ?>>S</option>
                                    <option value="M" <?= isset($_GET['size']) && $_GET['size'] == 'M' ? 'selected' : '' ?>>M</option>
                                    <option value="L" <?= isset($_GET['size']) && $_GET['size'] == 'L' ? 'selected' : '' ?>>L</option>
                                    <option value="XL" <?= isset($_GET['size']) && $_GET['size'] == 'XL' ? 'selected' : '' ?>>XL</option>
                                    <option value="XXL" <?= isset($_GET['size']) && $_GET['size'] == 'XXL' ? 'selected' : '' ?>>XXL</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                                <select name="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                    <option value="all">Todos los productos</option>
                                    <option value="in_stock" <?= isset($_GET['stock_status']) && $_GET['stock_status'] == 'in_stock' ? 'selected' : '' ?>>En stock</option>
                                    <option value="low_stock" <?= isset($_GET['stock_status']) && $_GET['stock_status'] == 'low_stock' ? 'selected' : '' ?>>Stock bajo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <a href="category.php?id=<?= $categoryId ?><?= $subcategoryFilter ? '&subcategory=' . $subcategoryFilter : '' ?>" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-undo mr-2"></i>Reiniciar
                        </a>
                        <button type="submit" class="px-6 py-2 bg-green-600 rounded-md text-white hover:bg-green-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Subcategorías -->
        <?php if (!empty($subcategories)): ?>
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Subcategorías</h2>
            <div class="flex flex-wrap gap-2">
                <a href="category.php?id=<?= $categoryId ?>" class="px-4 py-2 rounded-full text-sm <?= is_null($subcategoryFilter) ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' ?> transition-colors">
                    Todas
                </a>
                <?php foreach ($subcategories as $subcategory): ?>
                <a href="category.php?id=<?= $categoryId ?>&subcategory=<?= $subcategory['id'] ?>" class="px-4 py-2 rounded-full text-sm <?= $subcategoryFilter == $subcategory['id'] ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' ?> transition-colors">
                    <?= htmlspecialchars($subcategory['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Productos -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Productos de <?= htmlspecialchars($category['name']) ?></h2>
            
            <?php if (empty($products)): ?>
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <i class="fas fa-box-open text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">No hay productos disponibles</h3>
                <p class="text-gray-500 mb-6">No se encontraron productos en esta categoría<?= $subcategoryFilter ? ' con el filtro seleccionado' : '' ?>.</p>
                <a href="categories.php" class="inline-block bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
                    Ver Otras Categorías
                </a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="relative group">
                        <a href="product.php?id=<?= $product['id'] ?>">
                            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                                <?php if (!empty($product['main_image'])): ?>
                                    <img src="../<?= htmlspecialchars($product['main_image']) ?>" 
                                        alt="<?= htmlspecialchars($product['name']) ?>" 
                                        class="w-full h-64 object-cover">
                                <?php else: ?>
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="quickView(<?= $product['id'] ?>)" 
                                    class="bg-white text-gray-800 px-4 py-2 rounded-md hover:bg-gray-100 transition-colors mx-2">
                                <i class="fas fa-eye mr-2"></i>Vista Rápida
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            <a href="product.php?id=<?= $product['id'] ?>" class="hover:text-green-600">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= htmlspecialchars($product['description']) ?></p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-900">S/ <?= number_format($product['price'], 2) ?></span>
                            <button onclick="addToCart(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['price'] ?>)" 
                                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                <i class="fas fa-cart-plus mr-2"></i>Agregar
                            </button>
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            <span>Stock: <?= $product['stock'] ?></span>
                            <span class="mx-2">|</span>
                            <span>Talla: <?= htmlspecialchars($product['size']) ?></span>
                        </div>
                        <?php if (!empty($product['product_category_name'])): ?>
                        <span class="mt-2 inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                            <?= htmlspecialchars($product['product_category_name']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Paginación -->
        <div class="mb-8">
            <nav aria-label="Page navigation">
                <ul class="inline-flex items-center -space-x-px">
                    <li>
                        <a href="?id=<?= $categoryId ?>&page=1&subcategory=<?= $subcategoryFilter ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['price']) ? '&price='.urlencode($_GET['price']) : '' ?><?= isset($_GET['size']) ? '&size='.urlencode($_GET['size']) : '' ?><?= isset($_GET['stock_status']) ? '&stock_status='.urlencode($_GET['stock_status']) : '' ?>" class="px-3 py-2 rounded-l-md <?= $page == 1 ? 'bg-gray-200 text-gray-500' : 'bg-white text-green-600 hover:bg-gray-100' ?> transition-colors">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li>
                        <a href="?id=<?= $categoryId ?>&page=<?= $i ?>&subcategory=<?= $subcategoryFilter ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['price']) ? '&price='.urlencode($_GET['price']) : '' ?><?= isset($_GET['size']) ? '&size='.urlencode($_GET['size']) : '' ?><?= isset($_GET['stock_status']) ? '&stock_status='.urlencode($_GET['stock_status']) : '' ?>" class="px-3 py-2 <?= $page == $i ? 'bg-green-600 text-white' : 'bg-white text-green-600 hover:bg-gray-100' ?> transition-colors">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    <li>
                        <a href="?id=<?= $categoryId ?>&page=<?= $totalPages ?>&subcategory=<?= $subcategoryFilter ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['price']) ? '&price='.urlencode($_GET['price']) : '' ?><?= isset($_GET['size']) ? '&size='.urlencode($_GET['size']) : '' ?><?= isset($_GET['stock_status']) ? '&stock_status='.urlencode($_GET['stock_status']) : '' ?>" class="px-3 py-2 rounded-r-md <?= $page == $totalPages ? 'bg-gray-200 text-gray-500' : 'bg-white text-green-600 hover:bg-gray-100' ?> transition-colors">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Más categorías -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Otras Categorías</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php 
                $otherCategoriesQuery = $pdo->prepare("
                    SELECT 
                        c.id,
                        c.name,
                        c.description,
                        COUNT(DISTINCT p.id) AS product_count
                    FROM categories c
                    LEFT JOIN product_category_mapping pcm ON c.id = pcm.category_id
                    JOIN products p ON p.id = pcm.product_id
                    WHERE c.id != ? AND p.status = 'ACTIVE' AND p.deleted_at IS NULL
                    GROUP BY c.id, c.name, c.description
                    HAVING COUNT(DISTINCT p.id) > 0
                    ORDER BY product_count DESC
                    LIMIT 3
                ");
                $otherCategoriesQuery->execute([$categoryId]);
                $otherCategories = $otherCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($otherCategories as $otherCategory):
                ?>
                <a href="category.php?id=<?= $otherCategory['id'] ?>" class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
                    <div class="h-40 bg-gradient-to-r from-blue-500 to-green-500 flex items-center justify-center text-white p-6 group-hover:from-green-500 group-hover:to-blue-500 transition-all">
                        <div class="text-center">
                            <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($otherCategory['name']) ?></h3>
                            <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                                <?= $otherCategory['product_count'] ?> Producto<?= $otherCategory['product_count'] != 1 ? 's' : '' ?>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Moda & Salud</h3>
                    <p class="text-gray-400">Tu tienda de confianza para productos de moda y salud. Calidad y servicio garantizado.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition">Inicio</a></li>
                        <li><a href="categories.php" class="text-gray-400 hover:text-white transition">Categorías</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition">Nosotros</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-white transition">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                            <span>Av. Principal 123, Lima, Perú</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>(01) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>info@modaysalud.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-4 border-t border-gray-700 text-center text-gray-400 text-sm">
                <p>&copy; <?= date('Y') ?> Moda & Salud. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Modal del Carrito -->
    <div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold">Carrito de Compras</h2>
                    <button onclick="closeCart()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="cartItems" class="space-y-4 mb-6">
                    <!-- Items del carrito se cargan aquí -->
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between text-xl font-semibold mb-4">
                        <span>Total:</span>
                        <span id="cartTotal">S/ 0.00</span>
                    </div>
                    <button onclick="proceedToCheckout()" class="w-full bg-green-600 text-white py-3 rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>Proceder al Pago
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Vista Rápida -->
    <div id="quickViewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-xl overflow-hidden">
            <div class="flex">
                <!-- Imagen del producto -->
                <div class="w-1/2 bg-gray-100 p-6 flex items-center justify-center">
                    <div id="qvProductImage" class="w-full h-80 flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin text-green-600 text-4xl"></i>
                    </div>
                </div>
                <!-- Detalles del producto -->
                <div class="w-1/2 p-8">
                    <div class="flex justify-between items-start">
                        <h3 id="qvProductName" class="text-2xl font-bold text-gray-900">Cargando...</h3>
                        <button onclick="closeQuickView()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div id="qvProductPrice" class="text-3xl font-bold text-green-600 my-4">S/ 0.00</div>
                    
                    <div class="mb-6">
                        <p id="qvProductDescription" class="text-gray-600 mb-4">Cargando información del producto...</p>
                        
                        <div class="flex space-x-4 mb-2">
                            <div class="text-gray-700">
                                <span class="font-medium">Talla:</span> 
                                <span id="qvProductSize">-</span>
                            </div>
                            <div class="text-gray-700">
                                <span class="font-medium">Stock:</span> 
                                <span id="qvProductStock">-</span>
                            </div>
                        </div>
                        
                        <div id="qvProductCategory" class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">-</div>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button id="qvAddToCartBtn" onclick="" class="flex-1 bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-cart-plus mr-2"></i>Añadir al Carrito
                        </button>
                        <a id="qvViewDetailBtn" href="#" class="bg-gray-200 text-gray-800 py-3 px-4 rounded-md hover:bg-gray-300 transition-colors flex items-center justify-center">
                            <i class="fas fa-eye mr-2"></i>Ver Detalle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/cart.js"></script>
    <script>
        // Mostrar la cantidad actual del carrito
        document.addEventListener('DOMContentLoaded', function() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const itemCount = cart.reduce((count, item) => count + item.quantity, 0);
            document.getElementById('cartCount').textContent = itemCount;
        });

        // Función para vista rápida de producto
        function quickView(productId) {
            // Mostrar indicador de carga
            const modal = document.getElementById('quickViewModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex', 'items-center', 'justify-center');
            document.getElementById('qvProductName').innerText = 'Cargando...';
            document.getElementById('qvProductDescription').innerText = 'Cargando información del producto...';
            
            // Obtener detalles del producto desde la API
            fetch(`api/get-product.php?id=${productId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.product) {
                        const product = data.product;
                        
                        // Actualizar datos en el modal
                        document.getElementById('qvProductName').innerText = product.name;
                        document.getElementById('qvProductDescription').innerText = product.description || 'Sin descripción disponible';
                        document.getElementById('qvProductPrice').innerText = `S/ ${parseFloat(product.price).toFixed(2)}`;
                        document.getElementById('qvProductSize').innerText = product.size;
                        
                        // Mostrar stock con color según disponibilidad
                        let stockElement = document.getElementById('qvProductStock');
                        if (product.stock > 10) {
                            stockElement.innerHTML = `<span class="text-green-600">${product.stock} disponibles</span>`;
                        } else if (product.stock > 0) {
                            stockElement.innerHTML = `<span class="text-yellow-600">Solo ${product.stock} disponibles</span>`;
                        } else {
                            stockElement.innerHTML = '<span class="text-red-600">Sin stock</span>';
                        }
                        
                        // Mostrar categoría
                        if (product.product_category_name) {
                            document.getElementById('qvProductCategory').innerText = product.product_category_name;
                            document.getElementById('qvProductCategory').classList.remove('hidden');
                        } else {
                            document.getElementById('qvProductCategory').classList.add('hidden');
                        }
                        
                        // Configurar botón de añadir al carrito
                        const addToCartBtn = document.getElementById('qvAddToCartBtn');
                        addToCartBtn.onclick = function() {
                            addToCart(product.id, product.name, product.price);
                        };
                        
                        // Actualizar enlace para ver detalle
                        document.getElementById('qvViewDetailBtn').href = `product.php?id=${product.id}`;
                        
                        // Mostrar imagen si existe
                        const imgContainer = document.getElementById('qvProductImage');
                        if (product.main_image) {
                            imgContainer.innerHTML = `<img src="../${product.main_image}" alt="${product.name}" class="max-h-full max-w-full object-contain">`;
                        } else {
                            imgContainer.innerHTML = '<div class="w-full h-full flex items-center justify-center bg-gray-200"><i class="fas fa-image text-gray-400 text-5xl"></i></div>';
                        }
                    } else {
                        console.error('Error al cargar el producto');
                        closeQuickView();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    closeQuickView();
                });
        }

        // Función para cerrar el modal de vista rápida
        function closeQuickView() {
            const modal = document.getElementById('quickViewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex', 'items-center', 'justify-center');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('quickViewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuickView();
            }
        });
    </script>
</body>
</html>
