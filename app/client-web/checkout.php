<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/productModel.php';
require_once __DIR__ . '/../dashboard-web/model/carrtempModel.php';

use App\Model\ProductModel;
use App\Model\CarrTempModel;

if (!isset($_SESSION['cart_token'])) {
    $_SESSION['cart_token'] = bin2hex(random_bytes(16));
}
$cart_token = $_SESSION['cart_token'];
$productModel = new ProductModel($pdo);
$cartModel = new CarrTempModel($pdo);
$cart_items = $cartModel->getCartItems($cart_token);
$total = $cartModel->getCartTotal($cart_token);
$subtotal = $total;
$descuento = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | ModaSalud</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans">
<?php include 'includes/header.php'; ?>
<main class="max-w-2xl mx-auto px-4 py-10">
  <div class="bg-white rounded-xl shadow-lg p-6 md:p-10 flex flex-col gap-8">
    <h2 class="text-3xl font-bold text-purple-700 mb-2 text-center">Finaliza tu compra</h2>
    <div id="checkout-steps" class="flex flex-col gap-8">
      <!-- Paso 1: Correo electrónico -->
      <section id="step-1" class="step">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">1. Correo electrónico</h3>
        <label class="block mb-2 text-gray-700 font-medium" for="email">Correo electrónico <span class="text-rose-500">*</span></label>
        <input type="email" id="email" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="ejemplo@correo.com" required>
        <p id="email-error" class="text-rose-500 text-sm mt-1 hidden">Ingresa un correo válido.</p>
        <button type="button" onclick="nextStep(1)" class="mt-6 w-full bg-gradient-to-r from-purple-600 via-pink-500 to-rose-500 hover:from-purple-700 hover:to-rose-600 text-white text-lg font-bold py-3 rounded-xl shadow-lg transition">Continuar</button>
      </section>
      <!-- Paso 2: Datos del usuario -->
      <section id="step-2" class="step hidden">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">2. Datos del usuario</h3>
        <label class="block mb-2 text-gray-700 font-medium" for="fullname">Nombre completo <span class="text-rose-500">*</span></label>
        <input type="text" id="fullname" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="Nombre y apellidos" required>
        <p id="fullname-error" class="text-rose-500 text-sm mt-1 hidden">Este campo es obligatorio.</p>
        <label class="block mt-4 mb-2 text-gray-700 font-medium" for="dni">DNI (opcional)</label>
        <input type="text" id="dni" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="DNI">
        <label class="block mt-4 mb-2 text-gray-700 font-medium" for="phone">Teléfono <span class="text-rose-500">*</span></label>
        <input type="tel" id="phone" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="Número de celular" required>
        <p id="phone-error" class="text-rose-500 text-sm mt-1 hidden">Este campo es obligatorio.</p>
        <button type="button" onclick="nextStep(2)" class="mt-6 w-full bg-gradient-to-r from-purple-600 via-pink-500 to-rose-500 hover:from-purple-700 hover:to-rose-600 text-white text-lg font-bold py-3 rounded-xl shadow-lg transition">Siguiente</button>
      </section>
      <!-- Paso 3: Datos de envío -->
      <section id="step-3" class="step hidden">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">3. Datos de envío</h3>
        <div class="flex items-center mb-4">
          <input type="checkbox" id="pickup" class="accent-purple-600 mr-2" onchange="togglePickup()">
          <label for="pickup" class="text-gray-700 font-medium">Deseo recoger en tienda</label>
        </div>
        <div id="shipping-fields">
          <label class="block mb-2 text-gray-700 font-medium" for="address">Dirección <span class="text-rose-500">*</span></label>
          <input type="text" id="address" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="Calle y número">
          <p id="address-error" class="text-rose-500 text-sm mt-1 hidden">Este campo es obligatorio.</p>
          <label class="block mt-4 mb-2 text-gray-700 font-medium" for="city">Ciudad <span class="text-rose-500">*</span></label>
          <input type="text" id="city" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="Ciudad">
          <p id="city-error" class="text-rose-500 text-sm mt-1 hidden">Este campo es obligatorio.</p>
          <label class="block mt-4 mb-2 text-gray-700 font-medium" for="department">Departamento <span class="text-rose-500">*</span></label>
          <select id="department" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none">
            <option value="">Selecciona un departamento</option>
            <option value="Lima">Lima</option>
            <option value="Callao">Callao</option>
            <option value="Arequipa">Arequipa</option>
            <option value="Cusco">Cusco</option>
            <option value="La Libertad">La Libertad</option>
            <option value="Piura">Piura</option>
            <option value="Lambayeque">Lambayeque</option>
            <option value="Junín">Junín</option>
            <option value="Otro">Otro</option>
          </select>
          <p id="department-error" class="text-rose-500 text-sm mt-1 hidden">Selecciona un departamento.</p>
          <label class="block mt-4 mb-2 text-gray-700 font-medium" for="reference">Referencia de entrega (opcional)</label>
          <input type="text" id="reference" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="Referencia">
        </div>
        <button type="button" onclick="nextStep(3)" class="mt-6 w-full bg-gradient-to-r from-purple-600 via-pink-500 to-rose-500 hover:from-purple-700 hover:to-rose-600 text-white text-lg font-bold py-3 rounded-xl shadow-lg transition">Siguiente</button>
      </section>
      <!-- Paso 4: Método de pago y resumen -->
      <section id="step-4" class="step hidden">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">4. Método de pago</h3>
        <div class="flex flex-col gap-4 mb-6">
          <div class="flex gap-3">
            <button type="button" class="pay-method bg-purple-100 text-purple-700 px-3 py-2 rounded-lg font-semibold flex-1 border-2 border-transparent" data-method="yape" onclick="selectPayMethod('yape')">Yape</button>
            <button type="button" class="pay-method bg-pink-100 text-pink-600 px-3 py-2 rounded-lg font-semibold flex-1 border-2 border-transparent" data-method="plin" onclick="selectPayMethod('plin')">Plin</button>
            <button type="button" class="pay-method bg-gray-100 text-gray-600 px-3 py-2 rounded-lg font-semibold flex-1 border-2 border-transparent" data-method="card" onclick="selectPayMethod('card')">Tarjeta</button>
          </div>
          <div id="pay-yape" class="pay-details hidden mt-4">
            <img src="https://placehold.co/200x200?text=QR+Yape" alt="QR Yape" class="mx-auto rounded-lg border mb-2">
            <div class="text-center text-gray-700">Número: <span class="font-bold">999 888 777</span></div>
            <div class="text-center text-xs text-gray-500 mt-1">Escanea el código y confirma tu pago manualmente.</div>
          </div>
          <div id="pay-plin" class="pay-details hidden mt-4">
            <img src="https://placehold.co/200x200?text=QR+Plin" alt="QR Plin" class="mx-auto rounded-lg border mb-2">
            <div class="text-center text-gray-700">Número: <span class="font-bold">999 888 666</span></div>
            <div class="text-center text-xs text-gray-500 mt-1">Escanea el código y confirma tu pago manualmente.</div>
          </div>
          <div id="pay-card" class="pay-details hidden mt-4">
            <label class="block mb-2 text-gray-700 font-medium" for="card-number">Número de tarjeta <span class="text-rose-500">*</span></label>
            <input type="text" id="card-number" maxlength="19" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="0000 0000 0000 0000">
            <p id="card-number-error" class="text-rose-500 text-sm mt-1 hidden">Ingresa un número de tarjeta válido.</p>
            <label class="block mt-4 mb-2 text-gray-700 font-medium" for="card-name">Nombre del titular <span class="text-rose-500">*</span></label>
            <input type="text" id="card-name" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="Como aparece en la tarjeta">
            <p id="card-name-error" class="text-rose-500 text-sm mt-1 hidden">Este campo es obligatorio.</p>
            <div class="flex gap-4 mt-4">
              <div class="flex-1">
                <label class="block mb-2 text-gray-700 font-medium" for="card-expiry">Vencimiento (MM/AA) <span class="text-rose-500">*</span></label>
                <input type="text" id="card-expiry" maxlength="5" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="MM/AA">
                <p id="card-expiry-error" class="text-rose-500 text-sm mt-1 hidden">Formato inválido.</p>
              </div>
              <div class="flex-1">
                <label class="block mb-2 text-gray-700 font-medium" for="card-cvv">CVV <span class="text-rose-500">*</span></label>
                <input type="text" id="card-cvv" maxlength="4" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-400 outline-none" placeholder="CVV">
                <p id="card-cvv-error" class="text-rose-500 text-sm mt-1 hidden">CVV inválido.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Resumen del pedido -->
        <div class="bg-gray-50 rounded-xl shadow p-4 mb-6">
          <h4 class="text-lg font-bold text-purple-700 mb-2">Resumen del pedido</h4>
          <?php if (empty($cart_items)): ?>
            <div class="text-gray-500 text-center py-6">No hay productos en el carrito.</div>
          <?php else: ?>
            <div class="flex flex-col gap-3">
              <?php foreach ($cart_items as $item): ?>
                <div class="flex items-center justify-between border-b pb-2">
                  <div class="flex items-center gap-3">
                    <img src="/<?= htmlspecialchars($item['main_image'] ?? 'uploads/products/default.png') ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-12 h-12 object-contain rounded border">
                    <div>
                      <div class="font-semibold text-gray-800 text-sm line-clamp-1"><?= htmlspecialchars($item['name']) ?></div>
                      <div class="text-xs text-gray-500">x<?= $item['quantity'] ?></div>
                    </div>
                  </div>
                  <div class="text-base font-bold text-purple-700">S/ <?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="flex justify-between mt-4 text-base">
              <span>Subtotal</span><span>S/ <?= number_format($subtotal, 2) ?></span>
            </div>
            <div class="flex justify-between text-green-600 text-base">
              <span>Descuentos</span><span>- S/ <?= number_format($descuento, 2) ?></span>
            </div>
            <div class="flex justify-between font-bold text-lg text-pink-600 border-t pt-2">
              <span>Total</span><span>S/ <?= number_format($total, 2) ?></span>
            </div>
          <?php endif; ?>
        </div>
        <button type="button" onclick="submitOrder()" class="w-full bg-gradient-to-r from-purple-600 via-pink-500 to-rose-500 hover:from-purple-700 hover:to-rose-600 text-white text-lg font-bold py-3 rounded-xl shadow-lg transition">Generar pedido y realizar pago</button>
      </section>
      <!-- Paso 5: Confirmación -->
      <section id="step-5" class="step hidden">
        <div class="flex flex-col items-center justify-center py-12">
          <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
          <h4 class="text-2xl font-bold text-green-600 mb-2">¡Gracias por tu compra!</h4>
          <p class="text-gray-700 text-center mb-2">Tu pedido ha sido generado correctamente.</p>
          <a href="index.php" class="mt-4 px-6 py-2 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">Volver a la tienda</a>
        </div>
      </section>
    </div>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
