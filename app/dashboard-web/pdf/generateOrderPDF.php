<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/orderModel.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Model\OrderModel;

$id = $_GET['id'] ?? null;
if (!$id) die('ID de orden no proporcionado');

$model = new OrderModel($pdo, 'orders');
$order = $model->getDetailedById($id);
if (!$order) die('Orden no encontrada');

// Plantilla HTML (puedes mejorarla)
$html = "<h1>Orden #{$id}</h1>
<p><strong>Cliente:</strong> {$order['client_name']}</p>
<p><strong>Total:</strong> S/ {$order['total_price']}</p>
<p><strong>Estado:</strong> {$order['status']}</p>
<p><strong>Productos:</strong></p>
<ul>";

foreach ($order['items'] as $item) {
    $html .= "<li>{$item['product_name']} - Cant: {$item['quantity']} - S/ {$item['price']}</li>";
}
$html .= "</ul>";

// Configuración Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("orden_{$id}.pdf", ["Attachment" => false]); // Abrir en navegador
