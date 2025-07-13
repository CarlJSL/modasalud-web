<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/productModel.php';

use App\Model\ProductModel;

$productModel = new ProductModel($pdo, 'products');

// Obtener productos activos para mostrar en la tienda
$filters = ['status' => 'ACTIVE'];
$products = $productModel->getAll(12, 0, '', $filters);

// Obtener categorías para el filtro
$categoriesQuery = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// Obtener subcategorías
$subcategoriesQuery = $pdo->query("SELECT * FROM product_categories ORDER BY name");
$subcategories = $subcategoriesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Moda y Salud</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-green-50 via-emerald-100 to-blue-50 shadow-sm sticky top-0 z-50 border-b border-green-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="img/tienda.png" alt="Moda y Salud" class="w-12 h-12 rounded-full border-2 border-green-400 shadow">
                    <div>
                        <h1 class="text-2xl font-bold text-green-700 tracking-tight">ModaSalud</h1>
                        <span class="text-xs text-gray-500 font-medium">Tu tienda de bienestar y estilo</span>
                    </div>
                </div>

                <!-- Navegación -->
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-green-800 font-semibold hover:text-green-600 transition border-b-2 border-green-500">Inicio</a>
                    <a href="categories.php" class="text-green-800 font-semibold hover:text-green-600 transition">Categorías</a>
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

    <!-- Hero Bar -->
    <section class="bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 text-white py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold mb-6">Bienvenido a Moda & Salud</h1>
            <p class="text-xl mb-8">Descubre los mejores productos de moda y salud con calidad garantizada.</p>
            <a href="#productsGrid" class="bg-white text-green-700 px-8 py-4 rounded-md font-semibold hover:bg-gray-100 transition">Explorar Productos</a>
        </div>
    </section>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Buscador y Filtros -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-filter mr-2 text-green-600"></i> Filtrar Productos
                </h2>
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 mb-4">
                    <div class="flex-1 mb-4 md:mb-0">
                        <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Buscar productos</label>
                        <div class="relative">
                            <input id="searchInput" type="text" placeholder="Buscar por nombre o descripción..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 pr-10">
                            <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 flex-1">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                            <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['id']) ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subcategoría</label>
                            <select id="subcategoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                <option value="">Todas las subcategorías</option>
                                <?php foreach ($subcategories as $subcategory): ?>
                                    <option value="<?= htmlspecialchars($subcategory['id']) ?>">
                                        <?= htmlspecialchars($subcategory['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                            <select id="priceFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                <option value="">Todos los precios</option>
                                <option value="0-50">S/ 0 - S/ 50</option>
                                <option value="50-100">S/ 50 - S/ 100</option>
                                <option value="100-200">S/ 100 - S/ 200</option>
                                <option value="200+">S/ 200+</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Talla</label>
                            <select id="sizeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                <option value="">Todas las tallas</option>
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                                <option value="UNIQUE">UNIQUE</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                            <select id="stockFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500">
                                <option value="all">Todos</option>
                                <option value="in_stock">En stock</option>
                                <option value="low_stock">Stock bajo</option>
                                <option value="out_of_stock">Sin stock</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button id="resetFilters" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors mr-2">
                        <i class="fas fa-undo mr-2"></i>Reiniciar
                    </button>
                    <button id="applyFilters" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                </div>
            </div>
        </div>

        <!-- Título y resumen de resultados -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900"><i class="fas fa-store text-green-600 mr-2"></i>Nuestros Productos</h2>
            <div class="text-sm text-gray-600" id="resultCount">
                <i class="fas fa-box mr-1"></i> Mostrando <span id="currentCount"><?= count($products) ?></span> productos
            </div>
        </div>
        
        <!-- Grid de productos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="productsGrid">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
                    <div class="relative">
                        <a href="product.php?id=<?= $product['id'] ?>">
                            <div class="bg-gray-100 overflow-hidden">
                                <?php if (!empty($product['main_image'])): ?>
                                    <img src="../<?= htmlspecialchars($product['main_image']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>"
                                         class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <?php if ($product['stock'] <= 10 && $product['stock'] > 0): ?>
                            <span class="absolute top-2 left-2 bg-amber-500 text-white text-xs px-2 py-1 rounded-full">¡Stock bajo!</span>
                        <?php elseif ($product['stock'] == 0): ?>
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">Agotado</span>
                        <?php endif; ?>
                        <button onclick="addToWishlist(<?= $product['id'] ?>)" class="absolute top-2 right-2 bg-white text-gray-600 hover:text-red-500 p-2 rounded-full shadow-md hover:shadow-lg transition-all">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button onclick="quickView(<?= $product['id'] ?>)" class="absolute bottom-2 right-2 bg-white text-gray-600 hover:text-blue-500 p-2 rounded-full shadow-md hover:shadow-lg transition-all">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 truncate">
                            <a href="product.php?id=<?= $product['id'] ?>" class="hover:text-green-600 transition-colors">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= htmlspecialchars($product['description']) ?></p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-900">S/ <?= number_format($product['price'], 2) ?></span>
                            <button onclick="addToCart(<?= $product['id'] ?>, '<?= htmlspecialchars(str_replace("'", "\\'", $product['name'])) ?>', <?= $product['price'] ?>)" 
                                    class="<?= $product['stock'] > 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed' ?> text-white px-3 py-2 rounded-md transition-colors">
                                <i class="fas fa-cart-plus mr-2"></i>Agregar
                            </button>
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            <span class="<?= $product['stock'] == 0 ? 'text-red-500' : ($product['stock'] <= 10 ? 'text-amber-500' : 'text-green-600') ?>">
                                <i class="fas <?= $product['stock'] == 0 ? 'fa-times-circle' : ($product['stock'] <= 10 ? 'fa-exclamation-circle' : 'fa-check-circle') ?>"></i>
                                Stock: <?= $product['stock'] ?>
                            </span>
                            <span class="mx-2">|</span>
                            <span><i class="fas fa-ruler-combined mr-1"></i> Talla: <?= htmlspecialchars($product['size']) ?></span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <?php if (!empty($product['product_category_name'])): ?>
                            <span class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                <i class="fas fa-tag text-xs mr-1"></i><?= htmlspecialchars($product['product_category_name']) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($product['category_name'])): ?>
                            <a href="category.php?category=<?= $product['category_name'] ?>" class="inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full hover:bg-green-200 transition-colors">
                                <i class="fas fa-folder text-xs mr-1"></i><?= htmlspecialchars($product['category_name']) ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación -->
        <div class="mt-10 flex justify-center" id="pagination">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button id="prevPage" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Anterior</span>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div id="pageNumbers" class="flex">
                    <!-- Los números de página se generarán con JavaScript -->
                </div>
                <button id="nextPage" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Siguiente</span>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </nav>
        </div>
        
    </main>
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
            <div class="p
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

    <!-- Modal de Vista Rápida de Producto -->
    <div id="quickViewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="fixed inset-0 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900" id="qvProductName">Nombre del Producto</h2>
                    <button onclick="closeQuickView()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <div id="qvImageContainer" class="bg-gray-100 rounded-lg overflow-hidden">
                            <img id="qvProductImage" src="" alt="" class="w-full h-auto object-cover">
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <h3 class="text-sm text-gray-500 mb-1">Precio</h3>
                            <p class="text-3xl font-bold text-gray-900" id="qvProductPrice">S/ 0.00</p>
                        </div>
                        <div class="mb-4">
                            <h3 class="text-sm text-gray-500 mb-1">Descripción</h3>
                            <p class="text-gray-700" id="qvProductDescription">Descripción del producto...</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <h3 class="text-sm text-gray-500 mb-1">Talla</h3>
                                <p id="qvProductSize" class="text-gray-900 font-medium">S</p>
                            </div>
                            <div>
                                <h3 class="text-sm text-gray-500 mb-1">Stock</h3>
                                <p id="qvProductStock" class="text-gray-900 font-medium">10</p>
                            </div>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-sm text-gray-500 mb-1">Categorías</h3>
                            <div class="flex flex-wrap gap-2" id="qvProductCategories">
                                <!-- Categorías se cargan aquí -->
                            </div>
                        </div>
                        <div class="flex space-x-4">
                            <button id="qvAddToCart" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition-colors">
                                <i class="fas fa-cart-plus mr-2"></i>Añadir al Carrito
                            </button>
                            <a id="qvViewDetails" href="#" class="flex-1 bg-gray-100 text-gray-800 px-6 py-3 rounded-md hover:bg-gray-200 transition-colors text-center">
                                <i class="fas fa-info-circle mr-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <script src="js/cart.js"></script>
    <script>
// Variables globales para paginación
let currentPage = 1;
let totalPages = 1;

// Función para renderizar los productos
function renderProducts(products) {
    const grid = document.getElementById('productsGrid');
    
    if (!products.length) {
        grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-12"><i class="fas fa-box-open text-5xl mb-4 opacity-50"></i><p class="text-xl font-medium">No se encontraron productos que coincidan con tu búsqueda.</p><p class="mt-2">Intenta con otros filtros o términos de búsqueda.</p></div>';
        document.getElementById('pagination').style.display = 'none';
        document.getElementById('resultCount').innerHTML = '<i class="fas fa-box mr-1"></i> 0 productos encontrados';
        return;
    }
    
    document.getElementById('resultCount').innerHTML = `<i class="fas fa-box mr-1"></i> Mostrando <span id="currentCount">${products.length}</span> productos`;
    
    grid.innerHTML = products.map(product => `
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
            <div class="relative">
                <a href="product.php?id=${product.id}">
                    <div class="bg-gray-100 overflow-hidden">
                        ${product.main_image ? `<img src="../${product.main_image}" alt="${product.name}" class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300">` : `<div class="w-full h-64 bg-gray-200 flex items-center justify-center"><i class="fas fa-image text-gray-400 text-4xl"></i></div>`}
                    </div>
                </a>
                ${product.stock <= 10 && product.stock > 0 ? 
                    `<span class="absolute top-2 left-2 bg-amber-500 text-white text-xs px-2 py-1 rounded-full">¡Stock bajo!</span>` : 
                    (product.stock == 0 ? 
                        `<span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">Agotado</span>` : 
                        '')}
                <button onclick="addToWishlist(${product.id})" class="absolute top-2 right-2 bg-white text-gray-600 hover:text-red-500 p-2 rounded-full shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-heart"></i>
                </button>
                <button onclick="quickView(${product.id})" class="absolute bottom-2 right-2 bg-white text-gray-600 hover:text-blue-500 p-2 rounded-full shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2 truncate">
                    <a href="product.php?id=${product.id}" class="hover:text-green-600 transition-colors">
                        ${product.name}
                    </a>
                </h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">${product.description || ''}</p>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-gray-900">S/ ${parseFloat(product.price).toFixed(2)}</span>
                    <button onclick="addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price})" 
                            class="${product.stock > 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'} text-white px-3 py-2 rounded-md transition-colors">
                        <i class="fas fa-cart-plus mr-2"></i>Agregar
                    </button>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    <span class="${product.stock == 0 ? 'text-red-500' : (product.stock <= 10 ? 'text-amber-500' : 'text-green-600')}">
                        <i class="fas ${product.stock == 0 ? 'fa-times-circle' : (product.stock <= 10 ? 'fa-exclamation-circle' : 'fa-check-circle')}"></i>
                        Stock: ${product.stock}
                    </span>
                    <span class="mx-2">|</span>
                    <span><i class="fas fa-ruler-combined mr-1"></i> Talla: ${product.size}</span>
                </div>
                <div class="mt-2 flex flex-wrap gap-2">
                    ${product.product_category_name ? 
                        `<span class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                            <i class="fas fa-tag text-xs mr-1"></i>${product.product_category_name}
                        </span>` : ''}
                    ${product.category_name ? 
                        `<a href="category.php?category=${product.category_name}" class="inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full hover:bg-green-200 transition-colors">
                            <i class="fas fa-folder text-xs mr-1"></i>${product.category_name}
                        </a>` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

// Función para renderizar la paginación
function renderPagination(pagination) {
    const pageNumbers = document.getElementById('pageNumbers');
    currentPage = pagination.page;
    totalPages = pagination.pages;
    
    document.getElementById('pagination').style.display = totalPages > 1 ? 'flex' : 'none';
    
    // Habilitar/deshabilitar botones de navegación
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('prevPage').classList.toggle('opacity-50', currentPage === 1);
    document.getElementById('nextPage').disabled = currentPage === totalPages;
    document.getElementById('nextPage').classList.toggle('opacity-50', currentPage === totalPages);

    // Generar enlaces de paginación
    let paginationHTML = '';
    
    // Mostrar máximo 5 páginas a la vez
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }
    
    // Añadir primera página y puntos suspensivos si es necesario
    if (startPage > 1) {
        paginationHTML += `<button class="page-number relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" data-page="1">1</button>`;
        if (startPage > 2) {
            paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`;
        }
    }
    
    // Añadir páginas centrales
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `<button class="page-number relative inline-flex items-center px-4 py-2 border border-gray-300 ${currentPage === i ? 'bg-green-50 text-green-600 font-bold border-green-500' : 'bg-white text-gray-700 hover:bg-gray-50'} text-sm font-medium" data-page="${i}">${i}</button>`;
    }
    
    // Añadir última página y puntos suspensivos si es necesario
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`;
        }
        paginationHTML += `<button class="page-number relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" data-page="${totalPages}">${totalPages}</button>`;
    }
    
    pageNumbers.innerHTML = paginationHTML;
    
    // Añadir event listeners a los números de página
    document.querySelectorAll('.page-number').forEach(button => {
        button.addEventListener('click', () => {
            currentPage = parseInt(button.dataset.page);
            fetchProducts();
        });
    });
}

// Función para mostrar un indicador de carga
function showLoading() {
    const grid = document.getElementById('productsGrid');
    grid.innerHTML = `
        <div class="col-span-full flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
            <span class="ml-3 text-gray-600">Cargando productos...</span>
        </div>
    `;
}

// Función para obtener productos desde la API
function fetchProducts() {
    showLoading();
    
    const search = document.getElementById('searchInput').value.trim();
    const category = document.getElementById('categoryFilter').value;
    const subcategory = document.getElementById('subcategoryFilter').value;
    const price = document.getElementById('priceFilter').value;
    const size = document.getElementById('sizeFilter').value;
    const stock_status = document.getElementById('stockFilter').value;
    const limit = 12; // Productos por página
    
    console.log('Filtros aplicados:', {
        category,
        subcategory,
        price,
        size,
        stock_status
    });
    
    const params = new URLSearchParams({
        search, 
        category, 
        subcategory,
        price, 
        size, 
        stock_status,
        page: currentPage,
        limit
    });
    
    fetch('api/filter-products.php?' + params.toString())
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderProducts(data.products);
                renderPagination(data.pagination);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('productsGrid').innerHTML = `
                <div class="col-span-full text-center text-red-500 py-12">
                    <i class="fas fa-exclamation-triangle text-5xl mb-4"></i>
                    <p class="text-xl font-medium">Error al cargar los productos.</p>
                    <p class="mt-2">Por favor, inténtalo de nuevo más tarde.</p>
                </div>
            `;
        });
}

// Función para añadir a favoritos (implementar luego)
function addToWishlist(productId) {
    console.log('Añadir a favoritos:', productId);
    // Implementar después la funcionalidad
    alert('Funcionalidad de favoritos próximamente disponible');
}

// Función para vista rápida de producto
function quickView(productId) {
    // Mostrar indicador de carga
    const modal = document.getElementById('quickViewModal');
    modal.classList.remove('hidden');
    document.getElementById('qvProductName').innerText = 'Cargando...';
    document.getElementById('qvProductDescription').innerText = 'Cargando información del producto...';
    document.getElementById('qvProductImage').src = '';
    document.getElementById('qvProductImage').alt = '';

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
                const stockElement = document.getElementById('qvProductStock');
                stockElement.innerText = product.stock;
                stockElement.className = 'text-gray-900 font-medium';
                if (product.stock == 0) {
                    stockElement.classList.add('text-red-500');
                } else if (product.stock <= 10) {
                    stockElement.classList.add('text-amber-500');
                } else {
                    stockElement.classList.add('text-green-600');
                }

                // Mostrar imagen
                const imageElement = document.getElementById('qvProductImage');
                if (product.main_image) {
                    imageElement.src = `../${product.main_image}`;
                    imageElement.alt = product.name;
                } else {
                    imageElement.src = '../img/no-image.png';
                    imageElement.alt = 'Sin imagen disponible';
                }

                // Mostrar categorías
                let categoriesHtml = '';
                if (product.product_category_name) {
                    categoriesHtml += `<span class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">
                        <i class="fas fa-tag text-xs mr-1"></i>${product.product_category_name}
                    </span>`;
                }
                if (product.category_name) {
                    categoriesHtml += `<a href="category.php?category=${product.category_name}" class="inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full hover:bg-green-200 transition-colors">
                        <i class="fas fa-folder text-xs mr-1"></i>${product.category_name}
                    </a>`;
                }
                document.getElementById('qvProductCategories').innerHTML = categoriesHtml || 'Sin categorías';

                // Configurar botones
                const addToCartBtn = document.getElementById('qvAddToCart');
                if (product.stock > 0) {
                    addToCartBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    addToCartBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    addToCartBtn.onclick = () => {
                        addToCart(product.id, product.name, product.price);
                        closeQuickView();
                    };
                } else {
                    addToCartBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    addToCartBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    addToCartBtn.onclick = null;
                }

                document.getElementById('qvViewDetails').href = `product.php?id=${product.id}`;
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
    document.getElementById('qvProductImage').src = '';
    document.getElementById('qvProductImage').alt = '';
}

// Cerrar modal al hacer clic fuera
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        closeQuickView();
    }
});