<script>
// Manejo de pasos
let currentStep = 1;
function showStep(step) {
  document.querySelectorAll('.step').forEach((el, idx) => {
    el.classList.toggle('hidden', idx !== step - 1);
  });
  currentStep = step;
}
function nextStep(step) {
  if (step === 1) {
    const email = document.getElementById('email').value.trim();
    const emailError = document.getElementById('email-error');
    const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    if (!valid) {
      emailError.classList.remove('hidden');
      return;
    } else {
      emailError.classList.add('hidden');
    }
    showStep(2);
  } else if (step === 2) {
    let valid = true;
    const fullname = document.getElementById('fullname').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const fullnameError = document.getElementById('fullname-error');
    const phoneError = document.getElementById('phone-error');
    if (!fullname) {
      fullnameError.classList.remove('hidden');
      valid = false;
    } else {
      fullnameError.classList.add('hidden');
    }
    if (!phone) {
      phoneError.classList.remove('hidden');
      valid = false;
    } else {
      phoneError.classList.add('hidden');
    }
    if (!valid) return;
    showStep(3);
  } else if (step === 3) {
    if (!document.getElementById('pickup').checked) {
      let valid = true;
      const address = document.getElementById('address').value.trim();
      const city = document.getElementById('city').value.trim();
      const department = document.getElementById('department').value;
      const addressError = document.getElementById('address-error');
      const cityError = document.getElementById('city-error');
      const departmentError = document.getElementById('department-error');
      if (!address) {
        addressError.classList.remove('hidden');
        valid = false;
      } else {
        addressError.classList.add('hidden');
      }
      if (!city) {
        cityError.classList.remove('hidden');
        valid = false;
      } else {
        cityError.classList.add('hidden');
      }
      if (!department) {
        departmentError.classList.remove('hidden');
        valid = false;
      } else {
        departmentError.classList.add('hidden');
      }
      if (!valid) return;
    }
    showStep(4);
  }
}
function togglePickup() {
  const checked = document.getElementById('pickup').checked;
  document.getElementById('shipping-fields').classList.toggle('hidden', checked);
}
function selectPayMethod(method) {
  document.querySelectorAll('.pay-method').forEach(btn => {
    btn.classList.remove('border-purple-600', 'ring-2', 'ring-purple-200');
  });
  document.querySelectorAll('.pay-details').forEach(div => div.classList.add('hidden'));
  if (method === 'yape') {
    document.getElementById('pay-yape').classList.remove('hidden');
    document.querySelector('[data-method="yape"]').classList.add('border-purple-600', 'ring-2', 'ring-purple-200');
  } else if (method === 'plin') {
    document.getElementById('pay-plin').classList.remove('hidden');
    document.querySelector('[data-method="plin"]').classList.add('border-purple-600', 'ring-2', 'ring-purple-200');
  } else if (method === 'card') {
    document.getElementById('pay-card').classList.remove('hidden');
    document.querySelector('[data-method="card"]').classList.add('border-purple-600', 'ring-2', 'ring-purple-200');
  }
  window.selectedPayMethod = method;
}
function submitOrder() {
  // Validar método de pago
  const method = window.selectedPayMethod;
  if (!method) {
    showNotification('Selecciona un método de pago', 'error');
    return;
  }
  let valid = true;
  if (method === 'card') {
    const cardNumber = document.getElementById('card-number').value.replace(/\s+/g, '');
    const cardName = document.getElementById('card-name').value.trim();
    const cardExpiry = document.getElementById('card-expiry').value.trim();
    const cardCVV = document.getElementById('card-cvv').value.trim();
    const cardNumberError = document.getElementById('card-number-error');
    const cardNameError = document.getElementById('card-name-error');
    const cardExpiryError = document.getElementById('card-expiry-error');
    const cardCVVError = document.getElementById('card-cvv-error');
    if (!/^\d{16}$/.test(cardNumber)) {
      cardNumberError.classList.remove('hidden');
      valid = false;
    } else {
      cardNumberError.classList.add('hidden');
    }
    if (!cardName) {
      cardNameError.classList.remove('hidden');
      valid = false;
    } else {
      cardNameError.classList.add('hidden');
    }
    if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) {
      cardExpiryError.classList.remove('hidden');
      valid = false;
    } else {
      cardExpiryError.classList.add('hidden');
    }
    if (!/^\d{3,4}$/.test(cardCVV)) {
      cardCVVError.classList.remove('hidden');
      valid = false;
    } else {
      cardCVVError.classList.add('hidden');
    }
    if (!valid) return;
  }
  // Simular validación de stock
  <?php if (!empty($cart_items)): ?>
    let stockOk = true;
    <?php foreach ($cart_items as $item): ?>
      if (<?= $item['quantity'] ?> > <?= $item['stock'] ?>) {
        stockOk = false;
      }
    <?php endforeach; ?>
    if (!stockOk) {
      showNotification('Uno o más productos no tienen stock suficiente.', 'error');
      return;
    }
  <?php endif; ?>
  // Mostrar confirmación
  showStep(5);
}
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
document.addEventListener('DOMContentLoaded', function() {
  showStep(1);
});
</script>
</body>
</html> 