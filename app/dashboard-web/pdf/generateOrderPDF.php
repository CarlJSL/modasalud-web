<?php
/**
 * Utilidad para generar PDFs de órdenes
 * Este archivo facilita la generación automática de PDFs
 */

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/orderModel.php';

use App\Model\OrderModel;
use Dompdf\Dompdf;
use Dompdf\Options;

$id = $_GET['id'] ?? null;
$download = isset($_GET['download']) && $_GET['download'] == '1';

if (!$id) {
    die('ID de orden no proporcionado');
}

$model = new OrderModel($pdo, 'orders');
$order = $model->getDetailedById($id);

if (!$order) {
    die('Orden no encontrada');
}

// Crear HTML para el PDF con mejor diseño
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 10px;
            color: #666;
        }
        .order-title {
            font-size: 20px;
            color: #333;
            margin: 20px 0;
            text-align: center;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .info-title {
            font-weight: bold;
            font-size: 12px;
            color: #4f46e5;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-item {
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .products-table th {
            background-color: #4f46e5;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        .products-table td {
            border: 1px solid #ddd;
            padding: 10px 8px;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-final {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
            border-top: 2px solid #4f46e5;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">E-Commerce Moda y Salud</div>
        <div class="company-info">
            Especialistas en productos de moda y salud<br>
            Email: info@modaysalud.com | Teléfono: (01) 123-4567
        </div>
    </div>

    <div class="order-title">ORDEN DE COMPRA #' . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . '</div>

    <div class="info-box">
        <div class="info-title">INFORMACIÓN DE LA ORDEN</div>
        <div class="info-item">
            <span class="info-label">Número de Orden:</span>
            #' . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . '
        </div>
        <div class="info-item">
            <span class="info-label">Fecha:</span>
            ' . date('d/m/Y H:i', strtotime($order['created_at'])) . '
        </div>
        <div class="info-item">
            <span class="info-label">Estado:</span>
            ' . ($order['status'] == 'PENDING' ? 'Pendiente' : ($order['status'] == 'COMPLETED' ? 'Completada' : 'Cancelada')) . '
        </div>
    </div>

    <div class="info-box">
        <div class="info-title">INFORMACIÓN DEL CLIENTE</div>
        <div class="info-item">
            <span class="info-label">Nombre:</span>
            ' . htmlspecialchars($order['client_name']) . '
        </div>
        <div class="info-item">
            <span class="info-label">Email:</span>
            ' . htmlspecialchars($order['client_email']) . '
        </div>
        <div class="info-item">
            <span class="info-label">Dirección:</span>
            ' . htmlspecialchars($order['delivery_address']) . '
        </div>
        <div class="info-item">
            <span class="info-label">Ciudad:</span>
            ' . htmlspecialchars($order['delivery_city']) . ', ' . htmlspecialchars($order['delivery_region']) . '
        </div>
    </div>

    <table class="products-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>';

$subtotal = 0;
foreach ($order['items'] as $item) {
    $itemTotal = $item['quantity'] * $item['price'];
    $subtotal += $itemTotal;
    $html .= '
            <tr>
                <td><strong>' . htmlspecialchars($item['product_name']) . '</strong></td>
                <td style="text-align: center;">' . $item['quantity'] . '</td>
                <td style="text-align: right;">S/ ' . number_format($item['price'], 2) . '</td>
                <td style="text-align: right;">S/ ' . number_format($itemTotal, 2) . '</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="total-section">
        <div>Subtotal: S/ ' . number_format($subtotal, 2) . '</div>';

if ($order['discount_amount'] > 0) {
    $html .= '<div>Descuento: -S/ ' . number_format($order['discount_amount'], 2) . '</div>';
}

$html .= '
        <div class="total-final">
            TOTAL: S/ ' . number_format($order['total_price'], 2) . '
        </div>
    </div>

    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p>Para consultas o reclamos, contáctenos: info@modaysalud.com | (01) 123-4567</p>
        <p>Documento generado el ' . date('d/m/Y H:i:s') . '</p>
    </div>
</body>
</html>';

// Configurar DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Mostrar el PDF en el navegador o forzar descarga
$filename = "orden_" . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . ".pdf";
$dompdf->stream($filename, ['Attachment' => $download]);
?>
