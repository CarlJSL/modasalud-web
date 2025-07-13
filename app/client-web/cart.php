<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';

// Esta página mostrará el contenido del carrito y permitirá proceder al checkout
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Moda y Salud</title>
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
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Tu Carrito de Compras</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Lista de productos en el carrito -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="hidden md:grid grid-cols-12 text-sm font-semibold text-gray-700 pb-4 border-b">
                            <div class="col-span-6">Producto</div>
                            <div class="col-span-2 text-center">Precio</div>
                            <div class="col-span-2 text-center">Cantidad</div>
                            <div class="col-span-2 text-right">Subtotal</div>
                        </div>
                        
                        <div id="cartItemsList" class="divide-y divide-gray-200">
                            <!-- Los elementos del carrito se cargan aquí con JavaScript -->
                            <div class="py-16 text-center text-gray-500" id="emptyCartMessage">
                                <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                                <h3 class="text-xl font-semibold mb-2">Tu carrito está vacío</h3>
                                <p class="mb-6">¡Agrega algunos productos para comenzar!</p>
                                <a href="index.php" class="inline-block bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>Explorar Productos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-between mt-6 items-center">
                    <a href="index.php" class="inline-flex items-center text-green-600 hover:text-green-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Seguir Comprando
                    </a>
                    
                    <button id="clearCartBtn" class="inline-flex items-center text-red-600 hover:text-red-800 border border-red-600 rounded-md px-4 py-2 hover:bg-red-50 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Vaciar Carrito
                    </button>
                </div>
            </div>
            
            <!-- Resumen del pedido -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden sticky top-24">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumen del Pedido</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal</span>
                                <span id="cartSubtotal">S/ 0.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Envío</span>
                                <span id="cartShipping">Gratis</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between font-semibold text-lg">
                                <span>Total</span>
                                <span id="cartTotal">S/ 0.00</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Incluye impuestos</p>
                        </div>
                        
                        <button id="checkoutBtn" class="w-full bg-green-600 text-white font-semibold py-3 rounded-md hover:bg-green-700 transition-colors disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed">
                            <i class="fas fa-credit-card mr-2"></i>Proceder al Pago
                        </button>
                    </div>
                    
                    <!-- Métodos de pago aceptados -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500 mb-2">Aceptamos:</p>
                        <div class="flex space-x-3">
                            <i class="fab fa-cc-visa text-2xl text-blue-700"></i>
                            <i class="fab fa-cc-mastercard text-2xl text-red-600"></i>
                            <i class="fab fa-cc-amex text-2xl text-blue-500"></i>
                            <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>
                        </div>
                    </div>
                    
                    <!-- Políticas -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-start space-x-2 mb-2">
                            <i class="fas fa-shield-alt text-green-600 mt-1"></i>
                            <p class="text-xs text-gray-600">Pagos seguros y protegidos con cifrado SSL</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-exchange-alt text-green-600 mt-1"></i>
                            <p class="text-xs text-gray-600">30 días para cambios y devoluciones</p>
                        </div>
                    </div>
                </div>
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

    <script src="js/cart.js"></script>
    <script>
        // Renderizar carrito
        function renderCart() {
            const cartItems = JSON.parse(localStorage.getItem('cart')) || [];
            const cartItemsList = document.getElementById('cartItemsList');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const clearCartBtn = document.getElementById('clearCartBtn');
            
            // Actualizar contador
            document.getElementById('cartCount').textContent = cartItems.reduce((count, item) => count + item.quantity, 0);
            
            // Mostrar mensaje si el carrito está vacío
            if (cartItems.length === 0) {
                emptyCartMessage.classList.remove('hidden');
                checkoutBtn.disabled = true;
                clearCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
                document.getElementById('cartSubtotal').textContent = 'S/ 0.00';
                document.getElementById('cartTotal').textContent = 'S/ 0.00';
                return;
            }
            
            // Ocultar mensaje de carrito vacío
            emptyCartMessage.classList.add('hidden');
            checkoutBtn.disabled = false;
            clearCartBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            
            // Generar HTML para los items
            const itemsHTML = cartItems.map(item => `
                <div class="py-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    <div class="md:col-span-6 flex items-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-md flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-medium text-gray-900">${item.name}</h3>
                            <button onclick="removeItem(${item.productId})" class="text-sm text-red-600 hover:text-red-800 flex items-center mt-1">
                                <i class="fas fa-trash text-xs mr-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                    <div class="md:col-span-2 text-center">
                        <span class="md:hidden font-medium text-gray-700">Precio: </span>
                        <span>S/ ${item.price.toFixed(2)}</span>
                    </div>
                    <div class="md:col-span-2 flex items-center justify-center">
                        <div class="flex items-center border border-gray-300 rounded-md">
                            <button onclick="decreaseQuantity(${item.productId})" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-gray-900">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="w-8 text-center">${item.quantity}</span>
                            <button onclick="increaseQuantity(${item.productId})" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-gray-900">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="md:col-span-2 text-right font-medium">
                        <span class="md:hidden font-medium text-gray-700">Subtotal: </span>
                        <span>S/ ${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                </div>
            `).join('');
            
            cartItemsList.innerHTML = itemsHTML;
            
            // Actualizar totales
            const total = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('cartSubtotal').textContent = `S/ ${total.toFixed(2)}`;
            document.getElementById('cartTotal').textContent = `S/ ${total.toFixed(2)}`;
        }
        
        // Funciones para manejar el carrito
        function removeItem(productId) {
            cart.removeItem(productId);
            renderCart();
        }
        
        function increaseQuantity(productId) {
            const items = JSON.parse(localStorage.getItem('cart')) || [];
            const item = items.find(i => i.productId === productId);
            if (item) {
                cart.updateQuantity(productId, item.quantity + 1);
                renderCart();
            }
        }
        
        function decreaseQuantity(productId) {
            const items = JSON.parse(localStorage.getItem('cart')) || [];
            const item = items.find(i => i.productId === productId);
            if (item && item.quantity > 1) {
                cart.updateQuantity(productId, item.quantity - 1);
                renderCart();
            }
        }
        
        // Ir a checkout
        document.getElementById('checkoutBtn').addEventListener('click', function() {
            window.location.href = 'checkout.php';
        });
        
        // Vaciar carrito
        document.getElementById('clearCartBtn').addEventListener('click', function() {
            if (confirm('¿Estás seguro de que deseas vaciar el carrito?')) {
                cart.clear();
                renderCart();
            }
        });
        
        // Inicializar página
        document.addEventListener('DOMContentLoaded', function() {
            renderCart();
        });
    </script>
</body>
</html>
