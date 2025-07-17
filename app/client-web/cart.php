<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/productModel.php';
require_once __DIR__ . '/../dashboard-web/model/carrtempModel.php';

use App\Model\ProductModel;
use App\Model\CarrTempModel;

// Obtener o crear token del carrito
if (!isset($_SESSION['cart_token'])) {
    $_SESSION['cart_token'] = bin2hex(random_bytes(16));
}

$cart_token = $_SESSION['cart_token'];
$productModel = new ProductModel($pdo);
$cartModel = new CarrTempModel($pdo);

// Obtener productos del carrito
$cart_items = $cartModel->getCartItems($cart_token);
$total = $cartModel->getCartTotal($cart_token);
$subtotal = $total;
$descuento = 0;

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    ob_start();
    ?>
    <div id="cart-items">
        <?php foreach ($cart_items as $item): ?>
        <div class="cart-item flex flex-col md:flex-row items-center gap-4 bg-gray-50 rounded-xl shadow-sm p-4 relative mb-4" data-product-id="<?= $item['product_id'] ?>">
          <input type="checkbox" class="accent-purple-600 absolute left-2 top-2 item-checkbox" 
                 <?= $item['selected'] ? 'checked' : '' ?> 
                 onchange="toggleSelection(<?= $item['product_id'] ?>, this.checked)">
          <img src="/<?= htmlspecialchars($item['main_image'] ?? 'uploads/products/default.png') ?>" 
               alt="<?= htmlspecialchars($item['name']) ?>" 
               class="w-24 h-24 object-contain rounded-lg border border-gray-200 bg-white">
          <div class="flex-1 flex flex-col gap-1">
            <span class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($item['name']) ?></span>
            <span class="text-base font-bold text-purple-700">S/ <span class="item-price"><?= number_format($item['price'], 2) ?></span></span>
            <div class="text-sm text-gray-500">
              Stock disponible: <span class="stock-display"><?= $item['stock'] ?></span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button onclick="updateQuantity(<?= $item['product_id'] ?>, -1)" class="bg-gray-200 hover:bg-gray-300 rounded-full w-8 h-8 flex items-center justify-center">
              <i class="fas fa-minus text-sm"></i>
            </button>
            <input type="number" 
                   value="<?= $item['quantity'] ?>" 
                   min="1" 
                   max="<?= $item['stock'] ?>"
                   class="w-16 text-center border rounded focus:ring-pink-400 item-qty" 
                   onchange="updateQuantity(<?= $item['product_id'] ?>, 0, this.value)">
            <button onclick="updateQuantity(<?= $item['product_id'] ?>, 1)" class="bg-gray-200 hover:bg-gray-300 rounded-full w-8 h-8 flex items-center justify-center">
              <i class="fas fa-plus text-sm"></i>
            </button>
            <button onclick="removeItem(<?= $item['product_id'] ?>)" class="text-rose-500 hover:text-rose-700 text-xl ml-4">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
        <?php endforeach; ?>
    </div>
    <span id="subtotal">S/ <?= number_format($subtotal, 2) ?></span>
    <span id="total">S/ <?= number_format($total, 2) ?></span>
    <?php
    echo ob_get_clean();
    exit;
}
?><!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carrito de compras | ModaSalud</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans">
<?php include 'includes/header.php'; ?>
<main class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
  <!-- Carrito -->
  <section class="lg:col-span-2 bg-white rounded-xl shadow p-6 flex flex-col">
    <h2 class="text-3xl font-bold text-purple-700 mb-6">Carro (<span id="cart-count"><?= array_sum(array_column($cart_items, 'quantity')) ?></span> productos)</h2>
    <?php if (empty($cart_items)): ?>
      <div class="flex flex-col items-center justify-center py-16">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <p class="text-lg text-gray-500 mb-2">Tu carrito está vacío.</p>
        <a href="index.php" class="mt-2 px-6 py-2 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">Ver productos</a>
      </div>
    <?php else: ?>
      <div id="cart-items">
        <?php foreach ($cart_items as $item): ?>
        <div class="cart-item flex flex-col md:flex-row items-center gap-4 bg-gray-50 rounded-xl shadow-sm p-4 relative mb-4" data-product-id="<?= $item['product_id'] ?>">
          <input type="checkbox" class="accent-purple-600 absolute left-2 top-2 item-checkbox" 
                 <?= $item['selected'] ? 'checked' : '' ?> 
                 onchange="toggleSelection(<?= $item['product_id'] ?>, this.checked)">
          <img src="/<?= htmlspecialchars($item['main_image'] ?? 'uploads/products/default.png') ?>" 
               alt="<?= htmlspecialchars($item['name']) ?>" 
               class="w-24 h-24 object-contain rounded-lg border border-gray-200 bg-white">
          <div class="flex-1 flex flex-col gap-1">
            <span class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($item['name']) ?></span>
            <span class="text-base font-bold text-purple-700">S/ <span class="item-price"><?= number_format($item['price'], 2) ?></span></span>
            <div class="text-sm text-gray-500">
              Stock disponible: <span class="stock-display"><?= $item['stock'] ?></span>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button onclick="updateQuantity(<?= $item['product_id'] ?>, -1)" class="bg-gray-200 hover:bg-gray-300 rounded-full w-8 h-8 flex items-center justify-center">
              <i class="fas fa-minus text-sm"></i>
            </button>
            <input type="number" 
                   value="<?= $item['quantity'] ?>" 
                   min="1" 
                   max="<?= $item['stock'] ?>"
                   class="w-16 text-center border rounded focus:ring-pink-400 item-qty" 
                   onchange="updateQuantity(<?= $item['product_id'] ?>, 0, this.value)">
            <button onclick="updateQuantity(<?= $item['product_id'] ?>, 1)" class="bg-gray-200 hover:bg-gray-300 rounded-full w-8 h-8 flex items-center justify-center">
              <i class="fas fa-plus text-sm"></i>
            </button>
            <button onclick="removeItem(<?= $item['product_id'] ?>)" class="text-rose-500 hover:text-rose-700 text-xl ml-4">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="flex justify-between items-center mt-6">
        <button onclick="clearCart()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold">
          Vaciar carrito
        </button>
        <button onclick="location.reload()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold">
          Actualizar
        </button>
      </div>
    <?php endif; ?>
  </section>
  
  <!-- Resumen de orden -->
  <aside class="bg-white rounded-xl shadow p-6 flex flex-col gap-6 h-fit">
    <h3 class="text-xl font-bold text-purple-700 mb-2">Resumen de la orden</h3>
    <div class="flex flex-col gap-2 text-base">
      <div class="flex justify-between"><span>Subtotal</span><span id="subtotal">S/ <?= number_format($subtotal, 2) ?></span></div>
      <div class="flex justify-between text-green-600"><span>Descuentos</span><span>- S/ <?= number_format($descuento, 2) ?></span></div>
      <div class="flex justify-between font-bold text-lg text-pink-600 border-t pt-2"><span>Total a pagar</span><span id="total">S/ <?= number_format($total, 2) ?></span></div>
    </div>
    <div class="flex flex-col gap-2 mt-4">
      <span class="text-xs text-gray-400">Paga con:</span>
      <div class="flex gap-3">
        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold">Yape</span>
        <span class="bg-pink-100 text-pink-600 px-3 py-1 rounded-full text-xs font-semibold">Plin</span>
        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">Tarjeta</span>
      </div>
    </div>
    <button class="mt-6 bg-gradient-to-r from-purple-600 via-pink-500 to-rose-500 hover:from-purple-700 hover:to-rose-600 text-white text-lg font-bold py-3 rounded-xl shadow-lg transition">
      Continuar compra
    </button>
  </aside>
