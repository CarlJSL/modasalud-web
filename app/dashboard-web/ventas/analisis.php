<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php'); // o ../index.php dependiendo del nivel de carpeta
    exit();
}

// Incluir funciones de análisis
require_once __DIR__ . '/analytics-data.php';

// Obtener el período seleccionado
$periodo = $_GET['periodo'] ?? 'mes';

// Obtener datos de forma segura
$dashboardData = obtenerDatosSegurosDashboard($pdo, $periodo);

// Extraer variables para compatibilidad
$metricas = $dashboardData['metricas'];
$topProductos = $dashboardData['topProductos'];
$ventasPorCategoria = $dashboardData['ventasPorCategoria'];
$analisisDetallado = $dashboardData['analisisDetallado'];
$metricasConversion = $dashboardData['metricasConversion'];
$ventasPorRegion = $dashboardData['ventasPorRegion'];
?>

<!DOCTYPE html>
<html lang="es">

<?php
// Incluir archivo de configuración de la cabecera
include_once './../includes/head.php';
?>

<!-- CSS personalizado para el dashboard -->
<link rel="stylesheet" href="analytics-styles.css">

<body>
    <!-- Contenedor principal con navbar fijo y contenido con scroll -->
    <div class="flex h-screen">
        <!-- Incluir navegación lateral fija -->
        <div class="fixed inset-y-0 left-0 z-50">
            <?php include_once './../includes/navbar.php'; ?>
        </div>

        <!-- Contenedor principal del contenido con margen para el navbar -->
        <div class="flex-1 ml-64 flex flex-col min-h-screen">
            <!-- Incluir header superior fijo -->
            <div class="sticky top-0 z-40">
                <?php include_once './../includes/header.php'; ?>
            </div>

            <!-- Contenido principal dentro del Main con scroll -->
            <main class="flex-1 p-6 bg-gray-50 overflow-y-auto">
                <!-- Header de la página de análisis -->
                <div class="mb-8">
                    <!-- Breadcrumb -->
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="../orden/orders.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                    <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Análisis de Ventas</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <!-- Título y descripción de la página -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Análisis de Ventas</h1>
                            <p class="text-gray-600">Dashboard completo para monitorear el rendimiento de ventas y métricas clave del negocio</p>
                        </div>
                        <!-- Selector de período de tiempo -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Período:</label>
                            <select id="periodo-selector" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="hoy" <?= $periodo == 'hoy' ? 'selected' : '' ?>>Hoy</option>
                                <option value="semana" <?= $periodo == 'semana' ? 'selected' : '' ?>>Esta semana</option>
                                <option value="mes" <?= $periodo == 'mes' ? 'selected' : '' ?>>Este mes</option>
                                <option value="trimestre" <?= $periodo == 'trimestre' ? 'selected' : '' ?>>Este trimestre</option>
                                <option value="año" <?= $periodo == 'año' ? 'selected' : '' ?>>Este año</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Métricas principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <!-- Card: Ventas Totales -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-blue-300 transition-colors">
                        <div class="text-sm text-gray-500 mb-1">Ventas Totales</div>
                        <div class="text-2xl font-semibold text-gray-900 mb-2"><?= formatearDinero($metricas['ventas_totales']) ?></div>
                        <div class="text-sm <?= $metricas['crecimiento_ventas'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $metricas['crecimiento_ventas'] >= 0 ? '+' : '' ?><?= $metricas['crecimiento_ventas'] ?>%
                        </div>
                    </div>

                    <!-- Card: Órdenes -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-green-300 transition-colors">
                        <div class="text-sm text-gray-500 mb-1">Órdenes</div>
                        <div class="text-2xl font-semibold text-gray-900 mb-2"><?= formatearNumero($metricas['total_ordenes']) ?></div>
                        <div class="text-sm <?= $metricas['crecimiento_ordenes'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $metricas['crecimiento_ordenes'] >= 0 ? '+' : '' ?><?= $metricas['crecimiento_ordenes'] ?>%
                        </div>
                    </div>

                    <!-- Card: Productos -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-purple-300 transition-colors">
                        <div class="text-sm text-gray-500 mb-1">Productos Vendidos</div>
                        <div class="text-2xl font-semibold text-gray-900 mb-2"><?= formatearNumero($metricas['productos_vendidos']) ?></div>
                        <div class="text-sm <?= $metricas['crecimiento_productos'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $metricas['crecimiento_productos'] >= 0 ? '+' : '' ?><?= $metricas['crecimiento_productos'] ?>%
                        </div>
                    </div>

                    <!-- Card: Ticket Promedio -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-orange-300 transition-colors">
                        <div class="text-sm text-gray-500 mb-1">Ticket Promedio</div>
                        <div class="text-2xl font-semibold text-gray-900 mb-2"><?= formatearDinero($metricas['ticket_promedio']) ?></div>
                        <div class="text-sm <?= $metricas['crecimiento_ticket'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $metricas['crecimiento_ticket'] >= 0 ? '+' : '' ?><?= $metricas['crecimiento_ticket'] ?>%
                        </div>
                    </div>
                </div>

                <!-- Gráficos - Diseño minimalista -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Gráfico de tendencias -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Tendencia de Ventas</h3>
                            <select class="text-sm border border-gray-300 rounded px-2 py-1">
                                <option>Últimos 12 meses</option>
                                <option>Últimos 6 meses</option>
                            </select>
                        </div>
                        <div class="h-64 bg-gray-50 rounded border border-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-4xl text-gray-300 mb-2">📈</div>
                                <p class="text-gray-500 text-sm">Gráfico de Tendencias</p>
                            </div>
                        </div>
                    </div>

                    <!-- Top productos -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Top Productos</h3>
                            <button class="text-sm text-blue-600 hover:text-blue-800">Ver todos</button>
                        </div>
                        <div class="space-y-3">
                            <?php if (!empty($topProductos)): ?>
                                <?php foreach ($topProductos as $index => $producto): ?>
                                    <div class="flex items-center justify-between py-2">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-<?= $index == 0 ? 'blue' : ($index == 1 ? 'green' : 'purple') ?>-100 rounded text-<?= $index == 0 ? 'blue' : ($index == 1 ? 'green' : 'purple') ?>-600 flex items-center justify-center text-sm font-medium">
                                                <?= $index + 1 ?>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($producto['name']) ?></p>
                                                <p class="text-xs text-gray-500"><?= $producto['cantidad_vendida'] ?> unidades</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900"><?= formatearDinero($producto['ingresos_totales']) ?></p>
                                            <p class="text-xs text-green-600">+<?= rand(10, 25) ?>%</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-8 text-gray-500">
                                    <p>No hay datos de productos para mostrar</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Análisis adicional - Simplificado -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Distribución por categoría -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Por Categoría</h3>
                        <div class="h-32 bg-gray-50 rounded border border-gray-200 flex items-center justify-center mb-4">
                            <div class="text-center">
                                <div class="text-2xl text-gray-300 mb-1">🍩</div>
                                <p class="text-xs text-gray-500">Gráfico de Dona</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <?php if (!empty($ventasPorCategoria)): ?>
                                <?php foreach (array_slice($ventasPorCategoria, 0, 3) as $categoria): ?>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"><?= htmlspecialchars($categoria['categoria']) ?></span>
                                        <span class="font-medium"><?= $categoria['porcentaje'] ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-gray-500">
                                    <p>No hay datos disponibles</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Métricas de conversión -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Conversión</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Tasa de Conversión</span>
                                    <span class="font-medium"><?= $metricasConversion['tasa_conversion'] ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: <?= $metricasConversion['tasa_conversion'] * 10 ?>%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Tiempo Promedio</span>
                                    <span class="font-medium"><?= floor($metricasConversion['tiempo_promedio'] / 60) ?>m <?= $metricasConversion['tiempo_promedio'] % 60 ?>s</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 68%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Páginas por Sesión</span>
                                    <span class="font-medium"><?= $metricasConversion['paginas_por_sesion'] ?></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 56%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ventas por región -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Por Región</h3>
                        <div class="space-y-3">
                            <?php foreach ($ventasPorRegion as $region): ?>
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-<?= $region['codigo'] == 'MX' ? 'blue' : ($region['codigo'] == 'US' ? 'green' : ($region['codigo'] == 'CA' ? 'purple' : 'orange')) ?>-100 rounded text-<?= $region['codigo'] == 'MX' ? 'blue' : ($region['codigo'] == 'US' ? 'green' : ($region['codigo'] == 'CA' ? 'purple' : 'orange')) ?>-600 flex items-center justify-center text-xs font-medium">
                                            <?= $region['codigo'] ?>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-900"><?= $region['pais'] ?></span>
                                    </div>
                                    <div class="text-sm font-medium"><?= formatearDinero($region['ventas']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Tabla de análisis - Minimalista -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Análisis por Categorías</h3>
                            <p class="text-sm text-gray-500">Rendimiento de todas las categorías</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                Exportar PDF
                            </button>
                            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded text-sm hover:bg-gray-50">
                                Exportar Excel
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-3 font-medium text-gray-900">Categoría</th>
                                    <th class="text-left py-3 font-medium text-gray-900">Unidades</th>
                                    <th class="text-left py-3 font-medium text-gray-900">Ingresos</th>
                                    <th class="text-left py-3 font-medium text-gray-900">Productos</th>
                                    <th class="text-left py-3 font-medium text-gray-900">Crecimiento</th>
                                    <th class="text-left py-3 font-medium text-gray-900">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($analisisDetallado)): ?>
                                    <?php foreach ($analisisDetallado as $categoria): ?>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-4">
                                                <div class="font-medium text-gray-900"><?= htmlspecialchars($categoria['categoria']) ?></div>
                                                <div class="text-xs text-gray-500">Productos relacionados</div>
                                            </td>
                                            <td class="py-4 text-gray-900"><?= number_format($categoria['unidades_vendidas']) ?></td>
                                            <td class="py-4">
                                                <div class="font-medium text-gray-900"><?= formatearDinero($categoria['ingresos']) ?></div>
                                                <div class="text-xs text-gray-500"><?= formatearDinero($categoria['precio_promedio']) ?> promedio</div>
                                            </td>
                                            <td class="py-4">
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                    <?= $categoria['total_productos'] ?> productos
                                                </span>
                                            </td>
                                            <td class="py-4 <?= $categoria['crecimiento'] >= 0 ? 'text-green-600' : 'text-red-600' ?> font-medium">
                                                <?= $categoria['crecimiento'] >= 0 ? '+' : '' ?><?= $categoria['crecimiento'] ?>%
                                            </td>
                                            <td class="py-4">
                                                <span class="px-2 py-1 bg-<?= $categoria['estado_color'] ?>-100 text-<?= $categoria['estado_color'] ?>-800 rounded text-xs">
                                                    <?= $categoria['estado'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500">
                                            No hay datos disponibles para mostrar
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Call to Action simplificado -->
                <div class="bg-blue-600 rounded-lg p-6 text-white mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h3 class="text-xl font-medium mb-2">¿Necesitas un análisis más detallado?</h3>
                            <p class="text-blue-100">Genera reportes personalizados para tu negocio</p>
                        </div>
                        <div class="flex space-x-3">
                            <button class="btn-primary">
                                Generar Reporte
                            </button>
                            <button class="btn-secondary">
                                Contactar Soporte
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <?php include_once './../includes/footer.php'; ?>
                </div>
            </main>
            
        </div>
    </div>

    <!-- Scripts mejorados -->
    <script>
        class DashboardAnalytics {
            constructor() {
                this.initEventListeners();
                this.startAutoRefresh();
            }
            
            initEventListeners() {
                // Funcionalidad para el selector de período
                const periodoSelector = document.getElementById('periodo-selector');
                if (periodoSelector) {
                    periodoSelector.addEventListener('change', (e) => {
                        this.cambiarPeriodo(e.target.value);
                    });
                }
                
                // Funcionalidad para botones de exportación
                document.querySelectorAll('button').forEach(button => {
                    if (button.textContent.includes('Exportar PDF')) {
                        button.addEventListener('click', () => this.exportarPDF());
                    } else if (button.textContent.includes('Exportar Excel')) {
                        button.addEventListener('click', () => this.exportarExcel());
                    }
                });
            }
            
            cambiarPeriodo(periodo) {
                // Mostrar indicador de carga
                this.mostrarCargando(true);
                
                // Redirigir con el nuevo período
                window.location.href = '?periodo=' + periodo;
            }
            
            async exportarPDF() {
                try {
                    const response = await fetch('analytics-ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'action=exportar_pdf&periodo=' + this.getCurrentPeriod()
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.mostrarNotificacion('PDF generado exitosamente', 'success');
                        // Aquí se podría abrir el PDF en una nueva ventana
                        // window.open(result.download_url, '_blank');
                    } else {
                        this.mostrarNotificacion('Error al generar PDF: ' + result.message, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacion('Error de conexión', 'error');
                }
            }
            
            async exportarExcel() {
                try {
                    const response = await fetch('analytics-ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'action=exportar_excel&periodo=' + this.getCurrentPeriod()
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.mostrarNotificacion('Excel generado exitosamente', 'success');
                        // Aquí se podría descargar el archivo Excel
                        // window.open(result.download_url, '_blank');
                    } else {
                        this.mostrarNotificacion('Error al generar Excel: ' + result.message, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacion('Error de conexión', 'error');
                }
            }
            
            getCurrentPeriod() {
                const periodoSelector = document.getElementById('periodo-selector');
                return periodoSelector ? periodoSelector.value : 'mes';
            }
            
            mostrarCargando(mostrar) {
                const indicador = document.querySelector('.loading-indicator');
                if (indicador) {
                    if (mostrar) {
                        indicador.style.display = 'flex';
                        indicador.classList.remove('hidden');
                    } else {
                        indicador.style.display = 'none';
                        indicador.classList.add('hidden');
                    }
                }
            }
            
            mostrarNotificacion(mensaje, tipo) {
                // Crear notificación temporal
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                    tipo === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                }`;
                notification.textContent = mensaje;
                
                document.body.appendChild(notification);
                
                // Eliminar después de 3 segundos
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
            
            startAutoRefresh() {
                // Auto-refresh cada 5 minutos solo si la página está visible
                setInterval(() => {
                    if (document.visibilityState === 'visible') {
                        this.actualizarMetricas();
                    }
                }, 300000); // 5 minutos
            }
            
            async actualizarMetricas() {
                try {
                    const response = await fetch('analytics-ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'action=actualizar_metricas&periodo=' + this.getCurrentPeriod()
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Actualizar las métricas en la página
                        this.actualizarElementosUI(result.data);
                        console.log('Métricas actualizadas automáticamente');
                    }
                } catch (error) {
                    console.error('Error al actualizar métricas:', error);
                }
            }
            
            actualizarElementosUI(data) {
                // Aquí se actualizarían los elementos de la UI con los nuevos datos
                // Por simplicidad, solo mostramos un log
                console.log('Datos actualizados:', data);
            }
        }
        
        // Inicializar el dashboard cuando se cargue la página
        document.addEventListener('DOMContentLoaded', () => {
            window.dashboardAnalytics = new DashboardAnalytics();
        });
        
        // Manejar errores globales
        window.addEventListener('error', (e) => {
            console.error('Error en el dashboard:', e.error);
        });
    </script>
</body>

</html>