// Eventos de escucha para filtros
document.getElementById('searchInput').addEventListener('input', debounce(function() {
    currentPage = 1; // Reiniciar a la primera página
    fetchProducts();
}, 500));

// Aplicar filtros al hacer clic en el botón
document.getElementById('applyFilters').addEventListener('click', function() {
    currentPage = 1; // Reiniciar a la primera página
    fetchProducts();
});

// Reiniciar filtros
document.getElementById('resetFilters').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('subcategoryFilter').value = '';
    document.getElementById('priceFilter').value = '';
    document.getElementById('sizeFilter').value = '';
    document.getElementById('stockFilter').value = 'all';
    
    currentPage = 1; // Reiniciar a la primera página
    fetchProducts();
});

// Navegar a la página anterior
document.getElementById('prevPage').addEventListener('click', function() {
    if (currentPage > 1) {
        currentPage--;
        fetchProducts();
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

// Navegar a la página siguiente
document.getElementById('nextPage').addEventListener('click', function() {
    if (currentPage < totalPages) {
        currentPage++;
        fetchProducts();
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

// Función debounce para retrasar la búsqueda mientras se escribe
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

// Cargar productos al iniciar
document.addEventListener('DOMContentLoaded', function() {
    fetchProducts();
});
</script>
</body>
</html>