</main>

<!-- Sugerencias -->
<section class="max-w-7xl mx-auto px-4 mt-12">
  <h3 class="text-xl font-bold text-purple-700 mb-4">¿Y si le sumas lo último?</h3>
  <div class="flex gap-6 overflow-x-auto pb-4">
    <?php
    $sugeridos = $productModel->getAll(10, 0, '', ['status' => 'ACTIVE']);
    foreach ($sugeridos as $s): ?>
      <div class="min-w-[220px] bg-white rounded-2xl shadow-lg p-4 flex flex-col items-center relative">
        <?php if (!empty($s['main_image'])): ?>
          <img src="/<?= htmlspecialchars($s['main_image']) ?>" alt="<?= htmlspecialchars($s['name']) ?>" class="object-contain h-32 w-full rounded-xl mb-2">
        <?php else: ?>
          <div class="w-full h-32 flex items-center justify-center rounded-xl bg-gray-100 mb-2"><i class='fas fa-image text-4xl text-gray-300'></i></div>
        <?php endif; ?>
        <span class="text-sm font-semibold text-gray-800 text-center mb-1 line-clamp-2"><?= htmlspecialchars($s['name']) ?></span>
        <span class="text-base font-bold text-purple-700 mb-2">S/ <?= number_format($s['price'], 2) ?></span>
        <button onclick="addToCart(<?= $s['id'] ?>)" data-product-id="<?= $s['id'] ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1">
          <i class="fas fa-cart-plus"></i>
        </button>
        <?php if (rand(0, 10) > 7): ?>
          <span class="absolute top-2 left-2 bg-pink-500 text-white text-xs font-bold px-2 py-0.5 rounded">¡Nuevo!</span>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Modal de confirmación flotante mejorado -->
