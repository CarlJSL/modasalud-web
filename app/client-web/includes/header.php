<?php
// Obtener contador del carrito
if (!isset($_SESSION['cart_token'])) {
    $_SESSION['cart_token'] = bin2hex(random_bytes(16));
}

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../../dashboard-web/model/carrtempModel.php';
use App\Model\CarrTempModel;

$cartModel = new CarrTempModel($pdo);
$cartItemCount = $cartModel->getCartItemCount($_SESSION['cart_token']);
?>
<!-- Header global ModaSalud -->
<header class="sticky top-0 z-50 bg-white shadow border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-20">
    <!-- Logo y nombre -->
    <div class="flex items-center gap-3">
      <img src="img/tienda.png" alt="ModaSalud" class="w-12 h-12 rounded-full border-2 border-purple-500">
      <div>
        <a href="index.php" class="focus:outline-none">
          <h1 class="text-2xl font-bold text-purple-600 tracking-tight hover:underline">ModaSalud</h1>
        </a>
        <span class="text-xs text-gray-400 font-medium">Tu tienda de moda y salud</span>
      </div>
    </div>
    <!-- Barra de búsqueda funcional -->
    <?php $search = isset($_GET['search']) ? $_GET['search'] : ''; $category = isset($_GET['category']) ? $_GET['category'] : ''; ?>
    <form method="get" action="index.php" class="flex items-center gap-2 w-full max-w-md mx-8">
      <div class="relative w-full">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar productos en ModaSalud" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-pink-400 focus:outline-none text-gray-700" />
      </div>
      <?php if ($category): ?>
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
      <?php endif; ?>
      <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold"><i class="fas fa-search"></i></button>
    </form>
    <!-- Iconos de acción -->
    <div class="flex items-center gap-4">
      <a href="#" class="text-gray-500 hover:text-purple-600 transition" title="Iniciar sesión">
        <i class="fas fa-user text-xl"></i>
      </a>
      <a href="#" class="text-gray-500 hover:text-pink-500 transition" title="Favoritos">
        <i class="fas fa-heart text-xl"></i>
      </a>
      <a href="cart.php" class="relative text-gray-500 hover:text-rose-600 transition" title="Carrito de compras">
        <i class="fas fa-shopping-cart text-xl"></i>
        <?php if ($cartItemCount > 0): ?>
          <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full px-1.5 py-0.5"><?= $cartItemCount ?></span>
        <?php endif; ?>
      </a>
    </div>
  </div>
</header> 