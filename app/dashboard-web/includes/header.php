
<!-- Header moderno con diseño pastel y funcionalidades avanzadas -->
<header class="w-full bg-gradient-to-r from-white via-blue-50 to-purple-50 shadow-lg border-b border-blue-100 px-6 py-4 backdrop-blur-sm bg-white/80">
    <div class="flex items-center justify-between">
        <!-- Lado izquierdo: Título dinámico y breadcrumb -->
        <div class="flex items-center space-x-4">
            <!-- Botón hamburguesa para móviles (oculto en desktop) -->
            <button id="mobile-header-menu" class="lg:hidden p-2 rounded-xl bg-gradient-to-br from-blue-100 to-purple-100 hover:from-blue-200 hover:to-purple-200 transition-all duration-300 shadow-sm">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Información de la página actual -->
            <div class="flex flex-col">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="current-time"></span>
                    <span class="mx-2">•</span>
                    <span class="text-green-600 font-medium">En línea</span>
                </div>
            </div>
        </div>

        <!-- Lado derecho: Notificaciones y perfil de usuario -->
        <div class="flex items-center space-x-3">
            <!-- Notificaciones -->

            <!-- Separador visual -->
            <div class="h-8 w-px bg-gradient-to-b from-transparent via-gray-300 to-transparent"></div>

            <!-- Perfil de usuario mejorado -->
            <div class="relative group">
                <button class="flex items-center space-x-3 p-2 rounded-xl bg-gradient-to-br from-white/60 to-gray-50/60 hover:from-white/80 hover:to-gray-50/80 backdrop-blur-sm border border-gray-200/50 transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105">
                    <!-- Avatar con gradiente -->
                    <div class="relative">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 p-0.5 shadow-lg">
                            <img src="https://ui-avatars.com/api/?name=Carlos+Sipan&background=f8fafc&color=374151&size=128" alt="Avatar" class="w-full h-full rounded-full bg-white object-cover">
                        </div>
                        <!-- Indicador de estado en línea -->
                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full border-2 border-white shadow-sm"></div>
                    </div>

                    <!-- Información del usuario -->
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-gray-800">
                            <?= htmlspecialchars($_SESSION['usuario_username'] ?? 'Invitado') ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?= htmlspecialchars($_SESSION['usuario_rol'] ?? 'Sin rol') ?>
                        </p>
                    </div>


                    <!-- Icono de dropdown -->
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown del perfil (oculto por defecto) -->
                <div class="absolute right-0 top-full mt-2 w-64 bg-white/90 backdrop-blur-md rounded-xl shadow-xl border border-gray-200/50 overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                    <!-- Header del dropdown -->
                    <div class="p-4 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            
                            <div>
                                <p class="font-semibold text-gray-800">
                                    <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Invitado') ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($_SESSION['usuario_email'] ?? 'Sin email') ?>
                                </p>
                                <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full mt-1">Administrador</span>
                            </div>
                        </div>
                    </div>

                    <!-- Opciones del dropdown -->
                    <div class="p-2">
                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Mi Perfil
                        </a>
                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Mis Reportes
                        </a>
                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Preferencias
                        </a>
                        <div class="border-t border-gray-100 my-2"></div>
                        <a href="../users/logout.php" class="flex items-center px-3 py-2 text-sm text-red-600 rounded-lg hover:bg-red-50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Script para funcionalidades del header -->
<script>
    // Actualizar la hora en tiempo real
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
        const dateString = now.toLocaleDateString('es-ES', {
            day: 'numeric',
            month: 'short'
        });

        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = `${dateString}, ${timeString}`;
        }
    }

    // Actualizar la hora cada segundo
    updateTime();
    setInterval(updateTime, 1000);

    // Animación de los badges de notificaciones
    function animateNotificationBadge() {
        const badges = document.querySelectorAll('span[class*="bg-gradient-to-br"]');
        badges.forEach(badge => {
            if (badge.textContent && parseInt(badge.textContent) > 0) {
                badge.style.animation = 'pulse 2s infinite';
            }
        });
    }

    // Inicializar animaciones
    animateNotificationBadge();
</script>

<!-- Estilos adicionales para animaciones -->
<style>
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    /* Efecto glassmorphism para el header */
    header {
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* Animación suave para el dropdown */
    .group:hover .group-hover\:opacity-100 {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>