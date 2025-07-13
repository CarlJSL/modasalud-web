<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Moda y Salud</title>
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
                    <a href="contact.php" class="text-green-800 font-semibold hover:text-green-600 transition border-b-2 border-green-500">Contacto</a>
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

    <!-- Hero de contacto -->
    <div class="bg-gradient-to-r from-blue-500 to-green-500 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Contáctanos</h1>
            <p class="text-xl text-white opacity-90 max-w-3xl mx-auto">Estamos aquí para ayudarte. Nuestro equipo estará encantado de atender tus consultas y resolver cualquier duda que puedas tener.</p>
        </div>
    </div>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Formulario de Contacto -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Envíanos un mensaje</h2>
                
                <form class="space-y-6" id="contactForm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                        <input type="text" id="subject" name="subject" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                        <textarea id="message" name="message" rows="6" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full bg-green-600 text-white py-3 px-6 rounded-md hover:bg-green-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>Enviar Mensaje
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Información de Contacto -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Información de Contacto</h2>
                
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4 text-green-600">
                                <i class="fas fa-map-marker-alt text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Dirección</h3>
                                <p class="text-gray-600">Av. Principal 123, Lima, Perú</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4 text-green-600">
                                <i class="fas fa-phone-alt text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Teléfono</h3>
                                <p class="text-gray-600">(01) 123-4567</p>
                                <p class="text-gray-600">+51 987-654-321</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4 text-green-600">
                                <i class="fas fa-envelope text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Email</h3>
                                <p class="text-gray-600">info@modaysalud.com</p>
                                <p class="text-gray-600">ventas@modaysalud.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4 text-green-600">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">Horarios de Atención</h3>
                                <p class="text-gray-600">Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                                <p class="text-gray-600">Sábados: 10:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mapa -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <h3 class="p-4 font-semibold border-b">Nuestra ubicación</h3>
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d249743.7250584079!2d-77.15466925430647!3d-12.026267005225393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c5f619ee3ec7%3A0x14206cb9cc452e4a!2sLima!5e0!3m2!1ses!2spe!4v1645594113200!5m2!1ses!2spe" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
                
                <!-- Redes Sociales -->
                <div class="mt-8">
                    <h3 class="font-semibold mb-4">Síguenos en redes sociales</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="bg-pink-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-pink-700 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="bg-sky-500 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-sky-600 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="bg-green-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-green-700 transition-colors">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Preguntas Frecuentes</h2>
            
            <div class="max-w-3xl mx-auto">
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-sm">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left" onclick="toggleFaq(this)">
                            <span class="font-semibold text-gray-900">¿Cuáles son los métodos de pago aceptados?</span>
                            <i class="fas fa-chevron-down text-green-600"></i>
                        </button>
                        <div class="hidden px-6 py-4 border-t">
                            <p class="text-gray-600">Aceptamos tarjetas de crédito (Visa, Mastercard, American Express), transferencias bancarias, y pago contra entrega en efectivo.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-sm">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left" onclick="toggleFaq(this)">
                            <span class="font-semibold text-gray-900">¿Cuánto tiempo toma la entrega?</span>
                            <i class="fas fa-chevron-down text-green-600"></i>
                        </button>
                        <div class="hidden px-6 py-4 border-t">
                            <p class="text-gray-600">Las entregas en Lima Metropolitana se realizan en 24-48 horas laborables. Para provincias, el tiempo de entrega es de 3-5 días laborables dependiendo de la ubicación.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-sm">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left" onclick="toggleFaq(this)">
                            <span class="font-semibold text-gray-900">¿Puedo devolver un producto?</span>
                            <i class="fas fa-chevron-down text-green-600"></i>
                        </button>
                        <div class="hidden px-6 py-4 border-t">
                            <p class="text-gray-600">Sí, ofrecemos un período de devolución de 30 días para todos nuestros productos. El producto debe estar en su estado original y con todas las etiquetas. Para iniciar una devolución, contáctanos por correo electrónico o teléfono.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-sm">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left" onclick="toggleFaq(this)">
                            <span class="font-semibold text-gray-900">¿Realizan envíos internacionales?</span>
                            <i class="fas fa-chevron-down text-green-600"></i>
                        </button>
                        <div class="hidden px-6 py-4 border-t">
                            <p class="text-gray-600">Actualmente solo realizamos envíos dentro de Perú. Estamos trabajando para ofrecer envíos internacionales en el futuro.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-sm">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left" onclick="toggleFaq(this)">
                            <span class="font-semibold text-gray-900">¿Cómo puedo hacer seguimiento de mi pedido?</span>
                            <i class="fas fa-chevron-down text-green-600"></i>
                        </button>
                        <div class="hidden px-6 py-4 border-t">
                            <p class="text-gray-600">Una vez que tu pedido sea enviado, recibirás un correo electrónico con un número de seguimiento y un enlace para que puedas monitorear el estado de tu envío en tiempo real.</p>
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
        // Mostrar la cantidad actual del carrito
        document.addEventListener('DOMContentLoaded', function() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const itemCount = cart.reduce((count, item) => count + item.quantity, 0);
            document.getElementById('cartCount').textContent = itemCount;
            
            // Manejar envío del formulario
            document.getElementById('contactForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Aquí iría la lógica para enviar el formulario por AJAX
                // Por ahora solo mostramos un mensaje de éxito
                
                // Mensaje de éxito
                const successMessage = document.createElement('div');
                successMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4';
                successMessage.innerHTML = `
                    <div class="flex">
                        <div class="py-1 mr-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="font-semibold">¡Mensaje enviado!</p>
                            <p class="text-sm">Gracias por contactarnos. Te responderemos a la brevedad.</p>
                        </div>
                    </div>
                `;
                
                // Insertar mensaje después del formulario
                this.parentNode.insertBefore(successMessage, this.nextSibling);
                
                // Limpiar formulario
                this.reset();
                
                // Eliminar mensaje después de 5 segundos
                setTimeout(function() {
                    successMessage.remove();
                }, 5000);
            });
        });
        
        // Función para el acordeón de FAQ
        function toggleFaq(element) {
            const content = element.nextElementSibling;
            const icon = element.querySelector('i');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    </script>
</body>
</html>
