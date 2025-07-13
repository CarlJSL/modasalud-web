// JavaScript para la página de checkout
document.addEventListener('DOMContentLoaded', function() {
    loadOrderSummary();
    validateCartItems();
});

// Validar disponibilidad de productos al cargar la página
function validateCartItems() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    if (cart.length === 0) return;
    
    // Mostrar indicador de carga
    const orderSummary = document.getElementById('orderSummary');
    orderSummary.innerHTML += `
        <div id="validationMessage" class="text-center py-2 text-sm text-blue-600">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Verificando disponibilidad de productos...
        </div>
    `;
    
    // Validar stock y precios actuales
    fetch('api/validate-stock.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ items: cart })
    })
    .then(response => response.json())
    .then(data => {
        // Eliminar indicador de carga
        document.getElementById('validationMessage')?.remove();
        
        if (!data.success) {
            showValidationError('Error al verificar los productos. Intente nuevamente.');
            return;
        }
        
        // Si hay productos inválidos
        if (!data.valid) {
            let cartUpdated = false;
            const updatedCart = [...cart];
            
            // Procesar items inválidos
            data.invalidItems.forEach(invalidItem => {
                const index = updatedCart.findIndex(item => item.productId === invalidItem.productId);
                if (index !== -1) {
                    if (invalidItem.reason === 'not_found' || invalidItem.reason === 'inactive') {
                        // Eliminar producto no disponible
                        updatedCart.splice(index, 1);
                        cartUpdated = true;
                    } else if (invalidItem.reason === 'insufficient_stock' && invalidItem.availableStock > 0) {
                        // Ajustar cantidad al stock disponible
                        updatedCart[index].quantity = invalidItem.availableStock;
                        cartUpdated = true;
                    }
                }
            });
            
            // Actualizar precios si han cambiado
            data.updatedItems.forEach(updatedItem => {
                const index = updatedCart.findIndex(item => item.productId === updatedItem.productId);
                if (index !== -1 && updatedItem.priceChanged) {
                    updatedCart[index].price = updatedItem.price;
                    cartUpdated = true;
                }
            });
            
            if (cartUpdated) {
                localStorage.setItem('cart', JSON.stringify(updatedCart));
                loadOrderSummary();
                
                // Mostrar mensaje de actualización
                orderSummary.insertAdjacentHTML('afterbegin', `
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Se han realizado ajustes en su carrito debido a cambios en la disponibilidad o precios.
                                </p>
                            </div>
                        </div>
                    </div>
                `);
            }
        }
    })
    .catch(error => {
        document.getElementById('validationMessage')?.remove();
        console.error('Error:', error);
    });
}

function showValidationError(message) {
    const orderSummary = document.getElementById('orderSummary');
    orderSummary.insertAdjacentHTML('afterbegin', `
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">${message}</p>
                </div>
            </div>
        </div>
    `);
}

function loadOrderSummary() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const orderSummary = document.getElementById('orderSummary');
    const subtotal = document.getElementById('subtotal');
    const orderTotal = document.getElementById('orderTotal');

    if (cart.length === 0) {
        orderSummary.innerHTML = '<p class="text-gray-500 text-center py-4">No hay productos en el carrito</p>';
        subtotal.textContent = 'S/ 0.00';
        orderTotal.textContent = 'S/ 0.00';
        return;
    }

    // Mostrar productos
    orderSummary.innerHTML = cart.map(item => `
        <div class="flex items-center justify-between py-3 border-b border-gray-200">
            <div class="flex-1">
                <h4 class="font-medium">${item.name}</h4>
                <p class="text-sm text-gray-600">Cantidad: ${item.quantity}</p>
            </div>
            <div class="text-right">
                <p class="font-medium">S/ ${(item.price * item.quantity).toFixed(2)}</p>
                <p class="text-sm text-gray-600">S/ ${item.price.toFixed(2)} c/u</p>
            </div>
        </div>
    `).join('');

    // Calcular total
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    subtotal.textContent = `S/ ${total.toFixed(2)}`;
    orderTotal.textContent = `S/ ${total.toFixed(2)}`;
}

function submitOrder() {
    // Validar formulario
    if (!validateForm()) {
        return;
    }

    // Obtener datos del formulario
    const dniValue = document.getElementById('clientDni').value.trim();
    
    const formData = {
        client: {
            name: document.getElementById('clientName').value.trim(),
            email: document.getElementById('clientEmail').value.trim(),
            phone: document.getElementById('clientPhone').value.trim(),
            // Incluir DNI solo si tiene valor
            ...(dniValue && { dni: dniValue })
        },
        address: {
            address: document.getElementById('address').value.trim(),
            city: document.getElementById('city').value.trim(),
            region: document.getElementById('region').value.trim(),
            postal_code: document.getElementById('postalCode').value.trim()
        },
        payment_method: document.querySelector('input[name="paymentMethod"]:checked').value,
        items: JSON.parse(localStorage.getItem('cart')) || []
    };

    // Calcular total
    const total = formData.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    formData.total_price = total;

    // Enviar orden
    fetch('api/create-order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar modal de confirmación
            document.getElementById('orderNumber').textContent = data.order_id;
            showConfirmModal();
            
            // Limpiar carrito
            localStorage.removeItem('cart');
        } else {
            alert('Error al crear la orden: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la orden. Inténtalo de nuevo.');
    });
}

function validateForm() {
    const requiredFields = [
        { id: 'clientName', name: 'Nombre' },
        { id: 'clientEmail', name: 'Email' },
        { id: 'clientPhone', name: 'Teléfono' },
        { id: 'address', name: 'Dirección' },
        { id: 'city', name: 'Ciudad' }
    ];

    let isValid = true;

    // Limpiar errores previos
    document.querySelectorAll('.error-border').forEach(el => {
        el.classList.remove('error-border', 'border-red-500');
    });

    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        const value = element.value.trim();

        if (!value) {
            element.classList.add('error-border', 'border-red-500');
            isValid = false;
        }
    });

    // Validar DNI (opcional pero si se proporciona debe ser válido)
    const dni = document.getElementById('clientDni').value.trim();
    if (dni && (dni.length !== 8 || !/^\d+$/.test(dni))) {
        document.getElementById('clientDni').classList.add('error-border', 'border-red-500');
        isValid = false;
        alert('El DNI debe tener 8 dígitos numéricos');
    }

    // Validar email
    const email = document.getElementById('clientEmail').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
        document.getElementById('clientEmail').classList.add('error-border', 'border-red-500');
        isValid = false;
        alert('Ingresa un email válido');
    }

    // Validar que hay productos en el carrito
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length === 0) {
        alert('No hay productos en el carrito');
        isValid = false;
    }

    if (!isValid) {
        alert('Por favor completa todos los campos obligatorios');
    }

    return isValid;
}

function showConfirmModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function goToHome() {
    window.location.href = 'index.php';
}
