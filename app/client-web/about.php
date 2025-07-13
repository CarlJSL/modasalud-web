<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - Moda y Salud</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="index.php" class="flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Volver a la tienda</span>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Sobre Nosotros</h1>
                <div></div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <section class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <img src="img/tienda.png" alt="Moda y Salud">
                <div>
                    <h2 class="text-3xl font-bold text-green-700 mb-2">ModaSalud</h2>
                    <p class="text-gray-700 text-lg mb-4">Somos una empresa peruana dedicada a ofrecer productos de moda y bienestar, seleccionados cuidadosamente para mejorar tu calidad de vida y estilo personal.</p>
                    <ul class="text-gray-600 space-y-2">
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Atención personalizada y cercana</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Productos originales y de alta calidad</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Envíos a todo el Perú</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Pagos seguros y soporte post-venta</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="bg-gradient-to-r from-green-50 to-emerald-100 rounded-lg shadow p-8 mb-8">
            <h3 class="text-2xl font-semibold text-green-800 mb-4">Nuestra Misión</h3>
            <p class="text-gray-700 text-lg">Inspirar confianza y bienestar a través de productos de moda y salud, brindando una experiencia de compra fácil, segura y satisfactoria para todos nuestros clientes.</p>
        </section>

        <section class="bg-white rounded-lg shadow p-8">
            <h3 class="text-2xl font-semibold text-green-800 mb-4">¿Por qué elegirnos?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex flex-col items-center text-center">
                    <div class="bg-green-100 p-4 rounded-full mb-3">
                        <i class="fas fa-user-friends text-2xl text-green-600"></i>
                    </div>
                    <h4 class="font-bold text-lg mb-1">Cercanía</h4>
                    <p class="text-gray-600">Te acompañamos en cada paso de tu compra y resolvemos tus dudas rápidamente.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="bg-green-100 p-4 rounded-full mb-3">
                        <i class="fas fa-shield-alt text-2xl text-green-600"></i>
                    </div>
                    <h4 class="font-bold text-lg mb-1">Confianza</h4>
                    <p class="text-gray-600">Garantizamos productos auténticos y procesos de pago 100% seguros.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="bg-green-100 p-4 rounded-full mb-3">
                        <i class="fas fa-truck text-2xl text-green-600"></i>
                    </div>
                    <h4 class="font-bold text-lg mb-1">Rapidez</h4>
                    <p class="text-gray-600">Despachamos tu pedido en menos de 24h y hacemos seguimiento hasta la entrega.</p>
                </div>
            </div>
        </section>
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
</body>
</html>
