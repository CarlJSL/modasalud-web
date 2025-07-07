<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php'); // o ../index.php dependiendo del nivel de carpeta
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<?php
// Incluir archivo de configuración de la cabecera
include_once './../includes/head.php';
?>

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
                <!-- Header de la página de análisis con breadcrumb -->
                <div class="mb-8">
              

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
                                <option value="hoy">Hoy</option>
                                <option value="semana">Esta semana</option>
                                <option value="mes" selected>Este mes</option>
                                <option value="trimestre">Este trimestre</option>
                                <option value="año">Este año</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Métricas principales - Diseño minimalista -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <!-- Card: Ventas Totales -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-blue-300 transition-colors">
                        <div class="text-sm text-gray-500 mb-1">Ventas Totales</div>
                        <div class="text-2xl font-semibold text-gray-900 mb-2">$125,430</div>
                        <div class="text-sm text-green-600">+12.5%</div>
                    </div>

                    <!-- Card: Órdenes -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-green-300 transition-colors">
                        <div class="text-sm text-gray-500 mb-1">Órdenes</div>
                        <div class="text-2xl font-semibold text-gray-900 mb-2">1,248</div>
                        <div class="text-sm text-green-600">+8.2%</div>
                    </div>

                    <!-- Card: Productos -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-purple-300 transition-colors">
                        <div class="text-sm text-gray-500 mb-1">Productos Vendidos</div>
                        <div class="text-2xl font-semibold text-gray-900 mb-2">3,429</div>
                        <div class="text-sm text-green-600">+15.3%</div>
                    </div>

                    <!-- Card: Ticket Promedio -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-orange-300 transition-colors">
                        <div class="text-sm text-gray-500 mb-1">Ticket Promedio</div>
                        <div class="text-2xl font-semibold text-gray-900 mb-2">$100.50</div>
                        <div class="text-sm text-green-600">+3.7%</div>
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
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded text-blue-600 flex items-center justify-center text-sm font-medium">1</div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Sneakers Deportivos</p>
                                        <p class="text-xs text-gray-500">542 unidades</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">$32,520</p>
                                    <p class="text-xs text-green-600">+18%</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded text-green-600 flex items-center justify-center text-sm font-medium">2</div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Suplementos Vitamínicos</p>
                                        <p class="text-xs text-gray-500">387 unidades</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">$19,350</p>
                                    <p class="text-xs text-green-600">+12%</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded text-purple-600 flex items-center justify-center text-sm font-medium">3</div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Ropa Deportiva Elite</p>
                                        <p class="text-xs text-gray-500">321 unidades</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">$16,050</p>
                                    <p class="text-xs text-green-600">+25%</p>
                                </div>
                            </div>
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
                            <div class="flex justify-between">
                                <span class="text-gray-600">Moda Deportiva</span>
                                <span class="font-medium">45%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Suplementos</span>
                                <span class="font-medium">32%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Calzado</span>
                                <span class="font-medium">23%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Métricas de conversión -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Conversión</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Tasa de Conversión</span>
                                    <span class="font-medium">3.2%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 32%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Tiempo Promedio</span>
                                    <span class="font-medium">4m 32s</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 68%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Páginas por Sesión</span>
                                    <span class="font-medium">2.8</span>
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
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-blue-100 rounded text-blue-600 flex items-center justify-center text-xs font-medium">MX</div>
                                    <span class="ml-2 text-sm text-gray-900">México</span>
                                </div>
                                <div class="text-sm font-medium">$45,230</div>
                            </div>

                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-green-100 rounded text-green-600 flex items-center justify-center text-xs font-medium">US</div>
                                    <span class="ml-2 text-sm text-gray-900">Estados Unidos</span>
                                </div>
                                <div class="text-sm font-medium">$38,920</div>
                            </div>

                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-purple-100 rounded text-purple-600 flex items-center justify-center text-xs font-medium">CA</div>
                                    <span class="ml-2 text-sm text-gray-900">Canadá</span>
                                </div>
                                <div class="text-sm font-medium">$25,180</div>
                            </div>

                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-orange-100 rounded text-orange-600 flex items-center justify-center text-xs font-medium">EU</div>
                                    <span class="ml-2 text-sm text-gray-900">Europa</span>
                                </div>
                                <div class="text-sm font-medium">$16,100</div>
                            </div>
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
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">Moda Deportiva</div>
                                        <div class="text-xs text-gray-500">Ropa y accesorios</div>
                                    </td>
                                    <td class="py-4 text-gray-900">1,250</td>
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">$62,500</div>
                                        <div class="text-xs text-gray-500">$50.00 promedio</div>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">45 productos</span>
                                    </td>
                                    <td class="py-4 text-green-600 font-medium">+15.2%</td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Excelente</span>
                                    </td>
                                </tr>

                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">Suplementos</div>
                                        <div class="text-xs text-gray-500">Vitaminas y nutrición</div>
                                    </td>
                                    <td class="py-4 text-gray-900">987</td>
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">$39,480</div>
                                        <div class="text-xs text-gray-500">$40.00 promedio</div>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">32 productos</span>
                                    </td>
                                    <td class="py-4 text-green-600 font-medium">+12.8%</td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Muy Bueno</span>
                                    </td>
                                </tr>

                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">Calzado</div>
                                        <div class="text-xs text-gray-500">Zapatos deportivos</div>
                                    </td>
                                    <td class="py-4 text-gray-900">543</td>
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">$16,290</div>
                                        <div class="text-xs text-gray-500">$30.00 promedio</div>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">18 productos</span>
                                    </td>
                                    <td class="py-4 text-yellow-600 font-medium">+5.4%</td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Regular</span>
                                    </td>
                                </tr>

                                <tr class="hover:bg-gray-50">
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">Accesorios</div>
                                        <div class="text-xs text-gray-500">Complementos</div>
                                    </td>
                                    <td class="py-4 text-gray-900">321</td>
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">$7,650</div>
                                        <div class="text-xs text-gray-500">$23.83 promedio</div>
                                    </td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">25 productos</span>
                                    </td>
                                    <td class="py-4 text-red-600 font-medium">-2.1%</td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Necesita Atención</span>
                                    </td>
                                </tr>
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
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <button class="px-6 py-2 bg-white text-blue-600 rounded font-medium hover:bg-gray-100">
                                Generar Reporte
                            </button>
                            <button class="px-6 py-2 border border-white text-white rounded font-medium hover:bg-white hover:text-blue-600">
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

    <!-- Scripts simplificados -->
    <script>
        // Funcionalidad para el selector de período
        document.getElementById('periodo-selector').addEventListener('change', function() {
            const periodo = this.value;
            console.log('Período seleccionado:', periodo);
            // Aquí se puede agregar la lógica para actualizar las métricas
        });

        // Funcionalidad para botones de exportación
        document.querySelectorAll('button').forEach(button => {
            if (button.textContent.includes('Exportar')) {
                button.addEventListener('click', function() {
                    const tipo = this.textContent.includes('PDF') ? 'PDF' : 'Excel';
                    alert(`Función de exportación a ${tipo} - Próximamente disponible`);
                });
            }
        });
    </script>
</body>

</html>