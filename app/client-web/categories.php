<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/productModel.php';

use App\Model\ProductModel;

$productModel = new ProductModel($pdo, 'products');

// Obtener las categorías
$categoriesQuery = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Moda y Salud</title>
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
                    <img src="img/tienda.png" alt="Moda y Salud" class="w-12 h-12 rounded-full border-2 border-green-400 shadow">
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

    <!-- Banner de Categorías -->
    <div class="bg-gradient-to-r from-green-600 to-green-500 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">
            <h1 class="text-4xl font-extrabold mb-4 text-center">Nuestras Categorías</h1>
            <p class="text-lg text-green-50 max-w-2xl text-center">
                Explora nuestra selección de productos organizados por categorías para encontrar exactamente lo que necesitas
            </p>
        </div>
    </div>

    <!-- Listado de Categorías -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($categories as $category): 
                // Obtener imagen de la categoría (si existe)
                $stmt = $pdo->prepare("
                    SELECT pi.image_url 
                    FROM products p
                    JOIN product_category_mapping pcm ON p.id = pcm.product_id
                    LEFT JOIN product_images pi ON p.id = pi.product_id
                    WHERE pcm.category_id = ? AND p.status = 'ACTIVE' AND p.deleted_at IS NULL
                    AND pi.image_url IS NOT NULL
                    ORDER BY pi.created_at ASC
                    LIMIT 1
                ");
                $stmt->execute([$category['id']]);
                $categoryImage = $stmt->fetchColumn();
                
                // Obtener conteo de productos
                $stmt = $pdo->prepare("
                    SELECT COUNT(DISTINCT p.id) 
                    FROM products p
                    JOIN product_category_mapping pcm ON p.id = pcm.product_id
                    WHERE pcm.category_id = ? AND p.status = 'ACTIVE' AND p.deleted_at IS NULL
                ");
                $stmt->execute([$category['id']]);
                $productCount = $stmt->fetchColumn();
            ?>
            <a href="category.php?id=<?= $category['id'] ?>" class="block group">
                <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border border-green-100">
                    <div class="h-48 bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center overflow-hidden">
                        <?php if (!empty($categoryImage)): ?>
                            <img src="../<?= htmlspecialchars($categoryImage) ?>" alt="<?= htmlspecialchars($category['name']) ?>" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php else: ?>
                            <i class="fas fa-tags text-green-200 text-6xl"></i>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-green-700 mb-2"><?= htmlspecialchars($category['name']) ?></h2>
                        <p class="text-gray-600 text-sm mb-3"><?= htmlspecialchars($category['description'] ?? 'Explora nuestra selección de productos') ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500"><?= $productCount ?> productos</span>
                            <span class="inline-flex items-center text-green-600 font-medium text-sm">
                                Ver productos <i class="fas fa-arrow-right ml-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
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

    <script src="js/cart.js"></script>
</body>
</html>
