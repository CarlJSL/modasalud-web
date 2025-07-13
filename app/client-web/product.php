<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/productModel.php';

use App\Model\ProductModel;

$productModel = new ProductModel($pdo, 'products');

// Verificar que se proporcionó un ID de producto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$productId = (int)$_GET['id'];

// Obtener producto específico
$product = $productModel->getById($productId);

// Si no existe el producto, redirigir
if (empty($product)) {
    header("Location: index.php");
    exit;
}

// Obtener imágenes adicionales del producto
$imagesQuery = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY created_at ASC");
$imagesQuery->execute([$productId]);
$productImages = $imagesQuery->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos relacionados (de la misma categoría)
$relatedProducts = [];
if (!empty($product['category_id'])) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.description, p.price, p.size, p.stock, pi.image_url as main_image
        FROM products p
        LEFT JOIN product_category_mapping pcm ON p.id = pcm.product_id
        LEFT JOIN (
            SELECT product_id, image_url,
                   ROW_NUMBER() OVER (PARTITION BY product_id ORDER BY created_at ASC) as rn
            FROM product_images
        ) pi ON p.id = pi.product_id AND pi.rn = 1
        WHERE pcm.category_id = ? AND p.id != ? AND p.status = 'ACTIVE'
        LIMIT 4
    ");
    $stmt->execute([$product['category_id'], $productId]);
    $relatedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Moda y Salud</title>
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
                            <?php if (!empty($product['category_name'])): ?>
                                <a href="category.php?id=<?= $product['category_id'] ?>" class="text-gray-700 hover:text-green-600">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-gray-500">Productos</span>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-500"><?= htmlspecialchars($product['name']) ?></span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Detalles del Producto -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6">
                <!-- Imágenes del producto -->
                <div>
                    <!-- Imagen principal -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <?php if (!empty($productImages[0]['image_url'])): ?>
                            <img src="../<?= htmlspecialchars($productImages[0]['image_url']) ?>" 
                                alt="<?= htmlspecialchars($product['name']) ?>" 
                                class="w-full h-96 object-contain" id="mainProductImage">
                        <?php else: ?>
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Miniaturas de imágenes -->
                    <?php if (count($productImages) > 1): ?>
                    <div class="grid grid-cols-4 gap-2">
                        <?php foreach ($productImages as $index => $image): ?>
                            <div class="border rounded-lg overflow-hidden cursor-pointer hover:border-green-500 transition-colors" 
                                 onclick="changeMainImage('<?= htmlspecialchars($image['image_url']) ?>')">
                                <img src="../<?= htmlspecialchars($image['image_url']) ?>" 
                                    alt="Miniatura <?= $index + 1 ?>" 
                                    class="w-full h-24 object-cover">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Información del producto -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                    
                    <div class="flex items-center mb-4">
                        <?php if (!empty($product['category_name'])): ?>
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <?= htmlspecialchars($product['category_name']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($product['product_category_name'])): ?>
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full ml-2">
                                <?= htmlspecialchars($product['product_category_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-4xl font-bold text-gray-900 mb-6">
                        S/ <?= number_format($product['price'], 2) ?>
                    </div>
                    
                    <div class="prose prose-sm text-gray-700 mb-6">
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                    
                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-ruler-combined text-gray-500 mr-2"></i>
                            <span class="text-gray-700">Talla: <strong><?= htmlspecialchars($product['size']) ?></strong></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-box text-gray-500 mr-2"></i>
                            <?php if ($product['stock'] > 10): ?>
                                <span class="text-green-600">En stock (<?= $product['stock'] ?>)</span>
                            <?php elseif ($product['stock'] > 0): ?>
                                <span class="text-orange-600">¡Últimas unidades! (<?= $product['stock'] ?>)</span>
                            <?php else: ?>
                                <span class="text-red-600">Agotado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Controles de cantidad y botón de compra -->
                    <?php if ($product['stock'] > 0): ?>
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="flex items-center border border-gray-300 rounded-md">
                                <button type="button" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-gray-900" onclick="decreaseQuantity()">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantityInput" class="w-16 h-10 text-center border-x border-gray-300" value="1" min="1" max="<?= $product['stock'] ?>">
                                <button type="button" class="w-10 h-10 flex items-center justify-center text-gray-600 hover:text-gray-900" onclick="increaseQuantity(<?= $product['stock'] ?>)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            
                            <button id="addToCartBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-md transition-colors">
                                <i class="fas fa-cart-plus mr-2"></i>Agregar al Carrito
                            </button>
                        </div>
                    <?php else: ?>
                        <button disabled class="w-full bg-gray-300 text-gray-500 cursor-not-allowed font-semibold py-3 px-6 rounded-md mb-6">
                            Producto Agotado
                        </button>
                    <?php endif; ?>
                    
                    <!-- Mensajes de confianza -->
                    <div class="grid grid-cols-2 gap-4 border-t border-gray-200 pt-6">
                        <div class="flex items-start">
                            <div class="mr-3 text-green-500">
                                <i class="fas fa-truck text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm">Envío Rápido</h4>
                                <p class="text-xs text-gray-500">Entrega en 24-48 horas laborables</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="mr-3 text-green-500">
                                <i class="fas fa-shield-alt text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm">Compra Segura</h4>
                                <p class="text-xs text-gray-500">Pago protegido y seguro</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="mr-3 text-green-500">
                                <i class="fas fa-sync text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm">Cambios y Devoluciones</h4>
                                <p class="text-xs text-gray-500">30 días para cambios</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="mr-3 text-green-500">
                                <i class="fas fa-comments text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm">Atención al Cliente</h4>
                                <p class="text-xs text-gray-500">Lun-Vie 9:00-18:00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Productos Relacionados -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Productos Relacionados</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <a href="product.php?id=<?= $relatedProduct['id'] ?>">
                            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                                <?php if (!empty($relatedProduct['main_image'])): ?>
                                    <img src="../<?= htmlspecialchars($relatedProduct['main_image']) ?>" 
                                        alt="<?= htmlspecialchars($relatedProduct['name']) ?>" 
                                        class="w-full h-48 object-cover">
                                <?php else: ?>
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1">
                                <a href="product.php?id=<?= $relatedProduct['id'] ?>" class="hover:text-green-600">
                                    <?= htmlspecialchars($relatedProduct['name']) ?>
                                </a>
                            </h3>
                            <div class="flex items-center justify-between mt-2">
                                <span class="font-bold text-gray-900">S/ <?= number_format($relatedProduct['price'], 2) ?></span>
                                <button onclick="addToCart(<?= $relatedProduct['id'] ?>, '<?= htmlspecialchars($relatedProduct['name']) ?>', <?= $relatedProduct['price'] ?>)" 
                                        class="text-green-600 hover:text-green-700 transition-colors">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
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

    <script src="js/cart.js"></script>
    <script>
        // Funciones para cambio de imagen principal
        function changeMainImage(imagePath) {
            document.getElementById('mainProductImage').src = "../" + imagePath;
        }
        
        // Funciones para el control de cantidad
        function increaseQuantity(maxStock) {
            const input = document.getElementById('quantityInput');
            const currentValue = parseInt(input.value, 10);
            if (currentValue < maxStock) {
                input.value = currentValue + 1;
            }
        }
        
        function decreaseQuantity() {
            const input = document.getElementById('quantityInput');
            const currentValue = parseInt(input.value, 10);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }
        
        // Agregar al carrito desde la página de detalles
        document.getElementById('addToCartBtn').addEventListener('click', function() {
            const quantity = parseInt(document.getElementById('quantityInput').value, 10);
            addToCart(<?= $product['id'] ?>, '<?= addslashes($product['name']) ?>', <?= $product['price'] ?>, quantity);
        });

        // Mostrar la cantidad actual del carrito
        document.addEventListener('DOMContentLoaded', function() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const itemCount = cart.reduce((count, item) => count + item.quantity, 0);
            document.getElementById('cartCount').textContent = itemCount;
        });
    </script>
</body>
</html>
