<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/orderModel.php';

use App\Model\OrderModel;
use Dompdf\Dompdf;
use Dompdf\Options;

// Cargar configuración de la empresa
$companyConfig = include(__DIR__ . '/../../../config/company_config.php');
$company = $companyConfig['company'];
$pdfConfig = $companyConfig['pdf'];
$orderSettings = $companyConfig['order_settings'];
$businessInfo = $companyConfig['business_info'];

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de orden no proporcionado');
}

// Instancia del modelo apuntando a la tabla 'orders'
$orderModel = new OrderModel($pdo);
$order = $orderModel->getDetailedById($id);

if (!$order) {
    die('Orden no encontrada');
}

// Crear HTML para el PDF
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            margin: 10px;
            color: #222;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid <?= $pdfConfig['colors']['primary'] ?>;
            padding-bottom: 8px;
            background: linear-gradient(90deg, #f8fafc 60%, #e0e7ff 100%);
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: <?= $pdfConfig['colors']['primary'] ?>;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .company-info {
            font-size: 9px;
            color: #666;
        }
        .company-social {
            font-size: 9px;
            color: #888;
            margin-top: 2px;
        }
        .created-by-box {
            border: 1px solid #e5e7eb;
            background: #f3f4f6;
            border-radius: 6px;
            padding: 7px 12px 5px 12px;
            margin: 10px 0 8px 0;
            font-size: 10px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .created-by-label {
            font-weight: bold;
            color: <?= $pdfConfig['colors']['primary'] ?>;
            margin-right: 4px;
        }
        .created-by-user {
            font-weight: bold;
            color: #222;
        }
        .created-by-email {
            color: #666;
            font-size: 9px;
            margin-left: 6px;
        }
        .order-title {
            font-size: 14px;
            color: #333;
            margin: 12px 0 7px 0;
            text-align: left;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .info-box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 10px;
            background: #f9fafb;
            box-shadow: 0 1px 2px #e0e7ff44;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .info-label {
            font-weight: bold;
            color: #444;
            min-width: 70px;
        }
        .info-value {
            color: #222;
            text-align: right;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 0 0;
        }
        .products-table th, .products-table td {
            border: 1px solid #e5e7eb;
            padding: 4px 3px;
            font-size: 9px;
        }
        .products-table th {
            background: #e0e7ff;
            color: #333;
            font-weight: bold;
        }
        .total-section {
            margin-top: 10px;
            text-align: right;
            background: #f3f4f6;
            border-radius: 6px;
            padding: 7px 10px 4px 0;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px #e0e7ff33;
        }
        .total-row {
            font-size: 10px;
            margin: 2px 0;
        }
        .total-final {
            font-size: 13px;
            font-weight: bold;
            color: <?= $pdfConfig['colors']['primary'] ?>;
            border-top: 1px solid <?= $pdfConfig['colors']['primary'] ?>;
            margin-top: 4px;
            padding-top: 3px;
            letter-spacing: 1px;
        }
        .coupon-mini {
            display: inline-block;
            background: #fffbe6;
            border: 1px dashed #ffc107;
            color: #856404;
            font-size: 9px;
            border-radius: 4px;
            padding: 2px 8px 2px 4px;
            margin: 0 0 6px 0;
            vertical-align: middle;
        }
        .coupon-section {
            margin: 10px 0 0 0;
            text-align: left;
        }
        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 8px;
            color: #888;
            border-top: 1px solid #e5e7eb;
            padding-top: 7px;
        }
    </style>
</head>
<body>
    <!-- Header de la empresa -->
    <div class="header">
        <div class="company-name"><?= htmlspecialchars($company['name']) ?></div>
        <div class="company-info">
            <?php if (!empty($company['ruc'])): ?>RUC: <?= htmlspecialchars($company['ruc']) ?> | <?php endif; ?>
            <?= htmlspecialchars($company['address']) ?>
            <?php if (!empty($company['city'])): ?>, <?= htmlspecialchars($company['city']) ?><?php endif; ?>
            <?php if (!empty($company['country'])): ?>, <?= htmlspecialchars($company['country']) ?><?php endif; ?>
            <br>Email: <?= htmlspecialchars($company['email']) ?> | Tel: <?= htmlspecialchars($company['phone']) ?>
        </div>
        <?php if (!empty($company['social_media'])): ?>
        <div class="company-social">
            <?php if (!empty($company['social_media']['facebook'])): ?>
                Facebook: <?= htmlspecialchars($company['social_media']['facebook']) ?>
            <?php endif; ?>
            <?php if (!empty($company['social_media']['instagram'])): ?>
                | Instagram: <?= htmlspecialchars($company['social_media']['instagram']) ?>
            <?php endif; ?>
            <?php if (!empty($company['social_media']['whatsapp'])): ?>
                | WhatsApp: <?= htmlspecialchars($company['social_media']['whatsapp']) ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <!-- Usuario que creó la orden -->
    <?php if (!empty($order['created_by_username'])): ?>
    <div class="created-by-box">
        <span class="created-by-label">Creada por:</span>
        <span class="created-by-user"><?= htmlspecialchars($order['created_by_username']) ?></span>
        <?php if (!empty($order['created_by_email'])): ?>
        <span class="created-by-email">Email: <?= htmlspecialchars($order['created_by_email']) ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <!-- Título de la orden -->
    <div class="order-title">
        N° DE VENTA #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
    </div>
    <!-- Info principal -->
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Fecha:</span>
            <span class="info-value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span class="info-value"><?= htmlspecialchars($order['client_name']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value"><?= htmlspecialchars($order['client_email']) ?></span>
        </div>
        <?php if (!empty($order['client_phone'])): ?>
        <div class="info-row">
            <span class="info-label">Teléfono:</span>
            <span class="info-value"><?= htmlspecialchars($order['client_phone']) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Dirección:</span>
            <span class="info-value"><?= htmlspecialchars($order['delivery_address']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Ciudad:</span>
            <span class="info-value"><?= htmlspecialchars($order['delivery_city']) ?>, <?= htmlspecialchars($order['delivery_region']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                <span class="status-badge status-<?= strtolower($order['status']) ?>">
                    <?= $order['status'] == 'PENDING' ? 'Pendiente' : ($order['status'] == 'COMPLETED' ? 'Completada' : 'Cancelada') ?>
                </span>
            </span>
        </div>
    </div>
    <!-- Tabla de productos -->
    <table class="products-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $subtotal = 0;
            foreach ($order['items'] as $item): 
                $itemTotal = $item['quantity'] * $item['price'];
                $subtotal += $itemTotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td style="text-align: center;"><?= $item['quantity'] ?></td>
                <td style="text-align: right;"><?= $pdfConfig['currency'] ?> <?= number_format($item['price'], 2) ?></td>
                <td style="text-align: right;"><?= $pdfConfig['currency'] ?> <?= number_format($itemTotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Totales -->
    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span><?= $pdfConfig['currency'] ?> <?= number_format($subtotal, 2) ?></span>
        </div>
        <?php 
        $discountAmount = floatval($order['discount_amount'] ?? 0);
        if ($discountAmount == 0 && !empty($order['coupon_code']) && !empty($order['coupon_value'])) {
            if ($order['coupon_type'] == 'PERCENTAGE') {
                $discountAmount = ($subtotal * floatval($order['coupon_value'])) / 100;
            } else {
                $discountAmount = floatval($order['coupon_value']);
            }
        }
        if ($discountAmount > 0): ?>
        <div class="total-row">
            <span>Descuento:</span>
            <span>-<?= $pdfConfig['currency'] ?> <?= number_format($discountAmount, 2) ?></span>
        </div>
        <?php endif; ?>
        <div class="total-final">
            <span>TOTAL:</span>
            <span><?= $pdfConfig['currency'] ?> <?= number_format($order['total_price'], 2) ?></span>
        </div>
    </div>
    <!-- Cupón usado (sección separada y clara) -->
    <?php if (!empty($order['coupon_code'])): ?>
    <div class="coupon-section">
        <span class="coupon-mini">
            Cupón usado: <b><?= htmlspecialchars($order['coupon_code']) ?></b>
            <?php if ($order['coupon_type'] == 'PERCENTAGE'): ?>
                (<?= $order['coupon_value'] ?>%)
            <?php else: ?>
                (<?= $pdfConfig['currency'] ?><?= number_format($order['coupon_value'], 2) ?>)
            <?php endif; ?>
        </span>
    </div>
    <?php endif; ?>
    <!-- Footer -->
    <div class="footer">
        <?= htmlspecialchars($pdfConfig['footer_message']) ?> | <?= htmlspecialchars($company['email']) ?> | <?= htmlspecialchars($company['phone']) ?>
        <br>Documento generado el <?= date('d/m/Y H:i:s') ?>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

// Configurar DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Mostrar el PDF en el navegador o forzar descarga
$downloadMode = isset($_GET['download']) && $_GET['download'] == '1';

// Generar nombre del archivo basado en configuración
$dateFormat = date('Ymd');
$fileName = str_replace(
    ['{order_id}', '{date}'],
    [str_pad($order['id'], 6, '0', STR_PAD_LEFT), $dateFormat],
    $orderSettings['pdf_name_format']
);

$dompdf->stream("{$fileName}.pdf", ['Attachment' => $downloadMode]);
