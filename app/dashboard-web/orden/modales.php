<!-- Modal para Crear/Editar Orden -->
<div id="orderModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 id="orderModalTitle" class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-shopping-cart text-green-600 mr-2"></i>
                Nueva Orden
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('orderModal')">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-6">
            <!-- Mensaje del formulario -->

            <form id="orderForm" class="space-y-6">
                <input type="hidden" id="is_new_client" name="is_new_client" value="0">
                <input type="hidden" id="orderId" name="id">
                <input type="hidden" id="orderFormAction" name="action" value="create">

                <!-- Información del Cliente -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h6 class="text-sm font-medium text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-user text-blue-600 mr-2"></i>
                        Información del Cliente
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar por DNI</label>
                            <div class="flex space-x-2">
                                <input type="text" id="search_dni" name="search_dni" maxlength="8"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ingrese DNI">
                                <button type="button" onclick="searchClientByDNI()"
                                    class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <span id="search_dni-error" class="hidden text-red-500 text-xs mt-1"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                            <select id="client_id" name="client_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="toggleNewClientFields(this)">
                                <option value="">Seleccionar cliente...</option>
                                <option value="__new__">+ Nuevo cliente</option>
                            </select>
                            <span id="client_id-error" class="hidden text-red-500 text-xs mt-1"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección de Entrega *</label>
                            <select id="address_id" name="address_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="toggleNewAddressFields(this)">
                                <option value="">Seleccionar dirección...</option>
                                <option value="__new__">+ Nueva dirección</option>
                            </select>
                            <span id="address_id-error" class="hidden text-red-500 text-xs mt-1"></span>
                        </div>
                    </div>
                    <!-- Campos para nuevo cliente -->
                    <div id="newClientFields" class="hidden grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Cliente *</label>
                            <input type="text" id="new_client_name" name="new_client_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="new_client_email" name="new_client_email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" id="new_client_phone" name="new_client_phone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                            <select id="new_client_gender" name="new_client_gender"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="O">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                            <input type="date" id="new_client_birth" name="new_client_birth"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Campos para nueva dirección -->
                    <div id="newAddressFields" class="grid-cols-1 md:grid-cols-2 gap-4 mt-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección *</label>
                            <input type="text" id="new_address" name="new_address"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad *</label>
                            <input type="text" id="new_city" name="new_city"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Región</label>
                            <input type="text" id="new_region" name="new_region"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                            <input type="text" id="new_postal_code" name="new_postal_code"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" id="new_delivery_phone" name="new_delivery_phone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h6 class="text-sm font-medium text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-shopping-bag text-green-600 mr-2"></i>
                        Productos
                    </h6>
                    <div class="space-y-3">
                        <div class="flex space-x-2">
                            <select id="product_select" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar producto...</option>
                            </select>
                            <input type="number" id="quantity_input" placeholder="Cant." min="1" value="1"
                                class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="addProduct()"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div id="products_list" class="space-y-2">
                            <!-- Los productos se agregarán dinámicamente aquí -->
                        </div>
                        <span id="items-error" class="hidden text-red-500 text-xs"></span>
                    </div>
                </div>

                <!-- Información de Pago y Descuentos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información de Pago -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h6 class="text-sm font-medium text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                            Información de Pago
                        </h6>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                                <select id="payment_method" name="payment_method"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="CASH">Efectivo</option>
                                    <option value="YAPE">Yape</option>
                                    <option value="PLIN">Plin</option>
                                    <option value="TRANSFER">Transferencia</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Pago</label>
                                <select id="payment_status" name="payment_status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="PENDING">Pendiente</option>
                                    <option value="PAID">Pagado</option>
                                    <option value="FAILED">Fallido</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comprobante de Pago</label>
                                <input type="text" id="proof_url" name="proof_url" placeholder="URL del comprobante"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Descuentos y Totales -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h6 class="text-sm font-medium text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-percentage text-orange-600 mr-2"></i>
                            Descuentos y Totales
                        </h6>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cupón de Descuento</label>
                                <select id="coupon_id" name="coupon_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Sin cupón</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descuento Adicional</label>
                                <input type="number" id="discount_amount" name="discount_amount" step="0.01" value="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total *</label>
                                <input type="number" id="total_price" name="total_price" step="0.01" required readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none">
                                <span id="total_price-error" class="hidden text-red-500 text-xs mt-1"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado de la Orden -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h6 class="text-sm font-medium text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-flag text-red-600 mr-2"></i>
                        Estado de la Orden
                    </h6>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="status" name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="PENDING">Pendiente</option>
                            <option value="COMPLETED">Completada</option>
                            <option value="CANCELLED">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div id="orderFormMessage" class="hidden mb-4"></div>

            </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                onclick="closeModal('orderModal')">
                Cancelar
            </button>
            <button type="button" onclick="submitOrder()"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                <span id="submitOrderText">Crear Orden</span>
            </button>
        </div>
    </div>
