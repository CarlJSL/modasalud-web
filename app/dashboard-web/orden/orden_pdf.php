<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/orderModel.php';

use App\Model\OrderModel;

// Instancia del modelo apuntando a la tabla 'orders'


use Dompdf\Dompdf;
use Dompdf\Options;

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de orden no proporcionado');
}

$orderModel = new OrderModel($pdo);
$order = $orderModel->getDetailedById($id);

if (!$order) {
    die('Orden no encontrada');
}

// Crear HTML para el PDF
ob_start();
?>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #888; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Resumen de Orden #<?= $order['id'] ?></h1>
    <p><strong>Fecha:</strong> <?= $order['created_at'] ?></p>
    <p><strong>Cliente:</strong> <?= $order['client_name'] ?></p>
    <p><strong>Dirección:</strong> <?= $order['address'] ?>, <?= $order['city'] ?></p>
    <p><strong>Estado de la Orden:</strong> <?= $order['status'] ?></p>
    <p><strong>Método de Pago:</strong> <?= $order['payment']['method'] ?? 'N/A' ?></p>
    <p><strong>Estado de Pago:</strong> <?= $order['payment']['status'] ?? 'N/A' ?></p>

    <h2>Productos</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $item): ?>
            <tr>
                <td><?= $item['product_name'] ?></td>
                <td><?= $item['product_description'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>S/ <?= number_format($item['price'], 2) ?></td>
                <td>S/ <?= number_format($item['quantity'] * $item['price'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p style="text-align: right; margin-top: 20px;"><strong>Total:</strong> S/ <?= number_format($order['total_price'], 2) ?></p>
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

// Mostrar el PDF en el navegador
$dompdf->stream("orden_{$order['id']}.pdf", ['Attachment' => false]);
