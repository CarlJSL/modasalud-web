<!-- Sidebar moderno con diseño claro y pastel -->
<?php
// solo si no lo has iniciado aún en el archivo

$nombre = $_SESSION['usuario_nombre'] ?? 'Invitado';
$email = $_SESSION['usuario_email'] ?? 'correo@ejemplo.com';
?>
<aside class="w-64 h-screen bg-gradient-to-b from-slate-50 to-blue-50 border-r border-blue-100 shadow-lg hidden lg:flex lg:flex-col">
    <!-- Header del sidebar con logo y nombre -->
    <div class="p-6 border-b border-blue-100 bg-white/50 backdrop-blur-sm flex-shrink-0">
        <div class="flex items-center space-x-3">
            <!-- Logo con gradiente pastel -->
            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-800">MODA SALUD</h1>
                <p class="text-xs text-gray-500">Gladys Mayra</p>
            </div>
        </div>
    </div>

    <!-- Navegación principal con scroll -->
    <nav class="mt-6 px-4 space-y-2 flex-1 overflow-y-auto pb-4">
        <!-- Dashboard / Inicio -->
        <a href="../ventas/analisis.php" class="group flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-blue-100 hover:to-purple-100 hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm group-hover:shadow-md transition-shadow">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="font-medium">Dashboard</span>
            <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <!-- Productos (con submenu) -->
        <div>
            <button id="productos-toggle" class="w-full flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-green-100 hover:to-emerald-100 hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
                <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-emerald-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <span class="font-medium">Productos</span>
                <div class="ml-auto">
                    <svg id="productos-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </button>
            <!-- Submenu de Productos -->
            <div id="productos-submenu" class="ml-8 mt-2 space-y-1 hidden transition-all duration-300">
                <a href="../products/productos.php" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-700 transition-colors">
                    <div class="w-2 h-2 bg-green-300 rounded-full mr-3"></div>
                    Lista de Productos
                </a>
                <a href="../categories/categories.php" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-700 transition-colors">
                    <div class="w-2 h-2 bg-green-300 rounded-full mr-3"></div>
                    Categorías
                </a>
                <!--
                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-700 transition-colors">
                    <div class="w-2 h-2 bg-green-300 rounded-full mr-3"></div>
                    Inventario
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-700 transition-colors">
                    <div class="w-2 h-2 bg-green-300 rounded-full mr-3"></div>
                    Reseñas
                </a>
                -->
            </div>
        </div>

        <!-- Ventas (con submenu) -->
        <div class="group">
            <button id="ventas-toggle" class="w-full flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-purple-100 hover:to-pink-100 hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-500 rounded-lg flex items-center justify-center mr-3 shadow-sm group-hover:shadow-md transition-shadow">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="font-medium">Ventas</span>
                <div class="ml-auto">
                    <svg id="ventas-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </button>
            <!-- Submenu de Ventas -->
            <div id="ventas-submenu" class="ml-8 mt-2 space-y-1 hidden max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                <a href="./analisis.php" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition-colors">
                    <div class="w-2 h-2 bg-purple-300 rounded-full mr-3"></div>
                    Análisis de Ventas
                </a>
                <a href="../orden/orders.php" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition-colors">
                    <div class="w-2 h-2 bg-purple-300 rounded-full mr-3"></div>
                    Ventas
                </a>
                <!--
                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition-colors">
                    <div class="w-2 h-2 bg-purple-300 rounded-full mr-3"></div>
                    Pagos
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-purple-50 hover:text-purple-700 transition-colors">
                    <div class="w-2 h-2 bg-purple-300 rounded-full mr-3"></div>
                    Cupones
                </a>
