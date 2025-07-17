<?php
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../../dashboard-web/model/productModel.php';

use App\Model\ProductModel;

$productModel = new ProductModel($pdo);

// Procesar filtros desde GET
$filters = ['status' => 'ACTIVE'];
if (!empty($_GET['size'])) {
    // Si hay varias tallas, mostrar productos de cualquiera de esas tallas
    $sizes = (array)$_GET['size'];
    if (count($sizes) === 1) {
        $filters['size'] = $sizes[0];
    } else {
        // Filtro personalizado para varias tallas
        $quoted = array_map(function($s) use ($pdo) { return $pdo->quote($s); }, $sizes);
        $filters['_custom_size_filter'] = 'p.size IN (' . implode(',', $quoted) . ')';
    }
}
if (!empty($_GET['subcategory'])) {
    $subcats = (array)$_GET['subcategory'];
    if (count($subcats) === 1) {
        $filters['product_category'] = $subcats[0];
    } else {
        $quoted = array_map(function($s) use ($pdo) { return $pdo->quote($s); }, $subcats);
        $filters['_custom_subcategory_filter'] = 'pc.name IN (' . implode(',', $quoted) . ')';
    }
}
if (isset($_GET['price_min']) && is_numeric($_GET['price_min'])) {
    $filters['price_min'] = (float)$_GET['price_min'];
}
if (isset($_GET['price_max']) && is_numeric($_GET['price_max'])) {
    $filters['price_max'] = (float)$_GET['price_max'];
}
if (!empty($_GET['category'])) {
    $filters['category'] = $_GET['category'];
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Puedes agregar más filtros aquí si lo deseas

// Modificar getAll para soportar filtro personalizado de talla múltiple
$products = $productModel->getAll(20, 0, $search, $filters);
?>
<div class="bg-white rounded-xl p-6 shadow flex-1 w-full h-full flex flex-col">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
    <h2 class="text-2xl font-bold text-purple-700">Nuestros Productos</h2>
    <div class="text-sm text-gray-500">Mostrando <?= count($products) ?> productos</div>
  </div>
  <?php if (count($products) === 0): ?>
    <div class="flex flex-col items-center justify-center flex-1 py-16">
      <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
      <p class="text-lg text-gray-500 mb-2">No hay productos en esta categoría o con estos filtros.</p>
      <a href="index.php" class="mt-2 px-6 py-2 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">Ver todos los productos</a>
    </div>
  <?php else: ?>
    <div class="grid justify-center <?php
      if (count($products) === 1) echo 'grid-cols-1';
      elseif (count($products) === 2) echo 'grid-cols-1 sm:grid-cols-2';
      elseif (count($products) === 3) echo 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3';
      else echo 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4';
    ?> gap-8 flex-1">
      <?php foreach ($products as $product): ?>
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:border-pink-400 border-2 border-transparent transition flex flex-col w-full p-0 min-h-[400px] max-w-xs mx-auto max-h-[400px] h-[480px]">
          <div class="flex flex-col flex-1">
            <div class="relative flex items-center justify-center bg-gray-50 rounded-t-2xl" style="height:220px;">
              <?php if (!empty($product['main_image'])): ?>
                <img src="/<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="object-contain h-40 w-full rounded-t-2xl mx-auto" style="max-height:180px;">
              <?php else: ?>
                <div class="w-full h-40 flex items-center justify-center rounded-t-2xl"><i class='fas fa-image text-5xl text-gray-300'></i></div>
              <?php endif; ?>
              <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
                <?php if ($product['stock'] <= 10 && $product['stock'] > 0): ?>
                  <span class="bg-yellow-400 text-white text-xs font-bold px-2 py-0.5 rounded">¡Stock bajo!</span>
                <?php endif; ?>
                <?php if ($product['stock'] == 0): ?>
                  <span class="bg-rose-500 text-white text-xs font-bold px-2 py-0.5 rounded">Agotado</span>
                <?php endif; ?>
                <?php if (rand(0, 10) > 7): ?>
                  <span class="bg-pink-500 text-white text-xs font-bold px-2 py-0.5 rounded">CYBER WOW</span>
                <?php endif; ?>
              </div>
              <button class="absolute top-2 right-2 bg-white/80 hover:bg-pink-100 rounded-full p-2 shadow transition" title="Favorito">
                <i class="fa-regular fa-heart text-pink-500 text-lg"></i>
              </button>
            </div>
            <div class="flex-1 flex flex-col justify-between px-6 pt-4 pb-2">
              <div>
                <div class="text-xs text-gray-400 mb-1"><?= htmlspecialchars($product['category_name'] ?? 'ModaSalud') ?></div>
                <?php if (!empty($product['product_category_name'])): ?>
                  <div class="text-xs text-pink-500 mb-1 font-medium"><?= htmlspecialchars($product['product_category_name']) ?></div>
                <?php endif; ?>
                <h3 class="font-semibold text-gray-900 mb-2 text-lg line-clamp-2"><a href="product.php?id=<?= $product['id'] ?>" class="hover:text-purple-600"><?= htmlspecialchars($product['name']) ?></a></h3>
              </div>
            </div>
          </div>
          <div class="flex items-end justify-between gap-2 px-6 pb-3 pt-1 mt-auto">
            <span class="text-base font-bold text-purple-700">S/ <?= number_format($product['price'], 2) ?></span>
            <div class="flex gap-2">
              <a href="product.php?id=<?= $product['id'] ?>" class="flex items-center gap-1 px-2 py-1.5 rounded-lg font-semibold text-white text-xs transition bg-pink-500 hover:bg-pink-600" title="Ver más">
                <i class="fas fa-eye"></i>
              </a>
              <?php if ($product['stock'] > 0): ?>
                <button onclick="addToCart(<?= $product['id'] ?>)" 
                        data-product-id="<?= $product['id'] ?>" 
                        class="flex items-center gap-1 px-2 py-1.5 rounded-lg font-semibold text-white text-xs transition bg-purple-600 hover:bg-purple-700" 
                        title="Añadir al carrito">
                  <i class="fas fa-cart-plus"></i>
                </button>
              <?php else: ?>
                <button disabled class="flex items-center gap-1 px-2 py-1.5 rounded-lg font-semibold text-white text-xs bg-gray-400 cursor-not-allowed" title="Sin stock">
                  <i class="fas fa-times"></i>
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div> 