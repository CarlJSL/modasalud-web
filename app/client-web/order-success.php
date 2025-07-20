<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/orderModel.php';
require_once __DIR__ . '/../../config/company_config.php';

use App\Model\OrderModel;

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header('Location: index.php');
    exit;
}

$orderModel = new OrderModel($pdo);
$order = $orderModel->getDetailedById($order_id);

if (!$order) {
    header('Location: index.php');
    exit;
}

$company = require(__DIR__ . '/../../config/company_config.php');
$whatsapp = $company['company']['social_media']['whatsapp'] ?? '';
$company_phone = $company['company']['phone'] ?? '';
$company_email = $company['company']['email'] ?? '';
$company_name = $company['company']['name'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido confirmado | ModaSalud</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans">
  <?php include 'includes/header.php'; ?>
  
  <main class="max-w-4xl mx-auto px-4 py-12">
    <div class="text-center mb-8">
      <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-check-circle text-green-600 text-3xl"></i>
      </div>
      <h1 class="text-3xl font-bold text-gray-900 mb-2">¡Pedido confirmado!</h1>
      <p class="text-lg text-gray-600">Tu pedido #<?= $order['id'] ?> ha sido procesado exitosamente</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
      <!-- Información del pedido -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Información del pedido</h2>
        
        <div class="space-y-3">
          <div class="flex justify-between">
            <span class="text-gray-600">Número de pedido:</span>
            <span class="font-semibold">#<?= $order['id'] ?></span>
          </div>
          
          <div class="flex justify-between">
            <span class="text-gray-600">Fecha:</span>
            <span class="font-semibold"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
          </div>
          
          <div class="flex justify-between">
            <span class="text-gray-600">Estado:</span>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
              <?= $order['status'] === 'PENDING' ? 'Pendiente' : $order['status'] ?>
            </span>
          </div>
          
          <div class="flex justify-between">
            <span class="text-gray-600">Total:</span>
            <span class="text-xl font-bold text-purple-700">S/ <?= number_format($order['total_price'], 2) ?></span>
          </div>
        </div>
      </div>

      <!-- Información de entrega -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Información de entrega</h2>
        
        <div class="space-y-3">
          <div>
            <span class="text-gray-600 block">Cliente:</span>
            <span class="font-semibold"><?= htmlspecialchars($order['client_name']) ?></span>
          </div>
          
          <div>
            <span class="text-gray-600 block">Email:</span>
            <span class="font-semibold"><?= htmlspecialchars($order['client_email']) ?></span>
          </div>
          
          <div>
            <span class="text-gray-600 block">Teléfono:</span>
            <span class="font-semibold"><?= htmlspecialchars($order['client_phone'] ?? 'No registrado') ?></span>
          </div>
          
          <div>
            <span class="text-gray-600 block">Dirección:</span>
            <span class="font-semibold">
              <?= htmlspecialchars($order['delivery_address']) ?><br>
              <?= htmlspecialchars($order['delivery_city']) ?>, <?= htmlspecialchars($order['delivery_region']) ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Productos del pedido -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Productos pedidos</h2>
      
      <div class="space-y-4">
        <?php foreach ($order['items'] as $item): ?>
        <div class="flex items-center space-x-4 py-3 border-b border-gray-100 last:border-b-0">
          <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
            <i class="fas fa-tshirt text-gray-400 text-xl"></i>
          </div>
          <div class="flex-1">
            <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($item['product_name']) ?></h3>
            <p class="text-sm text-gray-600">Talla: <?= htmlspecialchars($item['product_size']) ?></p>
            <p class="text-sm text-gray-600">Cantidad: <?= $item['quantity'] ?></p>
          </div>
          <div class="text-right">
            <p class="font-bold text-purple-700">S/ <?= number_format($item['price'] * $item['quantity'], 2) ?></p>
            <p class="text-xs text-gray-500">S/ <?= number_format($item['price'], 2) ?> c/u</p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Información de pago -->
    <?php if ($order['payment']): ?>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Información de pago</h2>
      
      <div class="space-y-3">
        <div class="flex justify-between">
          <span class="text-gray-600">Método de pago:</span>
          <span class="font-semibold">
            <?php
            $methods = [
              'YAPE' => 'Yape',
              'PLIN' => 'Plin', 
              'TRANSFER' => 'Transferencia',
              'CASH' => 'Pago contra entrega'
            ];
            echo $methods[$order['payment']['method']] ?? $order['payment']['method'];
            ?>
          </span>
        </div>
        
        <div class="flex justify-between">
          <span class="text-gray-600">Estado del pago:</span>
          <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
            <?= $order['payment']['status'] === 'PENDING' ? 'Pendiente' : $order['payment']['status'] ?>
          </span>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Acciones -->
    <div class="text-center space-x-4">
      <a href="index.php" class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition">
        <i class="fas fa-arrow-left mr-2"></i>
        Seguir comprando
      </a>

      <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsapp) ?>?text=Hola,%20quiero%20consultar%20sobre%20mi%20pedido%20%23<?= $order['id'] ?>%20en%20<?= urlencode($company_name) ?>" target="_blank" class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition">
        <i class="fab fa-whatsapp mr-2"></i>
        Contactar por WhatsApp
      </a>
    </div>

    <!-- Información adicional -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
      <h3 class="font-semibold text-blue-900 mb-2">¿Qué sigue?</h3>
      <ul class="text-sm text-blue-800 space-y-1">
        <li>• Te contactaremos pronto para confirmar tu pedido</li>
        <li>• Recibirás actualizaciones por email sobre el estado de tu pedido</li>
        <li>• El tiempo de entrega estimado es de 2-5 días hábiles</li>
        <li>• Si tienes preguntas, puedes contactarnos al WhatsApp <b><?= htmlspecialchars($whatsapp) ?></b>, teléfono <b><?= htmlspecialchars($company_phone) ?></b> o email <b><?= htmlspecialchars($company_email) ?></b></li>
      </ul>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>

  <script>
    // Limpiar sesión del carrito después de mostrar el éxito
    setTimeout(() => {
      fetch('cart-ajax.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear'
      });
    }, 1000);
  </script>
</body>
</html>
