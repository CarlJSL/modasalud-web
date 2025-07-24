<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/reportModel.php';

use App\Model\ReportModel;
use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

$reportModel = new ReportModel($pdo);

// Obtener parámetros
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$filters = [
    'payment_method' => $_GET['payment_method'] ?? '',
    'order_source' => $_GET['order_source'] ?? ''
];

// Generar datos del reporte
$salesReport = $reportModel->getSalesReport($dateFrom, $dateTo, $filters);
$dailySales = $reportModel->getDailySales($dateFrom, $dateTo);
$topProducts = $reportModel->getTopProducts($dateFrom, $dateTo, 10);
$salesByCategory = $reportModel->getSalesByCategory($dateFrom, $dateTo);
$topCustomers = $reportModel->getTopCustomers($dateFrom, $dateTo, 10);
$paymentMethods = $reportModel->getPaymentMethodsReport($dateFrom, $dateTo);
$periodComparison = $reportModel->getPeriodComparison($dateFrom, $dateTo);

// Formatear fechas para el reporte
$dateFromFormatted = date('d/m/Y', strtotime($dateFrom));
$dateToFormatted = date('d/m/Y', strtotime($dateTo));

// HTML del PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1f2937;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        
        .header .subtitle {
            color: #6b7280;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        
        .summary-cell .value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .summary-cell .label {
            color: #6b7280;
            font-size: 11px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .comparison {
            font-size: 10px;
            margin-top: 3px;
        }
        
        .comparison.positive {
            color: #059669;
        }
        
        .comparison.negative {
            color: #dc2626;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
        }
        
        .chart-placeholder {
            width: 100%;
            height: 200px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ventas</h1>
        <div class="subtitle">Período: ' . $dateFromFormatted . ' - ' . $dateToFormatted . '</div>
        <div class="subtitle">Generado el: ' . date('d/m/Y H:i') . '</div>
    </div>

    <!-- Resumen Ejecutivo -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="summary-cell">
                <div class="value">S/ ' . number_format($salesReport['total_revenue'] ?? 0, 2) . '</div>
                <div class="label">Ingresos Totales</div>';

if (isset($periodComparison['total_revenue'])) {
    $change = $periodComparison['total_revenue']['percentage_change'];
    $class = $change >= 0 ? 'positive' : 'negative';
    $symbol = $change >= 0 ? '+' : '';
    $html .= '<div class="comparison ' . $class . '">' . $symbol . $change . '% vs período anterior</div>';
}

$html .= '
            </div>
            <div class="summary-cell">
                <div class="value">' . number_format($salesReport['total_orders'] ?? 0) . '</div>
                <div class="label">Total Órdenes</div>';

if (isset($periodComparison['total_orders'])) {
    $change = $periodComparison['total_orders']['percentage_change'];
    $class = $change >= 0 ? 'positive' : 'negative';
    $symbol = $change >= 0 ? '+' : '';
    $html .= '<div class="comparison ' . $class . '">' . $symbol . $change . '% vs período anterior</div>';
}

$html .= '
            </div>
            <div class="summary-cell">
                <div class="value">S/ ' . number_format($salesReport['average_ticket'] ?? 0, 2) . '</div>
                <div class="label">Ticket Promedio</div>';

if (isset($periodComparison['average_ticket'])) {
    $change = $periodComparison['average_ticket']['percentage_change'];
    $class = $change >= 0 ? 'positive' : 'negative';
    $symbol = $change >= 0 ? '+' : '';
    $html .= '<div class="comparison ' . $class . '">' . $symbol . $change . '% vs período anterior</div>';
}

$html .= '
            </div>
            <div class="summary-cell">
                <div class="value">' . number_format($salesReport['unique_customers'] ?? 0) . '</div>
                <div class="label">Clientes Únicos</div>';

if (isset($periodComparison['unique_customers'])) {
    $change = $periodComparison['unique_customers']['percentage_change'];
    $class = $change >= 0 ? 'positive' : 'negative';
    $symbol = $change >= 0 ? '+' : '';
    $html .= '<div class="comparison ' . $class . '">' . $symbol . $change . '% vs período anterior</div>';
}

$html .= '
            </div>
        </div>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="section">
        <div class="section-title">Productos Más Vendidos</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Ingresos</th>
                </tr>
            </thead>
            <tbody>';

foreach (array_slice($topProducts, 0, 10) as $index => $product) {
    $html .= '
                <tr>
                    <td class="text-center">' . ($index + 1) . '</td>
                    <td>' . htmlspecialchars($product['name']) . '</td>
                    <td>' . htmlspecialchars($product['category_name'] ?? 'Sin categoría') . '</td>
                    <td class="text-right">' . number_format($product['total_quantity']) . '</td>
                    <td class="text-right">S/ ' . number_format($product['total_revenue'], 2) . '</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>

    <!-- Ventas por Categoría -->
    <div class="section">
        <div class="section-title">Ventas por Categoría</div>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th class="text-right">Cantidad Vendida</th>
                    <th class="text-right">Ingresos</th>
                    <th class="text-right">% del Total</th>
                </tr>
            </thead>
            <tbody>';

$totalCategoryRevenue = array_sum(array_column($salesByCategory, 'total_revenue'));

foreach ($salesByCategory as $category) {
    $percentage = $totalCategoryRevenue > 0 ? ($category['total_revenue'] / $totalCategoryRevenue) * 100 : 0;
    $html .= '
                <tr>
                    <td>' . htmlspecialchars($category['category_name']) . '</td>
                    <td class="text-right">' . number_format($category['total_quantity']) . '</td>
                    <td class="text-right">S/ ' . number_format($category['total_revenue'], 2) . '</td>
                    <td class="text-right">' . number_format($percentage, 1) . '%</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>

    <!-- Mejores Clientes -->
    <div class="section">
        <div class="section-title">Mejores Clientes</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th class="text-right">Órdenes</th>
                    <th class="text-right">Total Gastado</th>
                </tr>
            </thead>
            <tbody>';

foreach (array_slice($topCustomers, 0, 10) as $index => $customer) {
    $html .= '
                <tr>
                    <td class="text-center">' . ($index + 1) . '</td>
                    <td>' . htmlspecialchars($customer['name']) . '</td>
                    <td>' . htmlspecialchars($customer['email']) . '</td>
                    <td class="text-right">' . number_format($customer['orders_count']) . '</td>
                    <td class="text-right">S/ ' . number_format($customer['total_spent'], 2) . '</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>

    <!-- Métodos de Pago -->
    <div class="section">
        <div class="section-title">Métodos de Pago</div>
        <table>
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th class="text-right">Transacciones</th>
                    <th class="text-right">Monto Total</th>
                    <th class="text-right">% del Total</th>
                </tr>
            </thead>
            <tbody>';

$totalPaymentRevenue = array_sum(array_column($paymentMethods, 'total_amount'));

foreach ($paymentMethods as $payment) {
    $percentage = $totalPaymentRevenue > 0 ? ($payment['total_amount'] / $totalPaymentRevenue) * 100 : 0;
    $methodName = '';
    switch ($payment['payment_method']) {
        case 'YAPE':
            $methodName = 'Yape';
            break;
        case 'PLIN':
            $methodName = 'Plin';
            break;
        case 'TRANSFER':
            $methodName = 'Transferencia';
            break;
        case 'CASH':
            $methodName = 'Efectivo';
            break;
        default:
            $methodName = $payment['payment_method'];
    }
    
    $html .= '
                <tr>
                    <td>' . htmlspecialchars($methodName) . '</td>
                    <td class="text-right">' . number_format($payment['transaction_count']) . '</td>
                    <td class="text-right">S/ ' . number_format($payment['total_amount'], 2) . '</td>
                    <td class="text-right">' . number_format($percentage, 1) . '%</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>

    <!-- Ventas Diarias (últimos días del período) -->
    <div class="section">
        <div class="section-title">Ventas Diarias (Últimos 7 días del período)</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th class="text-right">Órdenes</th>
                    <th class="text-right">Ingresos</th>
                    <th class="text-right">Ticket Promedio</th>
                </tr>
            </thead>
            <tbody>';

foreach (array_slice($dailySales, -7) as $day) {
    $avgTicket = $day['orders_count'] > 0 ? $day['daily_revenue'] / $day['orders_count'] : 0;
    $html .= '
                <tr>
                    <td>' . date('d/m/Y', strtotime($day['sale_date'])) . '</td>
                    <td class="text-right">' . number_format($day['orders_count']) . '</td>
                    <td class="text-right">S/ ' . number_format($day['daily_revenue'], 2) . '</td>
                    <td class="text-right">S/ ' . number_format($avgTicket, 2) . '</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Gestión de E-commerce</p>
        <p>Fecha de generación: ' . date('d/m/Y H:i:s') . '</p>
    </div>
</body>
</html>';

// Generar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Nombre del archivo
$filename = 'reporte_ventas_' . date('Ymd_His') . '.pdf';

// Enviar PDF al navegador
$dompdf->stream($filename, array('Attachment' => true));
?>
