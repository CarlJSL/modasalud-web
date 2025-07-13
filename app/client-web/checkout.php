<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';

// Verificar que hay items en el carrito (desde localStorage se maneja en JavaScript)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - E-commerce Moda y Salud</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header Simple -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="cart.php" class="flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Volver al carrito</span>
                </a>
                <h1 class="text-xl font-semibold">Finalizar Compra</h1>
                <div></div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold">1</div>
                    <span class="text-xs mt-1">Datos</span>
                </div>
                <div class="h-1 w-8 bg-green-300 rounded"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold">2</div>
                    <span class="text-xs mt-1">Entrega</span>
                </div>
                <div class="h-1 w-8 bg-green-300 rounded"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold">3</div>
                    <span class="text-xs mt-1">Pago</span>
                </div>
                <div class="h-1 w-8 bg-green-300 rounded"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold">4</div>
                    <span class="text-xs mt-1">Confirmar</span>
                </div>
            </div>
            <div class="text-center text-green-700 font-semibold mb-2">
                ¡Estás a un paso de recibir tus productos en casa!
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Formulario de Cliente y Dirección -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-6 flex items-center"><i class="fas fa-user mr-2 text-green-600"></i>Información de Entrega</h2>
                <form id="checkoutForm" class="space-y-6">
                    <!-- Información del Cliente -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">Datos del Cliente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                                <input type="text" id="clientName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">DNI</label>
                                <input type="text" id="clientDni" maxlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" id="clientEmail" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                                <input type="tel" id="clientPhone" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <!-- Dirección de Entrega -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">Dirección de Entrega</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección *</label>
                                <input type="text" id="address" required placeholder="Calle, número, distrito" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad *</label>
                                    <input type="text" id="city" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Región</label>
                                    <input type="text" id="region" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Código Postal</label>
                                <input type="text" id="postalCode" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <!-- Método de Pago -->
                    <div>
                        <h3 class="text-lg font-medium mb-4">Método de Pago</h3>
                        <div class="space-y-3">
                            <!-- 
                                Métodos de pago basados en los tipos ENUM de la base de datos:
                                payment_method:
                                - YAPE
                                - PLIN
                                - TRANSFER
                                - CASH
                            -->
                            <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="paymentMethod" value="YAPE" class="mr-3">
                                <i class="fas fa-mobile-alt text-purple-600 mr-2"></i>
                                <span>Yape</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="paymentMethod" value="PLIN" class="mr-3">
                                <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                                <span>Plin</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="paymentMethod" value="TRANSFER" class="mr-3">
                                <i class="fas fa-university text-green-600 mr-2"></i>
                                <span>Transferencia Bancaria</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="paymentMethod" value="CASH" class="mr-3" checked>
                                <i class="fas fa-money-bill text-yellow-600 mr-2"></i>
                                <span>Pago contra entrega</span>
                            </label>
                        </div>
                    </div>
                </form>
                <div class="mt-8 bg-green-50 border-l-4 border-green-400 p-4 rounded flex items-center gap-3">
                    <i class="fas fa-lock text-green-600 text-2xl"></i>
                    <div>
                        <p class="font-semibold text-green-700">Compra 100% segura</p>
                        <p class="text-green-700 text-sm">Tus datos y pagos están protegidos. Si tienes dudas, <a href="mailto:soporte@modaysalud.com" class="underline">contáctanos</a>.</p>
                    </div>
                </div>
            </div>
            <!-- Resumen del Pedido -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-6 flex items-center"><i class="fas fa-shopping-bag mr-2 text-blue-600"></i>Resumen del Pedido</h2>
                <div id="orderSummary" class="space-y-4 mb-6">
                    <!-- Los items se cargan dinámicamente -->
                </div>
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span id="subtotal">S/ 0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Envío:</span>
                        <span>Gratis</span>
                    </div>
                    <div class="flex justify-between text-xl font-semibold border-t pt-2">
                        <span>Total:</span>
                        <span id="orderTotal">S/ 0.00</span>
                    </div>
                </div>
                <button onclick="submitOrder()" class="w-full bg-green-600 text-white py-4 rounded-md hover:bg-green-700 transition-colors mt-6 text-lg font-semibold">
                    <i class="fas fa-credit-card mr-2"></i>
                    Confirmar Pedido
                </button>
                <div class="mt-6 text-center text-gray-500 text-sm">
                    ¿Necesitas ayuda? Escríbenos a <a href="mailto:soporte@modaysalud.com" class="underline">soporte@modaysalud.com</a> o al WhatsApp <a href="https://wa.me/51999999999" class="underline">+51 999 999 999</a>
                </div>
                <div class="mt-4 flex items-center justify-center gap-4">
                    <img src="../img/tienda.png" alt="Logo" class="w-12 h-12 rounded-full border-2 border-green-200">
                    <span class="text-green-700 font-semibold">Moda y Salud</span>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">¡Pedido Confirmado!</h3>
                    <p class="text-gray-600 mb-4">Tu pedido ha sido registrado exitosamente. Recibirás un email de confirmación pronto.</p>
                    <p class="text-sm text-gray-500 mb-6">Número de orden: <span id="orderNumber" class="font-semibold"></span></p>
                    <button onclick="goToHome()" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">Volver al inicio</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/cart.js"></script>
    <script src="js/checkout.js"></script>
</body>
</html>
