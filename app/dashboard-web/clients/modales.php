<!-- Modal para Crear/Editar Cliente -->
<div id="clientModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                <span id="modalTitle">Nuevo Cliente</span>
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('clientModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="clientForm" class="space-y-6">
                <input type="hidden" id="clientId" name="id">
                <input type="hidden" id="formAction" name="action" value="create">

                <!-- Información Personal -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h6 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        Información Personal
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nombre completo -->
                        <div class="form-group">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user text-gray-400 mr-1"></i>
                                Nombre Completo *
                            </label>
                            <input type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                id="name"
                                name="name"
                                required>
                            <div class="text-red-600 text-xs mt-1 hidden" id="name-error"></div>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                Email *
                            </label>
                            <input type="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                id="email"
                                name="email"
                                required>
                            <div class="text-red-600 text-xs mt-1 hidden" id="email-error"></div>
                        </div>

                        <!-- Teléfono -->
                        <div class="form-group">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-phone text-gray-400 mr-1"></i>
                                Teléfono
                            </label>
                            <input type="tel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                id="phone"
                                name="phone">
                            <div class="text-red-600 text-xs mt-1 hidden" id="phone-error"></div>
                        </div>

                        <!-- DNI -->
                        <div class="form-group">
                            <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-id-card text-gray-400 mr-1"></i>
                                DNI
                            </label>
                            <input type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                id="dni"
                                name="dni"
                                maxlength="8">
                            <div class="text-red-600 text-xs mt-1 hidden" id="dni-error"></div>
                        </div>

                        <!-- Género -->
                        <div class="form-group">
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-venus-mars text-gray-400 mr-1"></i>
                                Género
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                id="gender"
                                name="gender">
                                <option value="">Seleccionar...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                            <div class="text-red-600 text-xs mt-1 hidden" id="gender-error"></div>
                        </div>

                        <!-- Fecha de nacimiento -->
                        <div class="form-group">
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-birthday-cake text-gray-400 mr-1"></i>
                                Fecha de Nacimiento
                            </label>
                            <input type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                id="birth_date"
                                name="birth_date">
                            <div class="text-red-600 text-xs mt-1 hidden" id="birth_date-error"></div>
                        </div>

                        <!-- Estado -->
                        <div class="form-group">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-gray-400 mr-1"></i>
                                Estado
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                id="status"
                                name="status"
                                required>
                                <option value="ACTIVE">Activo</option>
                                <option value="INACTIVE">Inactivo</option>
                            </select>
                            <div class="text-red-600 text-xs mt-1 hidden" id="status-error"></div>
                        </div>
                    </div>
                </div>

                <!-- Dirección (solo para nuevo cliente) -->
                <div class="bg-green-50 rounded-lg p-4" id="addressSection">
                    <h6 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                        Dirección (Opcional)
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Dirección -->
                        <div class="form-group md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-home text-gray-400 mr-1"></i>
                                Dirección
                            </label>
                            <input type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                id="address"
                                name="address"
                                placeholder="Ej: Av. Principal 123">
                            <div class="text-red-600 text-xs mt-1 hidden" id="address-error"></div>
                        </div>

                        <!-- Ciudad -->
                        <div class="form-group">
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-city text-gray-400 mr-1"></i>
                                Ciudad
                            </label>
                            <input type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                id="city"
                                name="city">
                            <div class="text-red-600 text-xs mt-1 hidden" id="city-error"></div>
                        </div>

                        <!-- Región -->
                        <div class="form-group">
                            <label for="region" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-map text-gray-400 mr-1"></i>
                                Región
                            </label>
                            <input type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                id="region"
                                name="region">
                            <div class="text-red-600 text-xs mt-1 hidden" id="region-error"></div>
                        </div>

                        <!-- Código postal -->
                        <div class="form-group">
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-mail-bulk text-gray-400 mr-1"></i>
                                Código Postal
                            </label>
                            <input type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                id="postal_code"
                                name="postal_code">
                            <div class="text-red-600 text-xs mt-1 hidden" id="postal_code-error"></div>
                        </div>

                        <!-- Teléfono de dirección -->
                        <div class="form-group">
                            <label for="address_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-phone-alt text-gray-400 mr-1"></i>
                                Teléfono de Contacto
                            </label>
                            <input type="tel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                id="address_phone"
                                name="address_phone"
                                placeholder="Si es diferente al teléfono principal">
                            <div class="text-red-600 text-xs mt-1 hidden" id="address_phone-error"></div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="closeModal('clientModal')">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="submitButton">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitText">Crear Cliente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Detalles del Cliente -->
<div id="clientDetailModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                Detalles del Cliente
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('clientDetailModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="clientDetailsContent">
            <!-- El contenido se carga dinámicamente via JavaScript -->
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-600">Cargando información del cliente...</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200 px-6 py-4">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Confirmar Desactivación
            </h5>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <p class="text-sm text-gray-700">
                    ¿Estás seguro que deseas desactivar el cliente <strong id="deleteClientName"></strong>?
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    Esta acción cambiará el estado del cliente a "Inactivo" pero no eliminará sus datos ni historial.
                </p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="closeModal('deleteModal')">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" id="confirmDeleteButton">
                    <i class="fas fa-user-slash mr-2"></i>
                    Desactivar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentClientId = null;
let isEditMode = false;

// Función para abrir modal de creación
function openCreateModal() {
    isEditMode = false;
    currentClientId = null;
    
    // Resetear formulario
    document.getElementById('clientForm').reset();
    document.getElementById('formAction').value = 'create';
    document.getElementById('clientId').value = '';
    
    // Actualizar título y botón
    document.getElementById('modalTitle').textContent = 'Nuevo Cliente';
    document.getElementById('submitText').textContent = 'Crear Cliente';
    
    // Mostrar sección de dirección para nuevos clientes
    document.getElementById('addressSection').style.display = 'block';
    
    // Limpiar errores
    clearFormErrors();
    
    // Abrir modal
    openModal('clientModal');
}

// Función para abrir modal de edición
function openEditModal(clientId) {
    isEditMode = true;
    currentClientId = clientId;
    
    // Actualizar título y botón
    document.getElementById('modalTitle').textContent = 'Editar Cliente';
    document.getElementById('submitText').textContent = 'Actualizar Cliente';
    document.getElementById('formAction').value = 'update';
    document.getElementById('clientId').value = clientId;
    
    // Ocultar sección de dirección en edición
    document.getElementById('addressSection').style.display = 'none';
    
    // Limpiar errores
    clearFormErrors();
    
    // Cargar datos del cliente
    loadClientData(clientId);
    
    // Abrir modal
    openModal('clientModal');
}

// Función para cargar datos del cliente
function loadClientData(clientId) {
    fetch(`clientes.php?action=details&id=${clientId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const client = data.client;
                
                // Llenar formulario
                document.getElementById('name').value = client.name || '';
                document.getElementById('email').value = client.email || '';
                document.getElementById('phone').value = client.phone || '';
                document.getElementById('dni').value = client.dni || '';
                document.getElementById('gender').value = client.gender || '';
                document.getElementById('birth_date').value = client.birth_date || '';
                document.getElementById('status').value = client.status || 'ACTIVE';
                
            } else {
                showAlert('Error al cargar los datos del cliente', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al cargar los datos del cliente', 'error');
        });
}

// Función para abrir modal de detalles
function openDetailModal(clientId) {
    currentClientId = clientId;
    
    // Mostrar loading
    document.getElementById('clientDetailsContent').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Cargando información del cliente...</span>
        </div>
    `;
    
    // Abrir modal
    openModal('clientDetailModal');
    
    // Cargar detalles
    loadClientDetails(clientId);
}

// Función para cargar detalles completos del cliente
function loadClientDetails(clientId) {
    fetch(`clientes.php?action=details&id=${clientId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderClientDetails(data.client);
            } else {
                document.getElementById('clientDetailsContent').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-3"></i>
                        <p class="text-red-600">Error al cargar los detalles del cliente</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('clientDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-wifi text-red-400 text-4xl mb-3"></i>
                    <p class="text-red-600">Error de conexión</p>
                </div>
            `;
        });
}

// Función para renderizar detalles del cliente
function renderClientDetails(client) {
    const content = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información Principal -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-2xl font-bold">
                            ${client.name.charAt(0).toUpperCase()}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">${client.name}</h3>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${client.status_color}">
                        <i class="fas fa-circle mr-2 text-xs"></i>
                        ${client.status}
                    </div>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${client.customer_tier.color} mt-2">
                        ${client.customer_tier.tier}
                    </div>
                </div>
                
                <!-- Información de Contacto -->
                <div class="bg-white rounded-xl border border-gray-200 p-4 mt-4">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-address-card text-blue-500 mr-2"></i>
                        Información de Contacto
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-400 w-4 mr-3"></i>
                            <span>${client.email}</span>
                        </div>
                        ${client.phone ? `
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 w-4 mr-3"></i>
                                <span>${client.phone}</span>
                            </div>
                        ` : ''}
                        ${client.dni ? `
                            <div class="flex items-center">
                                <i class="fas fa-id-card text-gray-400 w-4 mr-3"></i>
                                <span>${client.dni}</span>
                            </div>
                        ` : ''}
                        ${client.gender ? `
                            <div class="flex items-center">
                                <i class="fas fa-venus-mars text-gray-400 w-4 mr-3"></i>
                                <span>${client.gender}</span>
                            </div>
                        ` : ''}
                        ${client.age ? `
                            <div class="flex items-center">
                                <i class="fas fa-birthday-cake text-gray-400 w-4 mr-3"></i>
                                <span>${client.age} años</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div class="bg-white rounded-xl border border-gray-200 p-4 mt-4">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                        Estadísticas
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="text-center bg-blue-50 rounded-lg p-3">
                            <div class="text-lg font-bold text-blue-600">${client.statistics.total_orders}</div>
                            <div class="text-gray-600">Órdenes</div>
                        </div>
                        <div class="text-center bg-green-50 rounded-lg p-3">
                            <div class="text-lg font-bold text-green-600">S/ ${parseFloat(client.statistics.total_spent).toFixed(2)}</div>
                            <div class="text-gray-600">Gastado</div>
                        </div>
                        <div class="text-center bg-yellow-50 rounded-lg p-3">
                            <div class="text-lg font-bold text-yellow-600">${client.statistics.cart_items_count}</div>
                            <div class="text-gray-600">En Carrito</div>
                        </div>
                        <div class="text-center bg-purple-50 rounded-lg p-3">
                            <div class="text-lg font-bold text-purple-600">${client.statistics.total_reviews}</div>
                            <div class="text-gray-600">Reseñas</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contenido Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Direcciones -->
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                        Direcciones (${client.addresses ? client.addresses.length : 0})
                    </h4>
                    ${client.addresses && client.addresses.length > 0 ? 
                        client.addresses.map(address => `
                            <div class="border border-gray-200 rounded-lg p-3 mb-3 ${address.is_default ? 'border-blue-300 bg-blue-50' : ''}">
                                ${address.is_default ? '<div class="text-xs text-blue-600 font-medium mb-2"><i class="fas fa-star mr-1"></i>Dirección Principal</div>' : ''}
                                <div class="text-sm">
                                    <div class="font-medium">${address.address}</div>
                                    <div class="text-gray-600">${address.city}, ${address.region} ${address.postal_code}</div>
                                    ${address.phone ? `<div class="text-gray-600"><i class="fas fa-phone text-xs mr-1"></i>${address.phone}</div>` : ''}
                                </div>
                            </div>
                        `).join('')
                    : '<p class="text-gray-500 text-sm">No hay direcciones registradas</p>'}
                </div>
                
                <!-- Órdenes Recientes -->
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-shopping-bag text-green-500 mr-2"></i>
                        Órdenes Recientes
                    </h4>
                    ${client.recent_orders && client.recent_orders.length > 0 ? `
                        <div class="space-y-3">
                            ${client.recent_orders.slice(0, 5).map(order => `
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium text-sm">#${order.id}</div>
                                            <div class="text-xs text-gray-600">${new Date(order.created_at).toLocaleDateString()}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-sm">S/ ${parseFloat(order.total_price).toFixed(2)}</div>
                                            <div class="text-xs">
                                                <span class="px-2 py-1 rounded-full ${order.status === 'COMPLETED' ? 'bg-green-100 text-green-800' : order.status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                                                    ${order.status}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : '<p class="text-gray-500 text-sm">No hay órdenes registradas</p>'}
                </div>
                
                <!-- Carrito Actual -->
                ${client.cart_items && client.cart_items.length > 0 ? `
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-shopping-cart text-orange-500 mr-2"></i>
                            Carrito Actual (${client.cart_items.length} items)
                        </h4>
                        <div class="space-y-2">
                            ${client.cart_items.map(item => `
                                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                                    <div>
                                        <div class="font-medium text-sm">${item.product_name}</div>
                                        <div class="text-xs text-gray-600">Cantidad: ${item.quantity}</div>
                                    </div>
                                    <div class="text-sm font-medium">S/ ${parseFloat(item.subtotal).toFixed(2)}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('clientDetailsContent').innerHTML = content;
}

// Función para abrir modal de eliminación
function openDeleteModal(clientId, clientName) {
    currentClientId = clientId;
    document.getElementById('deleteClientName').textContent = clientName;
    openModal('deleteModal');
}

// Event listener para formulario de cliente
document.getElementById('clientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = document.getElementById('submitButton');
    const originalText = submitButton.innerHTML;
    
    // Mostrar loading
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';
    submitButton.disabled = true;
    
    // Limpiar errores previos
    clearFormErrors();
    
    fetch('clientes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeModal('clientModal');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            if (data.errors) {
                // Mostrar errores específicos
                Object.keys(data.errors).forEach(field => {
                    showFieldError(field, data.errors[field]);
                });
            }
            showAlert(data.message || 'Error al procesar la solicitud', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexión', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
});

// Event listener para confirmación de eliminación
document.getElementById('confirmDeleteButton').addEventListener('click', function() {
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', currentClientId);
    
    const button = this;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Desactivando...';
    button.disabled = true;
    
    fetch('clientes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeModal('deleteModal');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert(data.message || 'Error al desactivar cliente', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error de conexión', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
});

// Funciones auxiliares
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
    
    // Limpiar formulario si es el modal de cliente
    if (modalId === 'clientModal') {
        document.getElementById('clientForm').reset();
        clearFormErrors();
    }
}

function clearFormErrors() {
    const errorElements = document.querySelectorAll('[id$="-error"]');
    errorElements.forEach(element => {
        element.classList.add('hidden');
        element.textContent = '';
    });
    
    // Remover clases de error de los inputs
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.classList.remove('border-red-500');
    });
}

function showFieldError(fieldName, message) {
    const errorElement = document.getElementById(fieldName + '-error');
    const inputElement = document.getElementById(fieldName);
    
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
    
    if (inputElement) {
        inputElement.classList.add('border-red-500');
    }
}

function showAlert(message, type = 'info') {
    // Crear elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-md shadow-lg text-white text-sm max-w-sm ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Remover después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}

// Cerrar modales al hacer clic fuera
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        const modals = ['clientModal', 'clientDetailModal', 'deleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});

// Cerrar modales con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = ['clientModal', 'clientDetailModal', 'deleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});
</script>
