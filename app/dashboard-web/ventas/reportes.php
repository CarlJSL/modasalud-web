<?php
session_start();


require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/reportModel.php';

use App\Model\ReportModel;

$reportModel = new ReportModel($pdo);

// Obtener parámetros de filtro
$dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // Primer día del mes actual
$dateTo = $_GET['date_to'] ?? date('Y-m-d'); // Fecha actual
$reportType = $_GET['report_type'] ?? 'general';
$filters = [
    'payment_method' => $_GET['payment_method'] ?? '',
    'order_source' => $_GET['order_source'] ?? ''
];

// Obtener datos según el tipo de reporte
$reportData = [];
$salesReport = null;
$dailySales = [];
$topProducts = [];
$salesByCategory = [];
$topCustomers = [];
$paymentMethods = [];
$inventoryReport = [];
$periodComparison = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!empty($dateFrom) && !empty($dateTo))) {
    $salesReport = $reportModel->getSalesReport($dateFrom, $dateTo, $filters);
    $dailySales = $reportModel->getDailySales($dateFrom, $dateTo);
    $topProducts = $reportModel->getTopProducts($dateFrom, $dateTo, 10);
    $salesByCategory = $reportModel->getSalesByCategory($dateFrom, $dateTo);
    $topCustomers = $reportModel->getTopCustomers($dateFrom, $dateTo, 10);
    $paymentMethods = $reportModel->getPaymentMethodsReport($dateFrom, $dateTo);
    $inventoryReport = $reportModel->getInventoryReport();
    $periodComparison = $reportModel->getPeriodComparison($dateFrom, $dateTo);
}

