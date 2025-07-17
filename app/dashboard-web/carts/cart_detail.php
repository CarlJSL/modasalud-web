<?php
session_start();
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/userBaseModel.php';
require_once __DIR__ . '/../model/carrtempModel.php';

use App\Model\CarrTempModel;

// Comprobar autenticación (por simplicidad, usando método directo)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../users/login.php');
    exit;
}

// Verificar que se proporcionó un token de carrito
if (!isset($_GET['token'])) {
    header('Location: cart_manager.php');
    exit;
}

$token = $_GET['token'];
$cartModel = new CarrTempModel($pdo);

// Procesar acciones
$message = '';
$messageType = '';

// Acción: Borrar carrito
if (isset($_POST['delete_cart'])) {
    try {
        $cartModel->clearCart($token);
        $message = "El carrito ha sido eliminado correctamente.";
        $messageType = 'success';
        // Redirigir después de borrar
        header('Refresh: 2; URL=cart_manager.php');
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'danger';
    }
}

// Acción: Refrescar stock
if (isset($_POST['refresh_stock'])) {
    try {
        $cartModel->refreshCartItemsStock($token);
        $message = "Los productos del carrito se han ajustado al stock disponible.";
        $messageType = 'success';
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'danger';
    }
}

// Obtener productos del carrito
$cartItems = $cartModel->getCartItems($token);

// Obtener valor total del carrito
$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}

// Obtener información general del carrito
$sql = "SELECT 
            MIN(added_at) as created_at,
            MAX(added_at) as updated_at
        FROM cart_items_temp
        WHERE cart_token = :token";
$stmt = $pdo->prepare($sql);
$stmt->execute(['token' => $token]);
$cartInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si hay productos con stock insuficiente
$insufficientStock = $cartModel->validateCartStock($token);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include '../includes/head.php'; ?>
    <title>Detalle del Carrito - Dashboard</title>
</head>
<body class="bg-gray-50">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Detalle del Carrito
                <span class="text-sm font-normal text-gray-500">
                    (<?= substr($token, 0, 8) ?>...<?= substr($token, -8) ?>)
                </span>
            </h1>
            <a href="cart_manager.php" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md">
                Volver
            </a>
        </div>
        
        <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-md <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información del carrito -->
            <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Carrito</h2>
                
                <ul class="space-y-3">
                    <li class="flex justify-between">
                        <span class="text-gray-600">Creado:</span>
                        <span class="font-medium"><?= date('d/m/Y H:i', strtotime($cartInfo['created_at'])) ?></span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Última actualización:</span>
                        <span class="font-medium"><?= date('d/m/Y H:i', strtotime($cartInfo['updated_at'])) ?></span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Total productos:</span>
                        <span class="font-medium"><?= count($cartItems) ?></span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Total artículos:</span>
                        <span class="font-medium"><?= array_sum(array_column($cartItems, 'quantity')) ?></span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Valor total:</span>
                        <span class="font-medium">S/ <?= number_format($cartTotal, 2) ?></span>
                    </li>
                </ul>
                
                <div class="mt-6 space-y-3">
                    <form method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este carrito?')">
                        <button type="submit" name="delete_cart" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md">
                            Eliminar Carrito
                        </button>
                    </form>
                    
                    <form method="post">
                        <button type="submit" name="refresh_stock" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-md">
                            Refrescar Stock
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Productos en el carrito -->
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Productos en el Carrito</h2>
                
                <?php if (empty($cartItems)): ?>
                <p class="text-gray-500 italic">Este carrito está vacío.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Producto</th>
                                <th class="px-4 py-2 text-center">Precio</th>
                                <th class="px-4 py-2 text-center">Cantidad</th>
                                <th class="px-4 py-2 text-center">Stock</th>
                                <th class="px-4 py-2 text-center">Subtotal</th>
                                <th class="px-4 py-2 text-center">Seleccionado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                            <tr class="border-b <?= isset($insufficientStock[$item['product_id']]) ? 'bg-red-50' : '' ?>">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <?php if ($item['main_image']): ?>
                                        <img src="/<?= htmlspecialchars($item['main_image']) ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                             class="w-10 h-10 object-cover rounded mr-3">
                                        <?php else: ?>
                                        <div class="w-10 h-10 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        <?php endif; ?>
                                        <span class="font-medium"><?= htmlspecialchars($item['name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">S/ <?= number_format($item['price'], 2) ?></td>
                                <td class="px-4 py-3 text-center"><?= number_format($item['quantity']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <?= number_format($item['stock']) ?>
                                    <?php if (isset($insufficientStock[$item['product_id']])): ?>
                                    <span class="text-red-500 text-xs block">¡Insuficiente!</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">S/ <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($item['selected']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Sí
                                    </span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        No
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($insufficientStock)): ?>
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                    <h3 class="text-sm font-medium text-red-800">¡Advertencia de stock insuficiente!</h3>
                    <p class="text-xs text-red-700 mt-1">
                        Algunos productos no tienen suficiente stock. Usa la opción "Refrescar Stock" para ajustar las cantidades.
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    <script src="../js/scripts.js"></script>
</body>
</html>
