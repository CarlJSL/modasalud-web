<?php
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../../dashboard-web/model/productModel.php';

use App\Model\ProductModel;

$productModel = new ProductModel($pdo);
$sizes = array_map(function($s) { return $s['name']; }, $productModel->getSizes());
$subcategories = $productModel->getSubCategories();

// Obtener valores actuales de filtros
$selectedSizes = isset($_GET['size']) ? (array)$_GET['size'] : [];
$selectedSubcategories = isset($_GET['subcategory']) ? (array)$_GET['subcategory'] : [];
$priceMin = isset($_GET['price_min']) ? (int)$_GET['price_min'] : 0;
$priceMax = isset($_GET['price_max']) ? (int)$_GET['price_max'] : 1000;
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
?>
<aside class="bg-white rounded-xl p-6 h-fit shadow w-full md:w-64 mb-6 md:mb-0">
  <form method="get" class="flex flex-col gap-8">
    <?php if ($selectedCategory): ?>
      <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
    <?php endif; ?>
    <div class="flex flex-col gap-3 mt-4">
      <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded-lg flex items-center justify-center gap-2 transition"><i class="fas fa-filter"></i>Aplicar filtros</button>
      <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 rounded-lg flex items-center justify-center gap-2 transition"><i class="fas fa-undo"></i>Reiniciar</a>
    </div>
    <div>
      <h3 class="flex items-center justify-between text-base font-semibold text-gray-800 mb-4 cursor-pointer">
        <span><i class="fas fa-layer-group mr-2 text-purple-500"></i>Subcategorías</span>
        <i class="fas fa-chevron-down text-gray-400"></i>
      </h3>
      <div class="flex flex-col gap-3 max-h-40 overflow-y-auto pr-1">
        <?php foreach ($subcategories as $subcat): ?>
          <label class="flex items-center gap-2">
            <input type="checkbox" name="subcategory[]" value="<?= htmlspecialchars($subcat['name']) ?>" class="accent-purple-500" <?= in_array($subcat['name'], $selectedSubcategories) ? 'checked' : '' ?>>
            <?= htmlspecialchars($subcat['name']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div>
      <h3 class="flex items-center justify-between text-base font-semibold text-gray-800 mb-4 cursor-pointer">
        <span><i class="fas fa-ruler mr-2 text-rose-400"></i>Tallas</span>
        <i class="fas fa-chevron-down text-gray-400"></i>
      </h3>
      <div class="flex flex-col gap-3">
        <?php foreach ($sizes as $size): ?>
          <label class="flex items-center gap-2">
            <input type="checkbox" name="size[]" value="<?= htmlspecialchars($size) ?>" class="accent-purple-500" <?= in_array($size, $selectedSizes) ? 'checked' : '' ?>>
            <?= htmlspecialchars($size) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div>
      <h3 class="flex items-center justify-between text-base font-semibold text-gray-800 mb-4 cursor-pointer">
        <span><i class="fas fa-dollar-sign mr-2 text-purple-400"></i>Rango de precios</span>
        <i class="fas fa-chevron-down text-gray-400"></i>
      </h3>
      <div class="flex flex-col gap-2">
        <div class="flex items-center gap-2">
          <input type="number" name="price_min" min="0" max="<?= $priceMax ?>" value="<?= $priceMin ?>" class="w-20 px-2 py-1 border rounded text-sm" placeholder="Mín">
          <span class="text-gray-400">-</span>
          <input type="number" name="price_max" min="<?= $priceMin ?>" max="10000" value="<?= $priceMax ?>" class="w-20 px-2 py-1 border rounded text-sm" placeholder="Máx">
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-1">S/ <?= $priceMin ?> - S/ <?= $priceMax ?></div>
      </div>
    </div>

  </form>
</aside> 