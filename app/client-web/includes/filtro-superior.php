<?php
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../../dashboard-web/model/categoriesModel.php';

use App\Model\CategoriesModel;

$categoriesModel = new CategoriesModel($pdo);
$categories = $categoriesModel->getAllCategories(30, 0);
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Construir la query string manteniendo los filtros actuales excepto 'category'
function buildCategoryUrl($category) {
    $params = $_GET;
    $params['category'] = $category;
    return '?' . http_build_query($params);
}
function buildClearCategoryUrl() {
    $params = $_GET;
    unset($params['category']);
    return '?' . http_build_query($params);
}
?>

<!-- Filtro superior: Categorías -->
<section class="w-full bg-gradient-to-r from-purple-50 via-pink-50 to-rose-50 rounded-xl p-4 mb-6">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-lg font-bold text-purple-700 mb-2">Categorías</h2>
    <div class="w-full flex flex-wrap gap-3 items-center justify-center py-4 mb-6">
      <?php foreach ($categories as $cat): ?>
        <a href="<?= buildCategoryUrl($cat['name']) ?>"
          class="flex items-center justify-center rounded-full px-5 py-2 text-sm font-semibold shadow transition
            <?= $selectedCategory === $cat['name'] ? 'bg-purple-600 text-white ring-2 ring-purple-400' : 'bg-white text-purple-700 hover:bg-purple-100' ?>
            border border-purple-200">
          <?= htmlspecialchars($cat['name']) ?>
        </a>
      <?php endforeach; ?>
      <?php if ($selectedCategory): ?>
        <a href="<?= buildClearCategoryUrl() ?>" class="ml-2 text-xs text-gray-500 underline">Quitar filtro</a>
      <?php endif; ?>
    </div>
  </div>
</section> 