</div>

<script>
    function searchClientByDNI() {
        const dni = document.getElementById('search_dni').value.trim();
        const errorSpan = document.getElementById('search_dni-error');
        errorSpan.classList.add('hidden');
        if (!dni || dni.length !== 8 || isNaN(dni)) {
            errorSpan.textContent = 'Ingrese un DNI válido de 8 dígitos';
            errorSpan.classList.remove('hidden');
            return;
        }
        // Mostrar loading si se desea
        fetch('orders.php?action=search_client_by_dni&dni=' + encodeURIComponent(dni))
            .then(res => res.json())
            .then(data => {
                if (data.success && data.client) {
                    // Seleccionar el cliente en el select y ocultar campos de nuevo cliente
                    const clientSelect = document.getElementById('client_id');
                    document.getElementById('is_new_client').value = '0';

                    let found = false;
                    for (let i = 0; i < clientSelect.options.length; i++) {
                        if (clientSelect.options[i].value == data.client.id) {
                            clientSelect.selectedIndex = i;
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        // Si no está en el select, agregarlo temporalmente
                        const opt = document.createElement('option');
                        opt.value = data.client.id;
                        opt.text = data.client.name + ' - ' + data.client.email;
                        clientSelect.add(opt, 1); // Insert after placeholder
                        clientSelect.selectedIndex = 1;
                    }
                    document.getElementById('newClientFields').classList.add('hidden');
                    // Cargar direcciones del cliente
                    loadClientAddresses(data.client.id);
                } else {
                    // No existe, mostrar campos para nuevo cliente y limpiar selects
                    document.getElementById('client_id').value = '__new__';
                    document.getElementById('is_new_client').value = '1';

                    document.getElementById('newClientFields').classList.remove('hidden');
                    document.getElementById('address_id').innerHTML = '<option value="">Seleccionar dirección...</option><option value="__new__">+ Nueva dirección</option>';
                    document.getElementById('newAddressFields').classList.remove('hidden');
                }
            })
            .catch(() => {
                errorSpan.textContent = 'Error al buscar el cliente';
                errorSpan.classList.remove('hidden');
            });
    }

    // Modifica toggleNewClientFields para mostrar/ocultar campos de nuevo cliente
    function toggleNewClientFields(select) {
        if (select.value === '__new__') {
            document.getElementById('newClientFields').classList.remove('hidden');
            document.getElementById('newAddressFields').classList.remove('hidden');
            document.getElementById('address_id').innerHTML = '<option value="">Seleccionar dirección...</option><option value="__new__">+ Nueva dirección</option>';
        } else {
            document.getElementById('newClientFields').classList.add('hidden');
            // Solo mostrar campos de dirección nueva si se selecciona esa opción
            if (document.getElementById('address_id').value === '__new__') {
                document.getElementById('newAddressFields').classList.remove('hidden');
            } else {
                document.getElementById('newAddressFields').classList.add('hidden');
            }
        }
    }

    // Modifica toggleNewAddressFields para mostrar/ocultar campos de nueva dirección
    function toggleNewAddressFields(select) {
        if (select.value === '__new__') {
            document.getElementById('newAddressFields').classList.remove('hidden');
        } else {
            document.getElementById('newAddressFields').classList.add('hidden');
        }
    }
</script>

</script>
<div id="orderStatusModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn modal-content">
        <div id="orderModalHeader" class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 id="orderModalTitle" class="text-lg font-semibold text-gray-800 flex items-center">
                <i id="orderModalIcon" class="fas fa-question-circle text-blue-500 mr-2"></i>
                Confirmar acción
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('orderStatusModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="text-center">
                <div id="orderModalCircle" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <i id="orderModalCircleIcon" class="fas fa-question text-blue-600 text-xl"></i>
                </div>
                <h3 id="orderModalQuestion" class="text-lg font-medium text-gray-900 mb-2">¿Estás seguro?</h3>
                <p id="orderModalText" class="text-sm text-gray-500 mb-4">
                    ¿Deseas continuar con esta acción?
                </p>
            </div>
            <input type="hidden" id="orderIdInput">
            <input type="hidden" id="orderNewStatusInput">
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" onclick="closeModal('orderStatusModal')">
                <i class="fas fa-times mr-1"></i> Cancelar
            </button>
            <button type="button"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
                onclick="submitOrderStatusChange()"
                id="confirmOrderStatusBtn">
                <i class="fas fa-check mr-1"></i> Confirmar
            </button>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de la orden -->
<div id="orderDetailModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[95vh] overflow-y-auto animate-fadeIn modal-content">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border-b border-gray-200 px-6 py-5 flex justify-between items-center rounded-t-xl">
            <h5 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-shopping-cart text-indigo-600 mr-3"></i>
                Detalles de la Orden
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-all duration-200"
                onclick="closeModal('orderDetailModal')">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Contenido dinámico -->
        <div class="p-6">
            <div id="orderDetails">
                <!-- Los detalles se cargarán dinámicamente aquí -->
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-200 rounded-b-xl">
            <button type="button" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
                onclick="closeModal('orderDetailModal')">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    // Variables globales
    let originalOrderData = {};
    let orderProducts = [];
    let availableProducts = [];
    let availableClients = [];
    let availableCoupons = [];

    // Función para mostrar modal
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.15)';

            setTimeout(() => {
                const content = modal.querySelector('.modal-content');
                if (content) {
                    content.style.opacity = '1';
                    content.style.transform = 'scale(1)';
                }
            }, 10);
        }
    }

    // Función para cerrar modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const content = modal.querySelector('.modal-content');
            if (content) {
                content.style.opacity = '0';
                content.style.transform = 'scale(0.95)';
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }, 200);
        }
    }

    // Cerrar modal al hacer clic en el fondo
    document.addEventListener('click', function(e) {
        if (e.target.id === 'orderModal' || e.target.id === 'orderDetailModal') {
            closeModal(e.target.id);
        }
    });

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('orderModal');
            closeModal('orderDetailModal');
        }
    });

    // Función para abrir modal de crear orden
    function openCreateModal() {
        originalOrderData = {};
        orderProducts = [];

        document.getElementById('orderForm').reset();
        document.getElementById('orderId').value = '';
        document.getElementById('orderFormAction').value = 'create';
        document.getElementById('orderModalTitle').textContent = 'Nueva Orden';
        document.getElementById('submitOrderText').textContent = 'Crear Orden';

        // Habilitar el formulario (en caso de que esté deshabilitado por una orden anterior)
        enableOrderForm();

        clearOrderFormErrors();
        loadOrderData();
        updateProductsList();
        updateOrderTotal();

        showModal('orderModal');
    }

    // Función para abrir modal de editar orden
    function openEditModal(orderId) {
        document.getElementById('orderForm').reset();
        document.getElementById('orderId').value = orderId;
        document.getElementById('orderFormAction').value = 'update';
        document.getElementById('orderModalTitle').textContent = 'Editar Orden';
        document.getElementById('submitOrderText').textContent = 'Actualizar Orden';

        // Habilitar el formulario (en caso de que esté deshabilitado)
        enableOrderForm();

        clearOrderFormErrors();
        loadOrderData();

        // Cargar datos de la orden
        fetch(`orders.php?action=get&id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const order = data.order;
                    originalOrderData = order;

                    // Llenar el formulario con los datos
                    document.getElementById('client_id').value = order.client_id;
                    document.getElementById('address_id').value = order.address_id;
                    document.getElementById('total_price').value = order.total_price;
                    document.getElementById('status').value = order.status;
                    document.getElementById('discount_amount').value = order.discount_amount || 0;
                    document.getElementById('coupon_id').value = order.coupon_id || '';

                    // Cargar direcciones del cliente
                    if (order.client_id) {
                        loadClientAddresses(order.client_id);
                    }
                } else {
                    alert('Error al cargar los datos de la orden: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos de la orden');
            });

        showModal('orderModal');
    }

    // Función para abrir modal de detalles de orden
    function openDetailModal(orderId) {
        showModal('orderDetailModal');

        // Cargar detalles de la orden
        fetch(`orders.php?action=details&id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayOrderDetails(data.order);
                } else {
                    document.getElementById('orderDetails').innerHTML =
                        '<p class="text-red-500 text-center">Error al cargar los detalles de la orden</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('orderDetails').innerHTML =
                    '<p class="text-red-500 text-center">Error al cargar los detalles de la orden</p>';
            });
    }

    // Función para cargar datos necesarios para el formulario
    function loadOrderData() {
        // Cargar clientes
        fetch('orders.php?action=get_clients')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availableClients = data.clients;
                    const clientSelect = document.getElementById('client_id');
                    clientSelect.innerHTML = '<option value="">Seleccionar cliente...</option>';
                    data.clients.forEach(client => {
                        clientSelect.innerHTML += `<option value="${client.id}">${client.name} - ${client.email}</option>`;
                    });
                }
            });

        // Cargar productos
        fetch('orders.php?action=get_products')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availableProducts = data.products;
                    const productSelect = document.getElementById('product_select');
                    productSelect.innerHTML = '<option value="">Seleccionar producto...</option>';
                    data.products.forEach(product => {
                        productSelect.innerHTML += `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">${product.name} - S/ ${product.price} (Stock: ${product.stock})</option>`;
                    });
                }
            });

        // Cargar cupones
        fetch('orders.php?action=get_coupons')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availableCoupons = data.coupons;
                    const couponSelect = document.getElementById('coupon_id');
                    couponSelect.innerHTML = '<option value="">Sin cupón</option>';
                    data.coupons.forEach(coupon => {
                        const description = coupon.description || `${coupon.discount_value}${coupon.discount_type === 'PERCENTAGE' ? '%' : ' S/'} de descuento`;
                        couponSelect.innerHTML += `<option value="${coupon.id}" data-type="${coupon.discount_type}" data-value="${coupon.discount_value}">${coupon.code} - ${description}</option>`;
                    });
                }
            });
    }

    // Función para cargar direcciones del cliente
    function loadClientAddresses(clientId) {
        fetch(`orders.php?action=get_client_addresses&client_id=${clientId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const addressSelect = document.getElementById('address_id');
                    addressSelect.innerHTML = '<option value="">Seleccionar dirección...</option>';
                    data.addresses.forEach(address => {
                        const addressText = `${address.address}, ${address.city}${address.region ? ', ' + address.region : ''}`;
                        const defaultText = address.is_default ? ' (Principal)' : '';
                        addressSelect.innerHTML += `<option value="${address.id}">${addressText}${defaultText}</option>`;
                    });
                }
            });
    }

    // Event listener para cambio de cliente
    document.getElementById('client_id').addEventListener('change', function() {
        const clientId = this.value;
        if (clientId) {
            loadClientAddresses(clientId);
        } else {
            document.getElementById('address_id').innerHTML = '<option value="">Seleccionar dirección...</option>';
        }
    });

    // Función para agregar producto
    function addProduct() {
        const productSelect = document.getElementById('product_select');
        const quantityInput = document.getElementById('quantity_input');

        const productId = productSelect.value;
        const quantity = parseInt(quantityInput.value) || 1;

        if (!productId) {
            alert('Debe seleccionar un producto');
            return;
        }

        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price);
        const stock = parseInt(selectedOption.dataset.stock);
        const productName = selectedOption.text.split(' - ')[0];

        if (quantity > stock) {
            alert(`No hay suficiente stock. Disponible: ${stock}`);
            return;
        }

        // Verificar si el producto ya está en la lista
        const existingIndex = orderProducts.findIndex(p => p.product_id === productId);
        if (existingIndex !== -1) {
            orderProducts[existingIndex].quantity += quantity;
        } else {
            orderProducts.push({
                product_id: productId,
                name: productName,
                price: price,
                quantity: quantity,
                stock: stock
            });
        }

        updateProductsList();
        updateOrderTotal();

        // Limpiar selección
        productSelect.value = '';
        quantityInput.value = 1;
    }

    function openOrderStatusModal(orderId, newStatus) {
        const modal = document.getElementById('orderStatusModal');
        const modalTitle = document.getElementById('orderModalTitle');
        const modalQuestion = document.getElementById('orderModalQuestion');
        const modalText = document.getElementById('orderModalText');
        const modalCircle = document.getElementById('orderModalCircle');
        const modalCircleIcon = document.getElementById('orderModalCircleIcon');
        const header = document.getElementById('orderModalHeader');

        document.getElementById('orderIdInput').value = orderId;
        document.getElementById('orderNewStatusInput').value = newStatus;

        if (newStatus === 'COMPLETED') {
            modalTitle.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-2"></i> Confirmar finalización';
            modalQuestion.textContent = '¿Marcar esta orden como completada?';
            modalText.textContent = 'Esta acción marcará la orden como completada. No podrá modificarse luego.';
            modalCircle.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4';
            modalCircleIcon.className = 'fas fa-check text-green-600 text-xl';
            header.className = 'bg-gradient-to-r from-green-50 to-lime-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center';
        } else {
            modalTitle.innerHTML = '<i class="fas fa-times-circle text-red-500 mr-2"></i> Confirmar cancelación';
            modalQuestion.textContent = '¿Cancelar esta orden?';
            modalText.textContent = 'Esta acción marcará la orden como cancelada. No podrá revertirse.';
            modalCircle.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4';
            modalCircleIcon.className = 'fas fa-times text-red-600 text-xl';
            header.className = 'bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center';
        }

        showModal('orderStatusModal');
    }


    function submitOrderStatusChange() {
        const id = document.getElementById('orderIdInput').value;
        const status = document.getElementById('orderNewStatusInput').value;

        fetch('orders.php?action=update_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeModal('orderStatusModal');
                    setTimeout(() => location.reload(), 1000);
                }
                // Opcional: recargar tabla o parte del DOM con JS

            })
            .catch(err => {
                closeModal('orderStatusModal');
                showToast('Error al actualizar la orden', true);
            });
    }

    function confirmOrderStatusChange() {
        const orderId = document.getElementById('orderIdInput').value;
        const newStatus = document.getElementById('orderNewStatusInput').value;

        window.location.href = `orders.php?action=updateStatus&id=${orderId}&status=${newStatus}`;
    }

    function showModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }


    // Función para actualizar lista de productos
    function updateProductsList() {
        const productsList = document.getElementById('products_list');

        if (orderProducts.length === 0) {
            productsList.innerHTML = '<p class="text-gray-500 text-sm">No hay productos agregados</p>';
            return;
        }

        productsList.innerHTML = orderProducts.map((product, index) => `
            <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md">
                <div class="flex-1">
                    <p class="font-medium text-sm">${product.name}</p>
                    <p class="text-xs text-gray-500">S/ ${product.price.toFixed(2)} c/u</p>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="number" value="${product.quantity}" min="1" max="${product.stock}" 
                           class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                           onchange="updateProductQuantity(${index}, this.value)">
                    <span class="text-sm font-medium">S/ ${(product.price * product.quantity).toFixed(2)}</span>
                    <button type="button" onclick="removeProduct(${index})" 
                            class="text-red-600 hover:text-red-900 text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Función para actualizar cantidad de producto
    function updateProductQuantity(index, newQuantity) {
        const quantity = parseInt(newQuantity);
        if (quantity > 0 && quantity <= orderProducts[index].stock) {
            orderProducts[index].quantity = quantity;
            updateProductsList();
            updateOrderTotal();
        }
    }

    // Función para remover producto
    function removeProduct(index) {
        orderProducts.splice(index, 1);
        updateProductsList();
        updateOrderTotal();
    }

    // Función para actualizar total de la orden
    function updateOrderTotal() {
        let subtotal = orderProducts.reduce((sum, product) => sum + (product.price * product.quantity), 0);
        let discount = parseFloat(document.getElementById('discount_amount').value) || 0;

        // Aplicar descuento de cupón si existe
        const couponSelect = document.getElementById('coupon_id');
        const selectedCoupon = couponSelect.options[couponSelect.selectedIndex];
        if (selectedCoupon && selectedCoupon.value) {
            const couponType = selectedCoupon.dataset.type;
            const couponValue = parseFloat(selectedCoupon.dataset.value);

            if (couponType === 'PERCENTAGE') {
                discount += subtotal * (couponValue / 100);
            } else {
                discount += couponValue;
            }
        }

        const total = Math.max(0, subtotal - discount);
        document.getElementById('total_price').value = total.toFixed(2);
    }

    // Event listeners para recalcular total
    document.getElementById('discount_amount').addEventListener('input', updateOrderTotal);
    document.getElementById('coupon_id').addEventListener('change', updateOrderTotal);

    // Función para deshabilitar el formulario y el botón
    function disableOrderForm() {
        const form = document.getElementById('orderForm');
        const submitButton = document.getElementById('submitOrderText').parentElement;
        
        // Deshabilitar el botón de envío
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        
        // Deshabilitar todos los campos del formulario
        const formElements = form.querySelectorAll('input, select, textarea, button');
        formElements.forEach(element => {
            if (element.type !== 'hidden' && element !== submitButton) {
                element.disabled = true;
                element.classList.add('opacity-50');
            }
        });
        
        // Deshabilitar botones de acción en la tabla de productos
        const actionButtons = document.querySelectorAll('#orderProductsTable button');
        actionButtons.forEach(button => {
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
        });
    }

    // Función para habilitar el formulario y el botón (solo en caso de error)
    function enableOrderForm() {
        const form = document.getElementById('orderForm');
        const submitButton = document.getElementById('submitOrderText').parentElement;
        
        // Habilitar el botón de envío
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        
        // Habilitar todos los campos del formulario
        const formElements = form.querySelectorAll('input, select, textarea, button');
        formElements.forEach(element => {
            if (element.type !== 'hidden') {
                element.disabled = false;
                element.classList.remove('opacity-50');
            }
        });
        
        // Habilitar botones de acción en la tabla de productos
        const actionButtons = document.querySelectorAll('#orderProductsTable button');
        actionButtons.forEach(button => {
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    }

    // Función para enviar formulario de orden
    function submitOrder() {
        console.log('Iniciando envío de orden...');

        // Verificar si ya se está procesando una orden
        const submitButton = document.getElementById('submitOrderText').parentElement;
        if (submitButton.disabled) {
            console.log('Ya se está procesando una orden');
            return;
        }

        const form = document.getElementById('orderForm');
        const formData = new FormData(form);

        // Agregar productos al FormData
        formData.append('items', JSON.stringify(orderProducts));
        console.log('Productos a enviar:', orderProducts);

        const isNewClient = document.getElementById('is_new_client').value;
        formData.append('is_new_client', isNewClient);
        console.log('Es nuevo cliente:', isNewClient);

        // Validar campos requeridos
        if (!validateOrderForm()) {
            console.log('Validación del formulario falló');
            return;
        }

        console.log('Validación del formulario exitosa');

        // Detectar si se está creando un cliente nuevo
        const clientId = formData.get('client_id');
        const addressId = formData.get('address_id');

        if (clientId === '__new__') {
            formData.append('is_new_client', '1');
            formData.append('new_client_name', document.getElementById('new_client_name').value.trim());
            formData.append('new_client_email', document.getElementById('new_client_email').value.trim());
            formData.append('new_client_phone', document.getElementById('new_client_phone').value.trim());
            formData.append('new_client_gender', document.getElementById('new_client_gender')?.value || '');
            formData.append('new_client_birth', document.getElementById('new_client_birth')?.value || '');
        }

        // Detectar si se está creando una dirección nueva
        if (addressId === '__new__') {
            formData.append('is_new_address', '1');
            formData.append('new_address', document.getElementById('new_address').value.trim());
            formData.append('new_city', document.getElementById('new_city').value.trim());
            formData.append('new_region', document.getElementById('new_region').value.trim());
            formData.append('new_postal_code', document.getElementById('new_postal_code').value.trim());
            formData.append('new_delivery_phone', document.getElementById('new_delivery_phone').value.trim());
        }

        // Deshabilitar formulario y botón inmediatamente
        disableOrderForm();

        // Mostrar loading
        document.getElementById('submitOrderText').innerHTML =
            '<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...';

        console.log('Enviando datos al servidor...');

        // Mostrar todos los datos que se están enviando
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        fetch('orders.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito con botón de PDF
                    let successMessage = data.message;
                    if (data.pdf_url && document.getElementById('orderFormAction').value === 'create') {
                        successMessage += `
                            <div class="mt-3 flex gap-2">
                                <button type="button" onclick="window.open('${data.pdf_url}', '_blank')" 
                                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-file-pdf mr-2"></i>
                                    Ver PDF de la Orden
                                </button>
                                <button type="button" onclick="downloadOrderPDF('${data.pdf_url}', '${data.order_id}')" 
                                        class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fas fa-download mr-2"></i>
                                    Descargar PDF
                                </button>
                            </div>
                        `;
                    }

                    showOrderFormMessage(successMessage, 'success');

                    // Cambiar el texto del botón a "Orden Creada" y mantenerlo deshabilitado
                    document.getElementById('submitOrderText').innerHTML =
                        '<i class="fas fa-check mr-1"></i>Orden Creada';

                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                    // No recargar automáticamente para permitir usar los botones de PDF
                    // El usuario puede cerrar el modal manualmente cuando termine
                } else {
                    // En caso de error, habilitar el formulario nuevamente
                    enableOrderForm();
                    
                    if (data.errors) {
                        displayOrderFormErrors(data.errors);
                    } else {
                        showOrderFormMessage(data.message, 'error');
                    }
                    
                    // Restaurar el texto del botón
                    document.getElementById('submitOrderText').textContent =
                        document.getElementById('orderFormAction').value === 'create' ? 'Crear Orden' : 'Actualizar Orden';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // En caso de error de red, habilitar el formulario nuevamente
                enableOrderForm();
                
                showOrderFormMessage('Error al procesar la solicitud', 'error');
                
                // Restaurar el texto del botón
                document.getElementById('submitOrderText').textContent =
                    document.getElementById('orderFormAction').value === 'create' ? 'Crear Orden' : 'Actualizar Orden';
            });
    }

    // Función para mostrar detalles de la orden
    function displayOrderDetails(order) {
        const statusColors = {
            'PENDING': 'bg-yellow-100 text-yellow-800',
            'COMPLETED': 'bg-green-100 text-green-800',
            'CANCELLED': 'bg-red-100 text-red-800'
        };

        const paymentStatusColors = {
            'PENDING': 'bg-yellow-100 text-yellow-800',
            'PAID': 'bg-green-100 text-green-800',
            'FAILED': 'bg-red-100 text-red-800'
        };

        const itemsHtml = order.items.map(item => `
            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                <div>
                    <p class="font-medium">${item.product_name}</p>
                    <p class="text-sm text-gray-500">${item.product_description || ''}</p>
                    <p class="text-sm text-gray-500">Talla: ${item.product_size}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">${item.quantity} x S/ ${parseFloat(item.price).toFixed(2)}</p>
                    <p class="text-sm text-gray-500">S/ ${(item.quantity * parseFloat(item.price)).toFixed(2)}</p>
                </div>
            </div>
        `).join('');

        document.getElementById('orderDetails').innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Información General -->
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Información General
                        </h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">ID:</span> #${order.id}</p>
                            <p><span class="font-medium">Estado:</span> 
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusColors[order.status] || 'bg-gray-100 text-gray-800'}">
                                    ${order.status}
                                </span>
                            </p>
                            <p><span class="font-medium">Total:</span> S/ ${parseFloat(order.total_price).toFixed(2)}</p>
                            <p><span class="font-medium">Descuento:</span> S/ ${parseFloat(order.discount_amount || 0).toFixed(2)}</p>
                            <p><span class="font-medium">Fecha:</span> ${new Date(order.created_at).toLocaleString()}</p>
                            <p><span class="font-medium">Tiempo:</span> ${order.time_ago}</p>
                        </div>
                    </div>
                    
                    <!-- Usuario Creador -->
                    ${order.created_by_username ? `
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-user-cog text-indigo-600 mr-2"></i>
                            Creado por
                        </h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Usuario:</span> ${order.created_by_username}</p>
                            <p><span class="font-medium">Email:</span> ${order.created_by_email || 'N/A'}</p>
                            <p><span class="font-medium">Fecha de creación:</span> ${new Date(order.created_at).toLocaleString()}</p>
                        </div>
                    </div>
                    ` : ''}
                    
                    <!-- Información del Cliente -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-user text-green-600 mr-2"></i>
                            Cliente
                        </h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Nombre:</span> ${order.client_name}</p>
                            <p><span class="font-medium">Email:</span> ${order.client_email}</p>
                            <p><span class="font-medium">Teléfono:</span> ${order.client_phone || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <!-- Dirección de Entrega -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>
                            Dirección de Entrega
                        </h3>
                        <div class="space-y-2">
                            <p>${order.delivery_address}</p>
                            <p>${order.delivery_city}${order.delivery_region ? ', ' + order.delivery_region : ''}</p>
                            <p>${order.delivery_postal_code || ''}</p>
                            <p><span class="font-medium">Teléfono:</span> ${order.delivery_phone || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Información de Productos y Pago -->
                <div class="space-y-4">
                    <!-- Productos -->
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-shopping-bag text-orange-600 mr-2"></i>
                            Productos
                        </h3>
                        <div class="space-y-2">
                            ${itemsHtml}
                        </div>
                    </div>
                    
                    <!-- Información de Pago -->
                    <div class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-credit-card text-teal-600 mr-2"></i>
                            Información de Pago
                        </h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Método:</span> ${order.payment?.method || 'N/A'}</p>
                            <p><span class="font-medium">Estado:</span> 
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${paymentStatusColors[order.payment?.status] || 'bg-gray-100 text-gray-800'}">
                                    ${order.payment?.status || 'PENDING'}
                                </span>
                            </p>
                            <p><span class="font-medium">Fecha de Pago:</span> ${order.payment?.paid_at ? new Date(order.payment.paid_at).toLocaleString() : 'N/A'}</p>
                            ${order.payment?.proof_url ? `<p><span class="font-medium">Comprobante:</span> <a href="${order.payment.proof_url}" target="_blank" class="text-blue-600 hover:underline">Ver comprobante</a></p>` : ''}
                        </div>
                    </div>
                    
                    <!-- Cupón -->
                    ${order.coupon_code ? `
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-ticket-alt text-yellow-600 mr-2"></i>
                            Cupón Aplicado
                        </h3>
                        <div class="space-y-2">
                            <p><span class="font-medium">Código:</span> ${order.coupon_code}</p>
                            <p><span class="font-medium">Descuento:</span> ${order.coupon_value}${order.coupon_type === 'PERCENTAGE' ? '%' : ' S/'}</p>
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    // Función para validar formulario de orden
    function validateOrderForm() {
        let isValid = true;
        clearOrderFormErrors();

        const clientId = document.getElementById('client_id').value;
        const addressId = document.getElementById('address_id').value;

        // Validar cliente
        if (!clientId) {
            showFieldError('client_id', 'Debe seleccionar un cliente');
            isValid = false;
        }

        // Si es nuevo cliente, validar sus campos obligatorios
        if (clientId === '__new__') {
            const requiredNewClientFields = ['new_client_name', 'new_client_email', 'search_dni'];
            requiredNewClientFields.forEach(field => {
                const el = document.getElementById(field);
                if (!el || !el.value.trim()) {
                    showFieldError(field, 'Este campo es requerido');
                    isValid = false;
                }
            });

            // Validar formato de email
            const email = document.getElementById('new_client_email').value;
            if (email && !email.includes('@')) {
                showFieldError('new_client_email', 'Formato de email inválido');
                isValid = false;
            }

            // Validar DNI
            const dni = document.getElementById('search_dni').value;
            if (dni && (dni.length !== 8 || isNaN(dni))) {
                showFieldError('search_dni', 'DNI debe tener 8 dígitos');
                isValid = false;
            }
        }

        // Validar dirección
        if (!addressId) {
            showFieldError('address_id', 'Debe seleccionar una dirección');
            isValid = false;
        }

        // Si es nueva dirección, validar sus campos obligatorios
        if (addressId === '__new__') {
            const requiredNewAddressFields = ['new_address', 'new_city', 'new_region'];
            requiredNewAddressFields.forEach(field => {
                const el = document.getElementById(field);
                if (!el || !el.value.trim()) {
                    showFieldError(field, 'Este campo es requerido');
                    isValid = false;
                }
            });
        }

        // Validar productos
        if (orderProducts.length === 0) {
            showFieldError('products', 'Debe agregar al menos un producto');
            isValid = false;
        }

        // Validar total
        const total = parseFloat(document.getElementById('total_price').value);
        if (isNaN(total) || total <= 0) {
            showFieldError('total_price', 'Agregue Productos');
            isValid = false;
        }

        return isValid;
    }



    // Función para mostrar error en campo específico
    function showFieldError(fieldId, message) {
        const errorElement = document.getElementById(fieldId + '-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }

        const field = document.getElementById(fieldId);
        if (field) {
            field.classList.add('border-red-500');
        }
    }

    // Función para limpiar errores del formulario
    function clearOrderFormErrors() {
        const errorElements = document.querySelectorAll('[id$="-error"]');
        errorElements.forEach(element => {
            element.classList.add('hidden');
        });

        const formControls = document.querySelectorAll('input, select, textarea');
        formControls.forEach(control => {
            control.classList.remove('border-red-500');
        });

        const formMessage = document.getElementById('orderFormMessage');
        if (formMessage) {
            formMessage.classList.add('hidden');
        }
    }

    // Función para mostrar errores específicos del formulario
    function displayOrderFormErrors(errors) {
        Object.keys(errors).forEach(field => {
            showFieldError(field, errors[field]);
        });
    }

    // Función para mostrar mensajes en el formulario
    function showOrderFormMessage(message, type) {
        const messageDiv = document.getElementById('orderFormMessage');
        let className = 'p-3 rounded-md text-sm ';

        switch (type) {
            case 'success':
                className += 'bg-green-50 border border-green-200 text-green-700';
                break;
            case 'error':
                className += 'bg-red-50 border border-red-200 text-red-700';
                break;
            case 'warning':
                className += 'bg-yellow-50 border border-yellow-200 text-yellow-700';
                break;
            default:
                className += 'bg-blue-50 border border-blue-200 text-blue-700';
        }

        if (messageDiv) {
            messageDiv.className = className;
            messageDiv.innerHTML = message; // Cambiado de textContent a innerHTML para permitir HTML
            messageDiv.classList.remove('hidden');
        }
    }

    // Función para descargar PDF de orden
    function downloadOrderPDF(pdfUrl, orderId) {
        // Crear un enlace temporal para forzar descarga
        const link = document.createElement('a');
        link.href = pdfUrl + '&download=1';
        link.download = `orden_${orderId}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Cualquier inicialización adicional
    });
</script>