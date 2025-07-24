<?php
session_start();
require_once __DIR__ . '/../conexion/db.php';
require_once __DIR__ . '/../dashboard-web/model/productModel.php';
require_once __DIR__ . '/../dashboard-web/model/carrtempModel.php';
require_once __DIR__ . '/../dashboard-web/model/orderModel.php';

use App\Model\ProductModel;
use App\Model\CarrTempModel;
use App\Model\OrderModel;

// Obtener o crear token del carrito
if (!isset($_SESSION['cart_token'])) {
  header('Location: cart.php');
  exit;
}

$cart_token = $_SESSION['cart_token'];
$productModel = new ProductModel($pdo);
$cartModel = new CarrTempModel($pdo);
$orderModel = new OrderModel($pdo);

// Obtener productos del carrito seleccionados
$cart_items = $cartModel->getSelectedItems($cart_token);
if (empty($cart_items)) {
  header('Location: cart.php');
  exit;
}

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
  <style>
    .drag-over {
      border-color: #7c3aed !important;
      background-color: #f3f4f6 !important;
    }
    
    .file-input-active {
      border-color: #7c3aed !important;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fadeIn {
      animation: fadeIn 0.3s ease-out;
    }
  </style>
</head>

<body class="bg-gray-50 font-sans">
  <?php include 'includes/header.php'; ?>

  <main class="max-w-6xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

      <!-- Formulario de checkout -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Finalizar compra</h2>

        <!-- Paso 1: Verificación de email -->
        <div id="step-email" class="checkout-step">
          <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</div>
            <h3 class="text-lg font-semibold text-gray-900">Información de contacto</h3>
          </div>

          <div class="space-y-4">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico</label>
              <input type="email" id="email" name="email" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                placeholder="ejemplo@correo.com">
              <p class="text-xs text-gray-500 mt-1">Ingresa tu email para verificar si ya tienes cuenta con nosotros</p>
            </div>

            <button type="button" onclick="verifyEmail()"
              class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-semibold transition">
              Verificar email
            </button>
          </div>
        </div>

        <!-- Paso 2: Información del cliente (cliente existente) -->
        <div id="step-existing-client" class="checkout-step hidden">
          <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
              <i class="fas fa-check"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Cliente encontrado</h3>
          </div>

          <div id="existing-client-info" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <!-- Información del cliente se cargará aquí -->
          </div>

          <button type="button" onclick="proceedToAddress()"
            class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-semibold transition">
            Continuar con esta información
          </button>

          <button type="button" onclick="showNewClientForm()"
            class="w-full mt-2 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg font-semibold transition">
            Usar información diferente
          </button>
        </div>

        <!-- Paso 2: Información del cliente (cliente nuevo) -->
        <div id="step-new-client" class="checkout-step hidden">
          <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">2</div>
            <h3 class="text-lg font-semibold text-gray-900">Información personal</h3>
          </div>

          <form id="client-form" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="client_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre completo *</label>
                <input type="text" id="client_name" name="client_name" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
              </div>

              <div>
                <label for="client_phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                <input type="tel" id="client_phone" name="client_phone" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="client_dni" class="block text-sm font-medium text-gray-700 mb-2">DNI *</label>
                <input type="text" id="client_dni" name="client_dni" required maxlength="8"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
              </div>

              <div>
                <label for="client_gender" class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                <select id="client_gender" name="client_gender"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                  <option value="">Seleccionar</option>
                  <option value="M">Masculino</option>
                  <option value="F">Femenino</option>
                  <option value="O">Otro</option>
                </select>
              </div>
            </div>

            <div>
              <label for="client_birth_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de nacimiento</label>
              <input type="date" id="client_birth_date" name="client_birth_date"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <button type="button" onclick="proceedToAddress()"
              class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-semibold transition">
              Continuar
            </button>
          </form>
        </div>

        <!-- Paso 3: Dirección de entrega -->
        <div id="step-address" class="checkout-step hidden">
          <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">3</div>
            <h3 class="text-lg font-semibold text-gray-900">Dirección de entrega</h3>
          </div>

          <!-- Direcciones existentes (si es cliente existente) -->
          <div id="existing-addresses" class="hidden mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar dirección guardada</label>
            <div id="addresses-list" class="space-y-2">
              <!-- Direcciones se cargarán aquí -->
            </div>

            <div class="flex gap-2 mt-3">
              <button type="button" onclick="showNewAddressForm()"
                class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg font-medium transition">
                <i class="fas fa-plus mr-2"></i>Agregar nueva dirección
              </button>

              <button type="button" onclick="proceedWithSelectedAddress()"
                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition">
                <i class="fas fa-check mr-2"></i>Usar dirección seleccionada
              </button>
            </div>
          </div>

          <form id="address-form" class="space-y-4">
            <div class="flex items-center justify-between mb-4" id="new-address-header">
              <h4 class="text-md font-semibold text-gray-800">Nueva dirección de entrega</h4>
              <button type="button" onclick="hideNewAddressForm()"
                class="text-gray-500 hover:text-gray-700 transition">
                <i class="fas fa-times"></i>
              </button>
            </div>

            <div>
              <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección completa *</label>
              <input type="text" id="address" name="address" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                placeholder="Av. Principal 123, Urbanización...">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Ciudad *</label>
                <input type="text" id="city" name="city" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
              </div>

              <div>
                <label for="region" class="block text-sm font-medium text-gray-700 mb-2">Región *</label>
                <input type="text" id="region" name="region" required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Código postal</label>
                <input type="text" id="postal_code" name="postal_code"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
              </div>

              <div>
                <label for="delivery_phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono de contacto</label>
                <input type="tel" id="delivery_phone" name="delivery_phone"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
              </div>
            </div>

            <button type="button" onclick="proceedToPayment()"
              class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-semibold transition">
              Continuar al pago
            </button>
          </form>
        </div>

        <!-- Paso 4: Método de pago -->
        <div id="step-payment" class="checkout-step hidden">
          <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">4</div>
            <h3 class="text-lg font-semibold text-gray-900">Método de pago</h3>
          </div>

          <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-3">
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="payment_method" value="YAPE" class="mr-3" checked onchange="togglePaymentFields()">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-purple-600 rounded flex items-center justify-center mr-3">
                    <i class="fas fa-mobile-alt text-white text-sm"></i>
                  </div>
                  <span class="font-medium">Yape</span>
                </div>
              </label>
            </div>

            <div class="border border-gray-200 rounded-lg p-3">
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="payment_method" value="PLIN" class="mr-3" onchange="togglePaymentFields()">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-pink-600 rounded flex items-center justify-center mr-3">
                    <i class="fas fa-mobile-alt text-white text-sm"></i>
                  </div>
                  <span class="font-medium">Plin</span>
                </div>
              </label>
            </div>

            <div class="border border-gray-200 rounded-lg p-3">
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="payment_method" value="TRANSFER" class="mr-3" onchange="togglePaymentFields()">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center mr-3">
                    <i class="fas fa-university text-white text-sm"></i>
                  </div>
                  <span class="font-medium">Transferencia bancaria</span>
                </div>
              </label>
            </div>

            <div class="border border-gray-200 rounded-lg p-3">
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="payment_method" value="CASH" class="mr-3" onchange="togglePaymentFields()">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-green-600 rounded flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill text-white text-sm"></i>
                  </div>
                  <span class="font-medium">Pago contra entrega</span>
                </div>
              </label>
            </div>
          </div>

          <!-- Campos condicionales de pago -->
          <div class="mt-4 space-y-4">
            <!-- Campo de código de verificación para YAPE -->
            <div id="verification_code_field" class="hidden">
              <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-key text-purple-600 mr-1"></i>
                Código de verificación YAPE *
              </label>
              <input type="text" id="verification_code" name="verification_code"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                placeholder="Ingresa el código de verificación de tu pago YAPE">
              <p class="text-xs text-gray-500 mt-1">El código que aparece en tu comprobante de pago YAPE</p>
            </div>

            <!-- Campo de subida de imagen para otros métodos -->
            <div id="proof_upload_field" class="hidden">
              <label for="proof_image" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-image text-blue-600 mr-1"></i>
                Comprobante de pago *
              </label>
              <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                <div class="space-y-1 text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <div class="flex text-sm text-gray-600">
                    <label for="proof_image" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                      <span>Subir comprobante</span>
                      <input id="proof_image" name="proof_image" type="file" class="sr-only" accept="image/*" onchange="handleFileSelect(this)">
                    </label>
                    <p class="pl-1">o arrastra y suelta</p>
                  </div>
                  <p class="text-xs text-gray-500">PNG, JPG hasta 10MB</p>
                </div>
              </div>
              <div id="file_preview" class="mt-2 hidden">
                <div class="flex items-center p-2 bg-green-50 border border-green-200 rounded-lg">
                  <i class="fas fa-check-circle text-green-600 mr-2"></i>
                  <span id="file_name" class="text-sm text-green-800"></span>
                  <button type="button" onclick="removeFile()" class="ml-auto text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Información de pago según el método -->
            <div id="payment_info" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <div id="yape_info">
                <h4 class="font-medium text-blue-900 mb-2">
                  <i class="fas fa-info-circle mr-1"></i>
                  Instrucciones para pago con YAPE
                </h4>
                <div class="text-sm text-blue-800 space-y-1">
                  <p>1. Abre tu app YAPE</p>
                  <p>2. Escanea el código QR o envía el pago al número: <strong>999 888 777</strong></p>
                  <p>3. Ingresa el monto total: <strong>S/ <?= number_format($total, 2) ?></strong></p>
                  <p>4. Completa el pago e ingresa el código de verificación en el campo de arriba</p>
                </div>
              </div>

              <div id="plin_info" class="hidden">
                <h4 class="font-medium text-pink-900 mb-2">
                  <i class="fas fa-info-circle mr-1"></i>
                  Instrucciones para pago con PLIN
                </h4>
                <div class="text-sm text-pink-800 space-y-1">
                  <p>1. Abre tu app PLIN</p>
                  <p>2. Envía el pago al número: <strong>999 888 777</strong></p>
                  <p>3. Ingresa el monto total: <strong>S/ <?= number_format($total, 2) ?></strong></p>
                  <p>4. Toma captura del comprobante y súbela en el campo de arriba</p>
                </div>
              </div>

              <div id="transfer_info" class="hidden">
                <h4 class="font-medium text-blue-900 mb-2">
                  <i class="fas fa-info-circle mr-1"></i>
                  Instrucciones para transferencia bancaria
                </h4>
                <div class="text-sm text-blue-800 space-y-1">
                  <p><strong>Banco:</strong> BCP</p>
                  <p><strong>Número de cuenta:</strong> 123-456789-0-12</p>
                  <p><strong>CCI:</strong> 00212312345678901234</p>
                  <p><strong>Titular:</strong> ModaSalud EIRL</p>
                  <p><strong>Monto:</strong> S/ <?= number_format($total, 2) ?></p>
                  <p class="mt-2">Sube tu comprobante de transferencia en el campo de arriba</p>
                </div>
              </div>

              <div id="cash_info" class="hidden">
                <h4 class="font-medium text-green-900 mb-2">
                  <i class="fas fa-info-circle mr-1"></i>
                  Pago contra entrega
                </h4>
                <div class="text-sm text-green-800 space-y-1">
                  <p>Pagarás en efectivo al momento de recibir tu pedido</p>
                  <p><strong>Monto a pagar:</strong> S/ <?= number_format($total, 2) ?></strong></p>
                  <p>Ten listo el monto exacto para facilitar la entrega</p>
                </div>
              </div>
            </div>
          </div>

          <button type="button" onclick="processOrder()"
            class="w-full mt-6 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-3 px-4 rounded-lg font-semibold transition">
            <i class="fas fa-lock mr-2"></i>
            Finalizar pedido
          </button>
        </div>
      </div>

      <!-- Resumen del pedido -->
      <div class="bg-white rounded-lg shadow-lg p-6 h-fit">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Resumen del pedido</h3>

        <div class="space-y-3">
          <?php foreach ($cart_items as $item): ?>
            <div class="flex items-center space-x-3 pb-3 border-b border-gray-100">
              <img src="/<?= htmlspecialchars($item['main_image'] ?? 'uploads/products/default.png') ?>"
                alt="<?= htmlspecialchars($item['name']) ?>"
                class="w-12 h-12 object-contain rounded border">
              <div class="flex-1">
                <p class="font-medium text-sm text-gray-900"><?= htmlspecialchars($item['name']) ?></p>
                <p class="text-xs text-gray-500">Cantidad: <?= $item['quantity'] ?></p>
              </div>
              <p class="font-bold text-purple-700">S/ <?= number_format($item['subtotal'], 2) ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
          <div class="flex justify-between items-center">
            <span class="text-lg font-bold text-gray-900">Total:</span>
            <span class="text-xl font-bold text-purple-700">S/ <?= number_format($total, 2) ?></span>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Loading overlay -->
  <div id="loading-overlay" class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
      <p class="text-gray-700">Procesando pedido...</p>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>

  <script>
    let clientData = {};
    let isExistingClient = false;
    let selectedFile = null;

    // Función para manejar campos de pago según el método seleccionado
    function togglePaymentFields() {
      const method = document.querySelector('input[name="payment_method"]:checked')?.value;
      const verificationField = document.getElementById('verification_code_field');
      const proofUploadField = document.getElementById('proof_upload_field');
      
      // Referencias a las secciones de información
      const yapeInfo = document.getElementById('yape_info');
      const plinInfo = document.getElementById('plin_info');
      const transferInfo = document.getElementById('transfer_info');
      const cashInfo = document.getElementById('cash_info');
      
      // Ocultar todos los campos primero
      verificationField.classList.add('hidden');
      proofUploadField.classList.add('hidden');
      
      // Ocultar todas las secciones de información
      yapeInfo.classList.add('hidden');
      plinInfo.classList.add('hidden');
      transferInfo.classList.add('hidden');
      cashInfo.classList.add('hidden');
      
      // Mostrar campos según el método de pago
      switch(method) {
        case 'YAPE':
          verificationField.classList.remove('hidden');
          yapeInfo.classList.remove('hidden');
          break;
        case 'PLIN':
          proofUploadField.classList.remove('hidden');
          plinInfo.classList.remove('hidden');
          // Re-configurar drag & drop cuando se muestre el campo
          setTimeout(setupDragAndDrop, 100);
          break;
        case 'TRANSFER':
          proofUploadField.classList.remove('hidden');
          transferInfo.classList.remove('hidden');
          // Re-configurar drag & drop cuando se muestre el campo
          setTimeout(setupDragAndDrop, 100);
          break;
        case 'CASH':
          cashInfo.classList.remove('hidden');
          break;
      }
    }

    // Función para manejar la selección de archivo
    function handleFileSelect(input) {
      const file = input.files[0];
      if (file) {
        if (validateFile(file)) {
          selectedFile = file;
          showFilePreview(file);
        } else {
          input.value = '';
        }
      }
    }

    // Función para validar archivo
    function validateFile(file) {
      // Validar tipo de archivo
      if (!file.type.startsWith('image/')) {
        alert('Por favor selecciona un archivo de imagen válido (PNG, JPG, JPEG)');
        return false;
      }
      
      // Validar tamaño (10MB máximo)
      if (file.size > 10 * 1024 * 1024) {
        alert('El archivo es demasiado grande. Máximo 10MB permitido');
        return false;
      }
      
      return true;
    }

    // Función para mostrar preview del archivo
    function showFilePreview(file) {
      const preview = document.getElementById('file_preview');
      const fileName = document.getElementById('file_name');
      
      fileName.textContent = `${file.name} (${formatFileSize(file.size)})`;
      preview.classList.remove('hidden');
      preview.classList.add('animate-fadeIn');
    }

    // Función para formatear tamaño de archivo
    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Función para remover archivo seleccionado
    function removeFile() {
      selectedFile = null;
      document.getElementById('proof_image').value = '';
      document.getElementById('file_preview').classList.add('hidden');
    }

    // Configurar drag & drop para el área de subida de archivos
    function setupDragAndDrop() {
      const dropArea = document.querySelector('#proof_upload_field .border-dashed');
      const fileInput = document.getElementById('proof_image');
      
      if (!dropArea || !fileInput) return;

      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
      });

      ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
      });

      dropArea.addEventListener('drop', handleDrop, false);

      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }

      function highlight(e) {
        dropArea.classList.add('drag-over');
      }

      function unhighlight(e) {
        dropArea.classList.remove('drag-over');
      }

      function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
          const file = files[0];
          if (validateFile(file)) {
            // Asignar archivo al input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            selectedFile = file;
            showFilePreview(file);
          }
        }
      }
    }

    // Validar campos de pago antes de procesar la orden
    function validatePaymentFields() {
      const method = document.querySelector('input[name="payment_method"]:checked')?.value;
      
      switch(method) {
        case 'YAPE':
          const verificationCode = document.getElementById('verification_code').value.trim();
          if (!verificationCode) {
            alert('Por favor ingresa el código de verificación de YAPE');
            document.getElementById('verification_code').focus();
            return false;
          }
          if (verificationCode.length < 6) {
            alert('El código de verificación debe tener al menos 6 caracteres');
            document.getElementById('verification_code').focus();
            return false;
          }
          break;
          
        case 'PLIN':
          if (!selectedFile) {
            alert('Por favor sube el comprobante de pago de PLIN');
            return false;
          }
          break;
          
        case 'TRANSFER':
          if (!selectedFile) {
            alert('Por favor sube el comprobante de transferencia bancaria');
            return false;
          }
          break;
          
        case 'CASH':
          // No requiere validaciones adicionales
          break;
          
        default:
          alert('Por favor selecciona un método de pago válido');
          return false;
      }
      
      return true;
    }

    // Inicializar campos de pago al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
      togglePaymentFields();
      setupDragAndDrop();
    });

    // Verificar email del cliente
    async function verifyEmail() {
      const email = document.getElementById('email').value.trim();

      if (!email) {
        alert('Por favor ingresa tu email');
        return;
      }

      if (!validateEmail(email)) {
        alert('Por favor ingresa un email válido');
        return;
      }

      try {
        showLoading();

        const response = await fetch('checkout-ajax.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `action=verify_email&email=${encodeURIComponent(email)}`
        });

        const data = await response.json();
        hideLoading();

        if (data.success && data.client) {
          // Cliente existe
          showExistingClient(data.client);
        } else {
          // Cliente nuevo
          showNewClientForm();
          document.getElementById('client_name').focus();
        }
      } catch (error) {
        hideLoading();
        alert('Error al verificar el email. Intenta nuevamente.');
        console.error('Error:', error);
      }
    }

    // Mostrar información de cliente existente
    function showExistingClient(client) {
      clientData = client;
      isExistingClient = true;

      document.getElementById('existing-client-info').innerHTML = `
        <div class="flex items-center mb-2">
          <i class="fas fa-user text-green-600 mr-2"></i>
          <span class="font-semibold">${client.name}</span>
        </div>
        <div class="text-sm text-gray-600">
          <p><i class="fas fa-envelope mr-2"></i>${client.email}</p>
          <p><i class="fas fa-phone mr-2"></i>${client.phone || 'No registrado'}</p>
        </div>
      `;

      hideStep('step-email');
      showStep('step-existing-client');
    }

    // Mostrar formulario de cliente nuevo
    function showNewClientForm() {
      const email = document.getElementById('email').value;

      hideStep('step-email');
      hideStep('step-existing-client');
      showStep('step-new-client');
    }

    // Proceder a la dirección
    async function proceedToAddress() {
      if (!isExistingClient) {
        // Validar formulario de cliente nuevo
        const form = document.getElementById('client-form');
        const formData = new FormData(form);

        const requiredFields = ['client_name', 'client_phone', 'client_dni'];
        for (let field of requiredFields) {
          if (!formData.get(field)) {
            alert(`Por favor completa el campo ${field.replace('client_', '')}`);
            return;
          }
        }

        // Guardar datos del cliente nuevo
        clientData = {
          name: formData.get('client_name'),
          email: document.getElementById('email').value,
          phone: formData.get('client_phone'),
          dni: formData.get('client_dni'),
          gender: formData.get('client_gender'),
          birth_date: formData.get('client_birth_date')
        };
      }

      // Si es cliente existente, cargar sus direcciones
      if (isExistingClient && clientData.id) {
        await loadClientAddresses(clientData.id);
      }

      hideStep('step-new-client');
      hideStep('step-existing-client');
      showStep('step-address');
    }

    // Cargar direcciones del cliente
    async function loadClientAddresses(clientId) {
      try {
        const response = await fetch(`checkout-ajax.php?action=get_client_addresses&client_id=${clientId}`);
        const data = await response.json();

        if (data.success && data.addresses.length > 0) {
          const addressesList = document.getElementById('addresses-list');
          addressesList.innerHTML = '';

          data.addresses.forEach((address, index) => {
            const addressDiv = document.createElement('div');
            addressDiv.className = 'border border-gray-200 rounded-lg p-3 cursor-pointer hover:border-purple-500 transition';
            addressDiv.innerHTML = `
              <label class="flex items-start cursor-pointer">
                <input type="radio" name="selected_address" value="${address.id}" class="mr-3 mt-1" ${address.is_default || index === 0 ? 'checked' : ''}>
                <div class="flex-1">
                  <div class="font-medium text-gray-900">${address.address}</div>
                  <div class="text-sm text-gray-600">${address.city}, ${address.region}</div>
                  ${address.postal_code ? `<div class="text-xs text-gray-500">CP: ${address.postal_code}</div>` : ''}
                  ${address.is_default ? '<span class="inline-block text-xs bg-green-100 text-green-800 px-2 py-1 rounded mt-1">Dirección principal</span>' : ''}
                </div>
              </label>
            `;

            // Agregar evento click al div para seleccionar el radio
            addressDiv.addEventListener('click', function(e) {
              if (e.target.type !== 'radio') {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
              }
            });

            addressesList.appendChild(addressDiv);
          });

          document.getElementById('existing-addresses').classList.remove('hidden');
          // Ocultar el formulario de nueva dirección inicialmente
          document.getElementById('address-form').classList.add('hidden');
        } else {
          // Si no hay direcciones, mostrar el formulario directamente
          document.getElementById('existing-addresses').classList.add('hidden');
          document.getElementById('address-form').classList.remove('hidden');
        }
      } catch (error) {
        console.error('Error loading addresses:', error);
        // En caso de error, mostrar el formulario de nueva dirección
        document.getElementById('existing-addresses').classList.add('hidden');
        document.getElementById('address-form').classList.remove('hidden');
      }
    }

    // Mostrar formulario de nueva dirección
    function showNewAddressForm() {
      // Deseleccionar direcciones existentes
      const radios = document.querySelectorAll('input[name="selected_address"]');
      radios.forEach(radio => radio.checked = false);

      // Mostrar formulario
      document.getElementById('address-form').classList.remove('hidden');
      document.getElementById('address').focus();

      // Añadir clase para indicar que es nueva dirección
      document.getElementById('address-form').setAttribute('data-is-new', 'true');
    }

    // Ocultar formulario de nueva dirección
    function hideNewAddressForm() {
      document.getElementById('address-form').classList.add('hidden');
      document.getElementById('address-form').removeAttribute('data-is-new');

      // Limpiar el formulario
      document.getElementById('address-form').reset();

      // Si hay direcciones existentes, seleccionar la primera por defecto
      const firstAddress = document.querySelector('input[name="selected_address"]');
      if (firstAddress) {
        firstAddress.checked = true;
      }
    }

    // Proceder con dirección seleccionada
    function proceedWithSelectedAddress() {
      const selectedAddress = document.querySelector('input[name="selected_address"]:checked');

      if (!selectedAddress) {
        alert('Por favor selecciona una dirección');
        return;
      }

      hideStep('step-address');
      showStep('step-payment');
    }

    // Proceder al pago
    function proceedToPayment() {
      // Validar dirección
      const selectedAddress = document.querySelector('input[name="selected_address"]:checked');
      const addressForm = document.getElementById('address-form');
      const isNewAddress = addressForm.getAttribute('data-is-new') === 'true';

      // Si no hay dirección seleccionada y el formulario está oculto o vacío
      if (!selectedAddress && (addressForm.classList.contains('hidden') || !addressForm.querySelector('#address').value.trim())) {
        alert('Por favor selecciona una dirección existente o completa el formulario de nueva dirección');
        return;
      }

      // Si está usando nueva dirección, validar campos requeridos
      if (isNewAddress && !addressForm.classList.contains('hidden')) {
        const requiredFields = [{
            id: 'address',
            name: 'Dirección'
          },
          {
            id: 'city',
            name: 'Ciudad'
          },
          {
            id: 'region',
            name: 'Región'
          }
        ];

        let hasErrors = false;
        for (let field of requiredFields) {
          const input = document.getElementById(field.id);
          if (!input.value.trim()) {
            alert(`Por favor completa el campo: ${field.name}`);
            input.focus();
            hasErrors = true;
            break;
          }
        }

        if (hasErrors) return;
      }

      hideStep('step-address');
      showStep('step-payment');
    }

    // Procesar orden
    async function processOrder() {
      const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;

      if (!paymentMethod) {
        alert('Por favor selecciona un método de pago');
        return;
      }

      // Validar campos de pago específicos
      if (!validatePaymentFields()) {
        return;
      }

      try {
        showLoading();

        // Crear FormData para enviar archivos
        const formData = new FormData();
        
        // Datos básicos de la orden
        formData.append('action', 'create_order');
        formData.append('client', JSON.stringify(clientData));
        formData.append('address', JSON.stringify(getAddressData()));
        formData.append('payment_method', paymentMethod);
        formData.append('is_existing_client', isExistingClient);

        // Datos específicos de pago según el método
        switch(paymentMethod) {
          case 'YAPE':
            const verificationCode = document.getElementById('verification_code').value.trim();
            formData.append('verification_code', verificationCode);
            break;
            
          case 'PLIN':
          case 'TRANSFER':
            if (selectedFile) {
              formData.append('proof_image', selectedFile);
            }
            break;
            
          case 'CASH':
            // No requiere datos adicionales
            break;
        }

        const response = await fetch('checkout-ajax.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();
        hideLoading();

        if (data.success) {
          // Redirigir a página de éxito
          window.location.href = `order-success.php?order_id=${data.order_id}`;
        } else {
          alert('Error al procesar la orden: ' + (data.message || 'Error desconocido'));
        }
      } catch (error) {
        hideLoading();
        alert('Error de conexión. Intenta nuevamente.');
        console.error('Error:', error);
      }
    }

    // Obtener datos de dirección
    function getAddressData() {
      const selectedAddress = document.querySelector('input[name="selected_address"]:checked');
      const addressForm = document.getElementById('address-form');
      const isNewAddress = addressForm.getAttribute('data-is-new') === 'true';

      if (selectedAddress && !isNewAddress) {
        // Usar dirección existente
        return {
          id: selectedAddress.value
        };
      } else if (isNewAddress || (!selectedAddress && !addressForm.classList.contains('hidden'))) {
        // Usar nueva dirección
        return {
          address: document.getElementById('address').value.trim(),
          city: document.getElementById('city').value.trim(),
          region: document.getElementById('region').value.trim(),
          postal_code: document.getElementById('postal_code').value.trim(),
          phone: document.getElementById('delivery_phone').value.trim()
        };
      } else {
        // Fallback - usar dirección seleccionada si existe
        if (selectedAddress) {
          return {
            id: selectedAddress.value
          };
        }
        return null;
      }
    }

    // Funciones auxiliares
    function validateEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showStep(stepId) {
      document.getElementById(stepId).classList.remove('hidden');
    }

    function hideStep(stepId) {
      document.getElementById(stepId).classList.add('hidden');
    }

    function showLoading() {
      document.getElementById('loading-overlay').classList.remove('hidden');
    }

    function hideLoading() {
      document.getElementById('loading-overlay').classList.add('hidden');
    }
  </script>
</body>

</html>