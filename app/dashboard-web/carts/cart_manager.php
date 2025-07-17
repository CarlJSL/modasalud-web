<?php
session_start();
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/userBaseModel.php';
require_once __DIR__ . '/../model/carrtempModel.php';

// Ajusta el namespace según el namespace real definido en userBaseModel.php y carrtempModel.php
// use UserBaseModel;
// use CarrTempModel;



// Procesar acciones
$message = '';
$messageType = '';

if (isset($_POST['cleanup_old_carts'])) {
    $days = isset($_POST['days']) ? (int)$_POST['days'] : 30;
    try {
        $deleted = $cartModel->cleanupOldCarts($days);
        $message = "Se han eliminado $deleted carritos inactivos de más de $days días.";
        $messageType = 'success';
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'danger';
    }
}

// Obtener estadísticas de carritos
$sql = "SELECT 
            COUNT(DISTINCT cart_token) as total_carts,
            SUM(quantity) as total_products,
            COUNT(*) as total_items,
            MAX(added_at) as latest_cart,
            MIN(added_at) as oldest_cart
        FROM cart_items_temp";
$stmt = $pdo->query($sql);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener productos populares en carritos
$popularProducts = $cartModel->getPopularCartProducts(5);

// Obtener carritos activos recientes
$sql = "SELECT 
            cart_token,
            COUNT(*) as items,
            SUM(quantity) as products,
            MAX(added_at) as last_update
        FROM cart_items_temp
        GROUP BY cart_token
        ORDER BY last_update DESC
        LIMIT 10";
$stmt = $pdo->query($sql);
$recentCarts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular el valor total de los carritos
$sql = "SELECT SUM(p.price * c.quantity) as total_value
        FROM cart_items_temp c
        JOIN products p ON c.product_id = p.id";
$stmt = $pdo->query($sql);
$totalValue = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'] ?? 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include '../includes/head.php'; ?>
    <title>Gestión de Carritos Temporales - Dashboard</title>
</head>
<body class="bg-gray-50">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Gestión de Carritos Temporales</h1>
        
        <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-md <?= $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <!-- Estadísticas generales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm text-gray-500 uppercase tracking-wider mb-2">Total de Carritos</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= number_format($stats['total_carts']) ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm text-gray-500 uppercase tracking-wider mb-2">Total de Productos</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= number_format($stats['total_products']) ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm text-gray-500 uppercase tracking-wider mb-2">Valor Total (S/)</h3>
                <p class="text-3xl font-bold text-indigo-600"><?= number_format($totalValue, 2) ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-sm text-gray-500 uppercase tracking-wider mb-2">Último Carrito</h3>
                <p class="text-lg font-semibold text-indigo-600">
                    <?= date('d/m/Y H:i', strtotime($stats['latest_cart'])) ?>
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Productos populares -->
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Productos Populares en Carritos</h2>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Producto</th>
                                <th class="px-4 py-2 text-center">Precio</th>
                                <th class="px-4 py-2 text-center">En Carritos</th>
                                <th class="px-4 py-2 text-center">Total Carritos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($popularProducts as $product): ?>
                            <tr class="border-b">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <?php if ($product['main_image']): ?>
                                        <img src="/<?= htmlspecialchars($product['main_image']) ?>" 
                                             alt="<?= htmlspecialchars($product['name']) ?>" 
                                             class="w-10 h-10 object-cover rounded mr-3">
                                        <?php else: ?>
                                        <div class="w-10 h-10 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        <?php endif; ?>
                                        <span class="font-medium"><?= htmlspecialchars($product['name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">S/ <?= number_format($product['price'], 2) ?></td>
                                <td class="px-4 py-3 text-center"><?= number_format($product['total_in_carts']) ?></td>
                                <td class="px-4 py-3 text-center"><?= number_format($product['cart_count']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Acciones de limpieza -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Mantenimiento</h2>
                
                <form method="post" class="mb-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Limpiar carritos antiguos
                        </label>
                        <div class="flex">
                            <input type="number" name="days" min="1" value="30" 
                                   class="w-20 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="inline-flex items-center px-3 rounded-none border border-l-0 border-gray-300 bg-gray-50 text-gray-500">
                                días
                            </span>
                            <button type="submit" name="cleanup_old_carts" 
                                    class="ml-2 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md">
                                Limpiar
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Elimina carritos que no han sido actualizados en los últimos X días.
                        </p>
                    </div>
                </form>
                
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Estadísticas adicionales</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between">
                            <span class="text-gray-600">Carrito más antiguo:</span>
                            <span class="font-medium"><?= date('d/m/Y', strtotime($stats['oldest_cart'])) ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600">Productos por carrito:</span>
                            <span class="font-medium"><?= number_format($stats['total_carts'] > 0 ? $stats['total_products'] / $stats['total_carts'] : 0, 1) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Carritos recientes -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Carritos Recientes</h2>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">Token (parcial)</th>
                            <th class="px-4 py-2 text-center">Productos</th>
                            <th class="px-4 py-2 text-center">Artículos</th>
                            <th class="px-4 py-2 text-center">Última actualización</th>
                            <th class="px-4 py-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCarts as $cart): ?>
                        <tr class="border-b">
                            <td class="px-4 py-3">
                                ...<?= substr($cart['cart_token'], -8) ?>
                            </td>
                            <td class="px-4 py-3 text-center"><?= number_format($cart['products']) ?></td>
                            <td class="px-4 py-3 text-center"><?= number_format($cart['items']) ?></td>
                            <td class="px-4 py-3 text-center"><?= date('d/m/Y H:i', strtotime($cart['last_update'])) ?></td>
                            <td class="px-4 py-3 text-center">
                                <a href="cart_detail.php?token=<?= urlencode($cart['cart_token']) ?>" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    Ver detalles
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    <script src="../js/scripts.js"></script>
</body>
</html>