-->
            </div>
        </div>

        <!-- Usuarios (con submenu) -->
        <div>
            <button id="usuarios-toggle" class="w-full flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-orange-100 hover:to-yellow-100 hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-yellow-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a2.121 2.121 0 11-3 3m3-3a2.121 2.121 0 01-3 3m3-3v6"></path>
                    </svg>
                </div>
                <span class="font-medium">Usuarios</span>
                <div class="ml-auto">
                    <svg id="usuarios-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </button>
            <!-- Submenu de Usuarios -->
            <div id="usuarios-submenu" class="ml-8 mt-2 space-y-1 hidden transition-all duration-300">
                <a href="../users/usuarios.php" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-orange-50 hover:text-orange-700 transition-colors">
                    <div class="w-2 h-2 bg-orange-300 rounded-full mr-3"></div>
                    Gestión de Usuarios
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-orange-50 hover:text-orange-700 transition-colors">
                    <div class="w-2 h-2 bg-orange-300 rounded-full mr-3"></div>
                    Roles y Permisos
                </a>
            </div>
        </div>

        <!-- Carrito de Compras -->
        <a href="#" class="group flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-teal-100 hover:to-cyan-100 hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
            <div class="w-8 h-8 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-lg flex items-center justify-center mr-3 shadow-sm group-hover:shadow-md transition-shadow">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a2.121 2.121 0 11-3 3m3-3a2.121 2.121 0 01-3 3m3-3v6"></path>
                </svg>
            </div>
            <span class="font-medium">Clientes</span>

        </a>

        <!-- Auditoría -->
        <a href="#" class="group flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-indigo-100 hover:to-blue-100 hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm group-hover:shadow-md transition-shadow">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span class="font-medium">Auditoría</span>
            <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        <!-- Reportes -->
        <a href="#" class="group flex items-center px-4 py-3 text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-indigo-100 hover:to-blue-100 hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm group-hover:shadow-md transition-shadow">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span class="font-medium">Reportes</span>
            <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>
    </nav>

    <!-- Sección de configuración (parte inferior) - fija -->
    <div class="p-4 border-t border-blue-100 bg-white/30 backdrop-blur-sm flex-shrink-0">
        <!-- Perfil del usuario -->
        <div class="flex items-center p-3 bg-white/60 rounded-xl shadow-sm mb-3 backdrop-blur-sm">
            <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($nombre) ?></p>
                <p class="text-xs text-gray-500"><?= htmlspecialchars($email) ?></p>
            </div>
        </div>

        <!-- Configuración -->
        <a href="#" class="group flex items-center px-4 py-3 text-gray-600 rounded-xl hover:bg-gradient-to-r hover:from-gray-100 hover:to-slate-100 hover:shadow-md transition-all duration-300">
            <div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-slate-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <span class="font-medium text-sm">Configuración</span>
        </a>
    </div>
</aside>

<!-- Overlay para móviles (cuando el sidebar esté abierto) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden hidden"></div>

<!-- Botón hamburguesa para móviles -->
<button id="mobile-menu-button" class="lg:hidden fixed top-4 left-4 z-30 p-2 bg-white rounded-lg shadow-lg">
    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Script para funcionalidad móvil -->
<script>
    // Funcionalidad del menú móvil
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebar = document.querySelector('aside');
    const overlay = document.getElementById('sidebar-overlay');

    mobileMenuButton?.addEventListener('click', function() {
        sidebar?.classList.toggle('hidden');
        overlay?.classList.toggle('hidden');
    });

    overlay?.addEventListener('click', function() {
        sidebar?.classList.add('hidden');
        overlay?.classList.add('hidden');
    });

    // Navbar fijo - sin efectos de parallax que interfieran con el scroll

    // Funcionalidad del submenu de Ventas
    const ventasToggle = document.getElementById('ventas-toggle');
    const ventasSubmenu = document.getElementById('ventas-submenu');
    const ventasArrow = document.getElementById('ventas-arrow');

    ventasToggle?.addEventListener('click', function() {
        const isHidden = ventasSubmenu?.classList.contains('hidden');

        if (isHidden) {
            ventasSubmenu?.classList.remove('hidden');
            ventasSubmenu?.classList.remove('max-h-0');
            ventasSubmenu?.classList.add('max-h-96');
            ventasArrow?.classList.add('rotate-180');
        } else {
            ventasSubmenu?.classList.add('max-h-0');
            ventasSubmenu?.classList.remove('max-h-96');
            ventasArrow?.classList.remove('rotate-180');
            setTimeout(() => {
                ventasSubmenu?.classList.add('hidden');
            }, 300);
        }
    });

    // Funcionalidad del submenu de Productos
    const productosToggle = document.getElementById('productos-toggle');
    const productosSubmenu = document.getElementById('productos-submenu');
    const productosArrow = document.getElementById('productos-arrow');

    productosToggle?.addEventListener('click', function() {
        const isHidden = productosSubmenu?.classList.contains('hidden');

        if (isHidden) {
            productosSubmenu?.classList.remove('hidden');
            productosArrow?.classList.add('rotate-180');
        } else {
            productosSubmenu?.classList.add('hidden');
            productosArrow?.classList.remove('rotate-180');
        }
    });

    // Funcionalidad del submenu de Usuarios
    const usuariosToggle = document.getElementById('usuarios-toggle');
    const usuariosSubmenu = document.getElementById('usuarios-submenu');
    const usuariosArrow = document.getElementById('usuarios-arrow');

    usuariosToggle?.addEventListener('click', function() {
        const isHidden = usuariosSubmenu?.classList.contains('hidden');

        if (isHidden) {
            usuariosSubmenu?.classList.remove('hidden');
            usuariosArrow?.classList.add('rotate-180');
        } else {
            usuariosSubmenu?.classList.add('hidden');
            usuariosArrow?.classList.remove('rotate-180');
        }
    });
</script>