<div id="confirm-modal-overlay" class="fixed inset-0 z-50 bg-opacity-90 hidden"></div>
<div id="confirm-modal" class="fixed left-1/2 top-1/2 z-50 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-2xl p-6 max-w-xs w-full text-center border border-gray-200 hidden">
  <div id="confirm-modal-message" class="mb-4 text-gray-800 text-lg font-semibold"></div>
  <div class="flex justify-center gap-4">
    <button id="confirm-modal-cancel" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold">Cancelar</button>
    <button id="confirm-modal-accept" class="px-4 py-2 rounded bg-red-500 hover:bg-red-600 text-white font-semibold">Sí, eliminar</button>
  </div>
</div>

<script>
// Funciones para manejar el carrito
async function updateQuantity(productId, change, newValue = null) {
    const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
    const qtyInput = cartItem.querySelector('.item-qty');
    const stockDisplay = cartItem.querySelector('.stock-display');
    const maxStock = parseInt(stockDisplay.textContent);
    
    let quantity;
    if (newValue !== null) {
        quantity = parseInt(newValue);
    } else {
        quantity = parseInt(qtyInput.value) + change;
    }
    
    if (quantity < 1) quantity = 1;
    if (quantity > maxStock) {
        showNotification('Stock insuficiente', 'error');
        return;
    }
    
    try {
        const response = await fetch('cart-ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update_quantity&product_id=${productId}&quantity=${quantity}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            qtyInput.value = quantity;
            updateTotals();
            showNotification('Cantidad actualizada', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
    }
}

async function toggleSelection(productId, selected) {
    try {
        const response = await fetch('cart-ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle_selection&product_id=${productId}&selected=${selected}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            updateTotals();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
    }
}


// Modal de confirmación reutilizable mejorado
function showConfirm(message, acceptText = 'Sí, eliminar') {
    return new Promise(resolve => {
        const overlay = document.getElementById('confirm-modal-overlay');
        const modal = document.getElementById('confirm-modal');
        const msg = document.getElementById('confirm-modal-message');
        const btnCancel = document.getElementById('confirm-modal-cancel');
        const btnAccept = document.getElementById('confirm-modal-accept');
        msg.textContent = message;
        btnAccept.textContent = acceptText;
        overlay.classList.remove('hidden');
        modal.classList.remove('hidden');
        function cleanup(result) {
            overlay.classList.add('hidden');
            modal.classList.add('hidden');
            btnCancel.removeEventListener('click', onCancel);
            btnAccept.removeEventListener('click', onAccept);
            overlay.removeEventListener('click', onOverlayClick);
            resolve(result);
        }
        function onCancel() { cleanup(false); }
        function onAccept() { cleanup(true); }
        function onOverlayClick(e) { if (e.target === overlay) cleanup(false); }
        btnCancel.addEventListener('click', onCancel);
        btnAccept.addEventListener('click', onAccept);
        overlay.addEventListener('click', onOverlayClick);
    });
}

async function removeItem(productId) {
    const confirmed = await showConfirm('¿Estás seguro de que quieres eliminar este producto del carrito?');
    if (!confirmed) return;
    try {
        const response = await fetch('cart-ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&product_id=${productId}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.querySelector(`[data-product-id="${productId}"]`).remove();
            updateTotals();
            updateCartCount();
            showNotification('Producto eliminado', 'success');
            
            // Recargar página si no hay más productos
            if (document.querySelectorAll('.cart-item').length === 0) {
                location.reload();
            }
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
    }
}

async function clearCart() {
    const confirmed = await showConfirm('¿Estás seguro de que quieres vaciar el carrito?', 'Sí, vaciar');
    if (!confirmed) return;
    try {
        const response = await fetch('cart-ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clear'
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
    }
}

function updateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('.cart-item').forEach(item => {
        const checkbox = item.querySelector('.item-checkbox');
        const price = parseFloat(item.querySelector('.item-price').textContent);
        const quantity = parseInt(item.querySelector('.item-qty').value);
        
        if (checkbox.checked) {
            subtotal += price * quantity;
        }
    });
    
    document.getElementById('subtotal').textContent = 'S/ ' + subtotal.toFixed(2);
    document.getElementById('total').textContent = 'S/ ' + subtotal.toFixed(2);
}

function updateCartCount() {
    const totalItems = Array.from(document.querySelectorAll('.item-qty'))
        .reduce((sum, input) => sum + parseInt(input.value), 0);
    document.getElementById('cart-count').textContent = totalItems;
}

// Función para agregar producto al carrito desde sugerencias
async function addToCart(productId, quantity = 1) {
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
            const btnAdd = document.querySelector(`[data-product-id="${productId}"]`);
            if (btnAdd) {
                btnAdd.innerHTML = '<i class="fas fa-check"></i>';
                btnAdd.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                btnAdd.classList.add('bg-green-500', 'hover:bg-green-600');
            }
            updateCartCount();
            // Recargar productos del carrito y totales sin recargar la página
            await refreshCartItems();
            // Restaurar el icono original después de refrescar el carrito
            setTimeout(() => {
                const btn = document.querySelector(`[data-product-id="${productId}"]`);
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-cart-plus"></i>';
                    btn.classList.remove('bg-green-500', 'hover:bg-green-600');
                    btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
                }
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Error de conexión', 'error');
        console.error('Error en addToCart:', error);
    }
}

// Refresca el bloque de productos del carrito y los totales
async function refreshCartItems() {
    try {
        const response = await fetch(window.location.pathname + '?ajax=1');
        const html = await response.text();
        // Extraer el bloque del carrito y los totales del HTML recibido
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const newCartItems = tempDiv.querySelector('#cart-items');
        const newSubtotal = tempDiv.querySelector('#subtotal');
        const newTotal = tempDiv.querySelector('#total');
        if (newCartItems && document.getElementById('cart-items')) {
            document.getElementById('cart-items').innerHTML = newCartItems.innerHTML;
        }
        if (newSubtotal && document.getElementById('subtotal')) {
            document.getElementById('subtotal').textContent = newSubtotal.textContent;
        }
        if (newTotal && document.getElementById('total')) {
            document.getElementById('total').textContent = newTotal.textContent;
        }
        updateTotals();
        updateCartCount();
    } catch (error) {
        console.error('Error actualizando carrito:', error);
    }
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Inicializar totales al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
});
</script>
</body>
</html>