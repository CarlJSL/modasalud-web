<?php
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/productModel.php';

use App\Model\ProductModel;

$productModel = new ProductModel($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $id ? $productModel->getDetailedById($id) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $product ? htmlspecialchars($product['name']) . ' | ModaSalud' : 'Producto no encontrado' ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans">
  <?php include 'includes/header.php'; ?>
  <main class="max-w-5xl mx-auto px-4 py-10">
    <?php if (!$product): ?>
      <div class="bg-white rounded-xl shadow p-10 text-center">
        <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
        <h2 class="text-2xl font-bold text-rose-600 mb-2">Producto no encontrado</h2>
        <p class="text-gray-500 mb-4">El producto que buscas no existe o fue eliminado.</p>
        <a href="index.php" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold">Volver a la tienda</a>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-xl shadow p-8 flex flex-col md:flex-row gap-10">
        <div class="flex-1 flex flex-col items-center justify-center">
          <?php $images = $product['images'] ?? []; ?>
          <?php if (count($images) > 1): ?>
            <div x-data="{active: 0}" class="w-full flex flex-col items-center">
              <div class="relative w-full max-w-md h-96 mb-4">
                <?php foreach ($images as $i => $img): ?>
                  <img x-show="active === <?= $i ?>" src="/<?= htmlspecialchars($img) ?>" alt="" class="object-contain w-full h-96 rounded-xl bg-gray-50 absolute top-0 left-0 transition-all duration-300" style="display: <?= $i === 0 ? 'block' : 'none' ?>;">
                <?php endforeach; ?>
                <button type="button" x-on:click="active = (active - 1 + <?= count($images) ?>) % <?= count($images) ?>" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-purple-100 rounded-full p-2 shadow"><i class="fas fa-chevron-left text-purple-600"></i></button>
                <button type="button" x-on:click="active = (active + 1) % <?= count($images) ?>" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-purple-100 rounded-full p-2 shadow"><i class="fas fa-chevron-right text-purple-600"></i></button>
              </div>
              <div class="flex gap-2 mt-2">
                <?php foreach ($images as $i => $img): ?>
                  <button type="button" x-on:click="active = <?= $i ?>" :class="active === <?= $i ?> ? 'ring-2 ring-purple-500' : ''" class="w-20 h-20 rounded border border-gray-200 bg-white overflow-hidden focus:outline-none">
                    <img src="/<?= htmlspecialchars($img) ?>" alt="" class="object-contain w-full h-full">
                  </button>
                <?php endforeach; ?>
              </div>
            </div>
          <?php elseif (!empty($product['main_image'])): ?>
            <img src="/<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="object-contain w-full max-w-md h-96 rounded-xl mb-4 bg-gray-50">
          <?php else: ?>
            <div class="w-full h-96 flex items-center justify-center rounded-xl bg-gray-100 mb-4"><i class='fas fa-image text-6xl text-gray-300'></i></div>
          <?php endif; ?>
        </div>
        <div class="flex-1 flex flex-col gap-4">
          <h1 class="text-3xl font-bold text-purple-700 mb-2"><?= htmlspecialchars($product['name']) ?></h1>
          <div class="flex flex-wrap gap-3 items-center mb-2">
            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold"><?= htmlspecialchars($product['category_name'] ?? 'Sin categoría') ?></span>
            <?php if (!empty($product['product_category_name'])): ?>
              <span class="bg-pink-100 text-pink-600 px-3 py-1 rounded-full text-xs font-semibold"><?= htmlspecialchars($product['product_category_name']) ?></span>
            <?php endif; ?>
            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">Talla: <?= htmlspecialchars($product['size']) ?></span>
            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">Stock: <?= (int)$product['stock'] ?></span>
          </div>
          <div class="text-2xl font-extrabold text-purple-700 mb-2">S/ <?= number_format($product['price'], 2) ?></div>
          <p class="text-gray-700 leading-relaxed mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
          <div class="flex gap-3 mt-4">
            <?php if ((int)$product['stock'] > 0): ?>
              <button onclick="addToCart(<?= $product['id'] ?>)" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2"><i class="fas fa-cart-plus"></i> Añadir al carrito</button>
            <?php else: ?>
              <button disabled class="bg-gray-400 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 cursor-not-allowed"><i class="fas fa-times"></i> Sin stock</button>
            <?php endif; ?>
            <button class="bg-pink-100 hover:bg-pink-200 text-pink-600 px-6 py-2 rounded-lg font-semibold flex items-center gap-2"><i class="fa-regular fa-heart"></i> Favorito</button>
          </div>
          <!-- Información de seguridad -->
          <div class="mt-6 bg-gray-50 rounded p-2 flex flex-col gap-1 border border-gray-100 text-xs text-gray-500">
            <div>Compra protegida: recibe el producto que esperabas o te devolvemos tu dinero.</div>
            <div>Pago seguro: tus datos están cifrados y protegidos.</div>
            <div>Devolución fácil: tienes hasta 7 días para devolver tu compra.</div>
            <div>Envíos rápidos y seguimiento en todo momento.</div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </main>
  <?php include 'includes/footer.php'; ?>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  
  <!-- Scripts para el carrito -->
  <script>
    // Función para agregar producto al carrito
    async function addToCart(productId, quantity = 1) {
      const loadingBtn = document.querySelector('.bg-purple-600');
      const originalText = loadingBtn.innerHTML;
      
      loadingBtn.disabled = true;
      loadingBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agregando...';
      
      try {
          const response = await fetch('cart-ajax.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `action=add&product_id=${productId}&quantity=${quantity}`
          });
          
          const data = await response.json();
          
          if (data.success) {
              showNotification(data.message, 'success');
              loadingBtn.innerHTML = '<i class="fas fa-check"></i>';
              loadingBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
              loadingBtn.classList.add('bg-green-500', 'hover:bg-green-600');
              updateCartCounter();
              setTimeout(() => {
                  loadingBtn.innerHTML = originalText;
                  loadingBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                  loadingBtn.classList.add('bg-purple-600', 'hover:bg-purple-700');
                  loadingBtn.disabled = false;
              }, 2000);
          } else {
              showNotification(data.message, 'error');
              loadingBtn.innerHTML = originalText;
              loadingBtn.disabled = false;
          }
      } catch (error) {
          console.error('Error:', error);
          showNotification('Error al conectar con el servidor', 'error');
          loadingBtn.innerHTML = originalText;
          loadingBtn.disabled = false;
      }
    }
    
    // Función para actualizar el contador del carrito
    async function updateCartCounter() {
        try {
            const response = await fetch('cart-count.php');
            const data = await response.json();
            const counterElement = document.getElementById('cart-count');
            if (counterElement) {
                counterElement.innerText = data.count;
                if (data.count > 0) {
                    counterElement.style.display = 'flex';
                } else {
                    counterElement.style.display = 'none';
                }
            }
        } catch (error) {
            console.error('Error actualizando contador:', error);
        }
    }

    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        } flex items-center gap-2`;
        
        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        
        notification.appendChild(icon);
        notification.appendChild(document.createTextNode(message));
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500);
        }, 3000);
    }
    
    // Inicializar contador al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        updateCartCounter();
    });
  </script>
</body>
</html> 