// Manejar generación de PDF
if (isset($_GET['action']) && $_GET['action'] === 'generate_pdf') {
    require_once __DIR__ . '/generate_report_pdf.php';
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once __DIR__ . '/../includes/head.php'; ?>
    <title>Reportes de Ventas - Sistema de Gestión</title>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>
    <!-- Flatpickr para selección de fechas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</head>

<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include_once __DIR__ . '/../includes/navbar.php'; ?>

        <!-- Contenido principal -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <?php include_once __DIR__ . '/../includes/header.php'; ?>

            <!-- Main content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                <!-- Header de la página -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Reportes de Ventas</h1>
                            <p class="text-gray-600 mt-2">Análisis detallado del rendimiento del negocio</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <?php if ($salesReport): ?>
                                <!--
                                <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Exportar Excel
                                </button> -->
                                <a href="?<?= http_build_query(array_merge($_GET, ['action' => 'generate_pdf'])) ?>" 
                                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Generar PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Formulario de filtros -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros de Reporte</h3>
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                            <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                            <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($dateTo) ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                            <select id="payment_method" name="payment_method" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todos los métodos</option>
                                <option value="YAPE" <?= $filters['payment_method'] === 'YAPE' ? 'selected' : '' ?>>Yape</option>
                                <option value="PLIN" <?= $filters['payment_method'] === 'PLIN' ? 'selected' : '' ?>>Plin</option>
                                <option value="TRANSFER" <?= $filters['payment_method'] === 'TRANSFER' ? 'selected' : '' ?>>Transferencia</option>
                                <option value="CASH" <?= $filters['payment_method'] === 'CASH' ? 'selected' : '' ?>>Efectivo</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Generar Reporte
                            </button>
                        </div>
                    </form>
                </div>

                <?php if ($salesReport): ?>
                    <!-- Resumen general -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Ingresos Totales</p>
                                    <p class="text-2xl font-bold text-gray-900">S/ <?= number_format($salesReport['total_revenue'] ?? 0, 2) ?></p>
                                    <?php if (isset($periodComparison['total_revenue'])): ?>
                                        <p class="text-xs <?= $periodComparison['total_revenue']['percentage_change'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= $periodComparison['total_revenue']['percentage_change'] >= 0 ? '+' : '' ?><?= $periodComparison['total_revenue']['percentage_change'] ?>% vs período anterior
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Total Órdenes</p>
                                    <p class="text-2xl font-bold text-gray-900"><?= number_format($salesReport['total_orders'] ?? 0) ?></p>
                                    <?php if (isset($periodComparison['total_orders'])): ?>
                                        <p class="text-xs <?= $periodComparison['total_orders']['percentage_change'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= $periodComparison['total_orders']['percentage_change'] >= 0 ? '+' : '' ?><?= $periodComparison['total_orders']['percentage_change'] ?>% vs período anterior
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Ticket Promedio</p>
                                    <p class="text-2xl font-bold text-gray-900">S/ <?= number_format($salesReport['average_ticket'] ?? 0, 2) ?></p>
                                    <?php if (isset($periodComparison['average_ticket'])): ?>
                                        <p class="text-xs <?= $periodComparison['average_ticket']['percentage_change'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= $periodComparison['average_ticket']['percentage_change'] >= 0 ? '+' : '' ?><?= $periodComparison['average_ticket']['percentage_change'] ?>% vs período anterior
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-500">Clientes Únicos</p>
                                    <p class="text-2xl font-bold text-gray-900"><?= number_format($salesReport['unique_customers'] ?? 0) ?></p>
                                    <?php if (isset($periodComparison['unique_customers'])): ?>
                                        <p class="text-xs <?= $periodComparison['unique_customers']['percentage_change'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= $periodComparison['unique_customers']['percentage_change'] >= 0 ? '+' : '' ?><?= $periodComparison['unique_customers']['percentage_change'] ?>% vs período anterior
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos y tablas -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Gráfico de ventas diarias -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas Diarias</h3>
                            <div class="h-80">
                                <canvas id="dailySalesChart"></canvas>
                            </div>
                        </div>

                        <!-- Gráfico de ventas por categoría -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ventas por Categoría</h3>
                            <div class="h-80">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Tablas de datos -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Top productos -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Productos Más Vendidos</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach (array_slice($topProducts, 0, 5) as $product): ?>
                                            <tr>
                                                <td class="px-4 py-3 text-sm">
                                                    <div>
                                                        <p class="font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?></p>
                                                        <p class="text-gray-500"><?= htmlspecialchars($product['category_name'] ?? 'Sin categoría') ?></p>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900"><?= $product['total_quantity'] ?></td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">S/ <?= number_format($product['total_revenue'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Top clientes -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mejores Clientes</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Órdenes</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach (array_slice($topCustomers, 0, 5) as $customer): ?>
                                            <tr>
                                                <td class="px-4 py-3 text-sm">
                                                    <div>
                                                        <p class="font-medium text-gray-900"><?= htmlspecialchars($customer['name']) ?></p>
                                                        <p class="text-gray-500"><?= htmlspecialchars($customer['email']) ?></p>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900"><?= $customer['orders_count'] ?></td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">S/ <?= number_format($customer['total_spent'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Estado vacío -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay datos para mostrar</h3>
                        <p class="mt-1 text-sm text-gray-500">Selecciona un rango de fechas para generar un reporte.</p>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <script>
        // Datos para los gráficos
        const dailySalesData = <?= json_encode($dailySales) ?>;
        const categoryData = <?= json_encode($salesByCategory) ?>;

        // Configuración de colores
        const colors = {
            primary: '#3B82F6',
            success: '#10B981',
            warning: '#F59E0B',
            danger: '#EF4444',
            info: '#8B5CF6',
            secondary: '#6B7280'
        };

        // Gráfico de ventas diarias
        if (dailySalesData.length > 0) {
            const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailySalesData.map(item => {
                        const date = new Date(item.sale_date);
                        return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
                    }),
                    datasets: [{
                        label: 'Ventas Diarias (S/)',
                        data: dailySalesData.map(item => parseFloat(item.daily_revenue)),
                        borderColor: colors.primary,
                        backgroundColor: colors.primary + '20',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'S/ ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Gráfico de categorías
        if (categoryData.length > 0) {
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.category_name),
                    datasets: [{
                        data: categoryData.map(item => parseFloat(item.total_revenue)),
                        backgroundColor: [
                            colors.primary,
                            colors.success,
                            colors.warning,
                            colors.danger,
                            colors.info,
                            colors.secondary
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            // Mostrar mensaje cuando no hay datos
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            categoryCtx.font = '16px Arial';
            categoryCtx.fillStyle = '#6B7280';
            categoryCtx.textAlign = 'center';
            categoryCtx.fillText('No hay datos de categorías para mostrar', categoryCtx.canvas.width / 2, categoryCtx.canvas.height / 2);
        }

        // Función para exportar a Excel (simulada)
        function exportToExcel() {
            alert('Función de exportación a Excel en desarrollo');
        }
    </script>
</body>

</html>
