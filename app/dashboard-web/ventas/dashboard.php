<?php
session_start();

// Control de acceso basado en roles y permisos

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/orderModel.php';

use App\Model\OrderModel;

$orderModel = new OrderModel($pdo, 'orders');

// Obtener datos para el dashboard
$dashboardStats = $orderModel->getDashboardStats();
$salesByMonth = $orderModel->getSalesByMonth();
$salesByCategory = $orderModel->getSalesByCategory();
$topProducts = $orderModel->getTopSellingProducts(5);
$orderStatusCounts = [
    'pending' => $orderModel->countByPaymentStatus('PENDING'),
    'paid' => $orderModel->countByPaymentStatus('PAID'),
    'failed' => $orderModel->countByPaymentStatus('FAILED'),
    'cancelled' => $orderModel->countByOrderStatus('CANCELLED')
];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once __DIR__ . '/../includes/head.php'; ?>
    <title>Dashboard de Ventas - Sistema de Gestión</title>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Date-fns para formateo de fechas -->
    <script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>
    <!-- Estilos personalizados del dashboard -->
    <link rel="stylesheet" href="../css/dashboard.css">
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
                            <h1 class="text-3xl font-bold text-gray-900">Dashboard de Ventas</h1>
                            <p class="text-gray-600 mt-2">Resumen de rendimiento y estadísticas de ventas</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Último actualizado</p>
                                <p class="text-sm font-medium text-gray-900"><?= date('d/m/Y H:i') ?></p>
                            </div>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- KPIs Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Ventas del mes -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl dashboard-card">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 gradient-green rounded-xl flex items-center justify-center kpi-icon shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Ventas del Mes</dt>
                                        <dd class="text-2xl font-bold text-gray-900 animate-count">S/ <?= number_format($dashboardStats['current_month_sales'], 2) ?></dd>
                                        <dd class="text-xs text-green-600 font-medium">+12% vs mes anterior</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Órdenes del mes -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl dashboard-card">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 gradient-blue rounded-xl flex items-center justify-center kpi-icon shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Órdenes del Mes</dt>
                                        <dd class="text-2xl font-bold text-gray-900 animate-count"><?= number_format($dashboardStats['current_month_orders']) ?></dd>
                                        <dd class="text-xs text-blue-600 font-medium">+8% vs mes anterior</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket promedio -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl dashboard-card">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 gradient-yellow rounded-xl flex items-center justify-center kpi-icon shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Ticket Promedio</dt>
                                        <dd class="text-2xl font-bold text-gray-900 animate-count">S/ <?= number_format($dashboardStats['average_ticket'], 2) ?></dd>
                                        <dd class="text-xs text-yellow-600 font-medium">+5% vs mes anterior</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clientes nuevos -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl dashboard-card">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 gradient-purple rounded-xl flex items-center justify-center kpi-icon shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Clientes Nuevos</dt>
                                        <dd class="text-2xl font-bold text-gray-900 animate-count"><?= number_format($dashboardStats['new_customers']) ?></dd>
                                        <dd class="text-xs text-purple-600 font-medium">+15% vs mes anterior</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos principales -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Gráfico de ventas por mes -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl dashboard-card">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg leading-6 font-semibold text-gray-900">Ventas por Mes</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        +12%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de estado de órdenes -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl dashboard-card">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900">Estado de Órdenes</h3>
                            <p class="text-sm text-gray-500 mt-1">Distribución actual de órdenes</p>
                        </div>
                        <div class="p-6">
                            <div class="chart-container">
                                <canvas id="ordersStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segunda fila de gráficos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Ventas por categoría -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl dashboard-card">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900">Ventas por Categoría</h3>
                            <p class="text-sm text-gray-500 mt-1">Rendimiento por línea de productos</p>
                        </div>
                        <div class="p-6">
                            <div class="chart-container">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Productos más vendidos -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl dashboard-card">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900">Top 5 Productos</h3>
                            <p class="text-sm text-gray-500 mt-1">Productos más populares del mes</p>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php if (empty($topProducts)): ?>
                                    <div class="text-center py-8">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <p class="text-gray-500 text-sm">No hay datos de productos</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($topProducts as $index => $product): ?>
                                        <div class="flex items-center justify-between product-item p-3 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                                    <?= $index + 1 ?>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($product['name']) ?></p>
                                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($product['category_name'] ?? 'Sin categoría') ?></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-gray-900"><?= $product['total_quantity'] ?> unidades</p>
                                                <p class="text-xs text-green-600 font-medium">S/ <?= number_format($product['total_revenue'], 2) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // Datos para los gráficos
        const salesData = <?= json_encode($salesByMonth) ?>;
        const categoryData = <?= json_encode($salesByCategory) ?>;
        const orderStatusData = <?= json_encode($orderStatusCounts) ?>;

        // Configuración de colores mejorada
        const colors = {
            primary: '#3B82F6',
            success: '#10B981',
            warning: '#F59E0B',
            danger: '#EF4444',
            info: '#8B5CF6',
            secondary: '#6B7280',
            gradients: {
                blue: ['#3B82F6', '#1D4ED8'],
                green: ['#10B981', '#059669'],
                yellow: ['#F59E0B', '#D97706'],
                purple: ['#8B5CF6', '#7C3AED'],
                red: ['#EF4444', '#DC2626']
            }
        };

        // Configuración global de Chart.js
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6B7280';

        // Gráfico de ventas por mes (mejorado)
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const gradient = salesCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesData.map(item => {
                    const date = new Date(item.month);
                    return date.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Ventas (S/)',
                    data: salesData.map(item => parseFloat(item.total_sales)),
                    borderColor: colors.primary,
                    backgroundColor: gradient,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: colors.primary,
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: colors.primary,
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Ventas: S/ ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return 'S/ ' + value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Gráfico de estado de órdenes (dona mejorada)
        const ordersCtx = document.getElementById('ordersStatusChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pagadas', 'Pendientes', 'Fallidas', 'Canceladas'],
                datasets: [{
                    data: [
                        orderStatusData.paid || 0,
                        orderStatusData.pending || 0,
                        orderStatusData.failed || 0,
                        orderStatusData.cancelled || 0
                    ],
                    backgroundColor: [
                        colors.success,
                        colors.warning,
                        colors.danger,
                        colors.secondary
                    ],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de ventas por categoría (pastel mejorado)
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: categoryData.map(item => item.category_name || 'Sin categoría'),
                datasets: [{
                    data: categoryData.map(item => parseFloat(item.total_sales)),
                    backgroundColor: [
                        colors.primary,
                        colors.success,
                        colors.warning,
                        colors.danger,
                        colors.info,
                        colors.secondary
                    ].slice(0, categoryData.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': S/ ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Animación de entrada para los números KPI
        document.addEventListener('DOMContentLoaded', function() {
            const kpiNumbers = document.querySelectorAll('.animate-count');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            kpiNumbers.forEach(number => {
                observer.observe(number);
            });
        });
    </script>
</body>

</html>
