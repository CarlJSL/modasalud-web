<!-- Modal para Crear/Editar Producto -->
<div id="productModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-box text-green-500 mr-2"></i>
                <span id="modalTitle">Nuevo Producto</span>
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('productModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <!-- Mensaje del formulario -->
            <div id="formMessage" class="hidden mb-4"></div>

            <form id="productForm" class="space-y-4">
                <input type="hidden" id="productId" name="id">
                <input type="hidden" id="formAction" name="action" value="create">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div class="form-group md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag text-gray-400 mr-1"></i>
                            Nombre del Producto
                        </label>
                        <input type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            id="name"
                            name="name"
                            required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="name-error"></div>
                    </div>

                    <!-- Descripción -->
                    <div class="form-group md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left text-gray-400 mr-1"></i>
                            Descripción
                        </label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            id="description"
                            name="description"
                            rows="3"></textarea>
                        <div class="text-red-600 text-xs mt-1 hidden" id="description-error"></div>
                    </div>

                    <!-- Precio -->
                    <div class="form-group">
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign text-gray-400 mr-1"></i>
                            Precio
                        </label>
                        <input type="number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            id="price"
                            name="price"
                            step="0.01"
                            min="0"
                            required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="price-error"></div>
                    </div>

                    <!-- Stock -->
                    <div class="form-group">
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-warehouse text-gray-400 mr-1"></i>
                            Stock
                        </label>
                        <input type="number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            id="stock"
                            name="stock"
                            min="0"
                            required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="stock-error"></div>
                    </div>

                    <!-- Categoría -->
                    <div class="form-group">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list text-gray-400 mr-1"></i>
                            Categoría
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            id="category"
                            name="category"
                            required>
                            <option value="">Seleccionar categoría...</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-red-600 text-xs mt-1 hidden" id="category-error"></div>
                    </div>

                    <!-- Tipo de Producto -->
                    <div class="form-group">
                        <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags text-gray-400 mr-1"></i>
                            Tipo de Producto
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            id="subcategory"
                            name="subcategory">
                            <option value="">Seleccionar SubCategoría...</option>
                            <?php foreach ($subCategories as $type): ?>
                                <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-red-600 text-xs mt-1 hidden" id="subcategory-error"></div>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-toggle-on text-gray-400 mr-1"></i>
                            Estado
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            id="status"
                            name="status"
                            required>
                            <option value="ACTIVE">Activo</option>
                            <option value="DISCONTINUED">Descontinuado</option>
                            <option value="COMING_SOON">Próximamente</option>
                            <option value="ON_SALE">En oferta</option>
                        </select>
                        <div class="text-red-600 text-xs mt-1 hidden" id="status-error"></div>
                    </div>

                    <!-- Talla -->
                    <div class="form-group">
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-ruler text-gray-400 mr-1"></i>
                            Talla
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            id="size"
                            name="size"
                            required>
                            <option value="">Seleccionar talla...</option>
                            <?php foreach ($sizes as $size): ?>
                                <option value="<?= htmlspecialchars($size['name']) ?>"><?= htmlspecialchars($size['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-red-600 text-xs mt-1 hidden" id="size-error"></div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        onclick="closeModal('productModal')">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </button>
                    <button type="button"
                        id="submitProductBtn"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        onclick="submitProduct()">
                        <i class="fas fa-save mr-1"></i>
                        <span id="submitProductText">Crear Producto</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar producto -->


<!-- Modal para eliminar producto -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn modal-content">
        <div id="deleteModalHeader" class="bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 id="deleteModalTitle" class="text-lg font-semibold text-gray-800 flex items-center">
                <i id="deleteModalIcon" class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Confirmar Eliminación
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('deleteModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="text-center">
                <div id="deleteModalCircle" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i id="deleteModalCircleIcon" class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 id="deleteModalQuestion" class="text-lg font-medium text-gray-900 mb-2">¿Estás seguro?</h3>
                <p id="deleteModalText" class="text-sm text-gray-500 mb-4">
                    Esta acción desactivará al producto <strong id="deleteProductName"></strong>.
                    El producto no podrá aparecer en una nueva orden, pero sus datos se mantendrán.
                </p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-yellow-400 mr-2 mt-0.5"></i>
                        <div id="deleteModalNote" class="text-sm text-yellow-800">
                            <p><strong>Nota:</strong> Esta es una eliminación lógica. El producto puede ser reactivado posteriormente.</p>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="deleteProductId">
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="closeModal('deleteModal')">
                <i class="fas fa-times mr-1"></i>
                Cancelar
            </button>
            <button
                type="button"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                onclick="confirmDeleteFromModal()"
                id="confirmDeleteBtn">
                <i class="fas fa-trash mr-1"></i>
                Sí, Desactivar
            </button>
        </div>
    </div>
</div>


<!-- Modal para ver detalles del producto -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[95vh] overflow-y-auto animate-fadeIn modal-content">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border-b border-gray-200 px-6 py-5 flex justify-between items-center rounded-t-xl">
            <h5 class="text-xl font-bold text-gray-800 flex items-center">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-2 rounded-lg mr-3">
                    <i class="fas fa-box-open text-white text-lg"></i>
                </div>
                Detalles del Producto
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-all duration-200" onclick="closeModal('detailModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Contenido dinámico -->
        <div class="p-6">
            <div id="productDetails">
                <!-- Carga inicial de espera -->
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full mb-4">
                        <i class="fas fa-spinner fa-spin text-2xl text-indigo-600"></i>
                    </div>
                    <p class="text-gray-600 text-lg">Cargando detalles del producto...</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-200 rounded-b-xl">
            <button type="button" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200" onclick="closeModal('detailModal')">
                <i class="fas fa-times mr-2"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal para Gestión de Imágenes -->
<div id="imageManagerModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-images text-blue-600 mr-2"></i>
                Gestión de Imágenes del Producto
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('imageManagerModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="imageManagerContent">
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Cargando imágenes...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Funciones JavaScript para manejar los modales de productos con Tailwind CSS

    // Función para mostrar modal
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Remover la clase hidden y establecer display flex
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';

            // Forzar el fondo transparente
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.15)';

            // Agregar animación al contenido del modal
            setTimeout(() => {
                const content = modal.querySelector('.animate-fadeIn');
                if (content) {
                    content.style.opacity = '1';
                    content.style.transform = 'scale(1)';
                }
            }, 10);

            // Enfocar el primer input si existe
            setTimeout(() => {
                const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, select');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 300);
        }
    }

    // Función para cerrar modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Si es el modal de gestión de imágenes y hubo cambios, actualizar la tabla
            if (modalId === 'imageManagerModal' && window.imageManagerChanged) {
                // Mostrar mensaje usando el mismo sistema que los otros modales
                const messageDiv = document.createElement('div');
                messageDiv.className = 'fixed top-4 right-4 z-50 p-3 rounded-md text-sm bg-blue-50 text-blue-800 border border-blue-200 shadow-lg';
                messageDiv.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Actualizando tabla de productos...';
                document.body.appendChild(messageDiv);
                
                // Recargar la página para actualizar la tabla con las nuevas imágenes
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                
                // Resetear flag
                window.imageManagerChanged = false;
            }
            
            modal.classList.add('hidden');
            modal.style.display = 'none';

            // Limpiar cualquier estado del formulario si es necesario
            if (modalId === 'productModal') {
                clearFormErrors();
            }

            // Resetear estilos inline
            modal.style.backgroundColor = '';
            modal.style.alignItems = '';
            modal.style.justifyContent = '';
        }
    }

    // Cerrar modal al hacer clic en el fondo
    document.addEventListener('click', function(e) {
        // Verificar si se hizo clic en el overlay del modal (no en el contenido)
        if (e.target.id === 'productModal' || e.target.id === 'editModal' || e.target.id === 'deleteModal' || e.target.id === 'detailModal' || e.target.id === 'imageManagerModal') {
            closeModal(e.target.id);
        }
    });

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = ['productModal', 'editModal', 'deleteModal', 'detailModal', 'imageManagerModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }
    });

    // Función para abrir modal de crear producto
    function openCreateModal() {
        // Limpiar datos originales
        originalProductData = {};

        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('formAction').value = 'create';
        document.getElementById('modalTitle').textContent = 'Nuevo Producto';
        document.getElementById('submitProductText').textContent = 'Crear Producto';
        clearFormErrors();

        showModal('productModal');

        // Actualizar estado del botón
        setTimeout(updateSubmitButtonState, 100);
    }

    // Variable global para guardar los datos originales del producto
    let originalProductData = {};



    // Función para abrir modal de editar producto
    function openEditModal(productId) {
        // Limpiar formulario
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = productId;
        document.getElementById('formAction').value = 'update';
        document.getElementById('modalTitle').textContent = 'Editar Producto';
        document.getElementById('submitProductText').textContent = 'Actualizar Producto';
        // document.getElementById('editForm').reset();
        //  document.getElementById('editId').value = productId;
        clearFormErrors();

        // Cargar datos del producto
        fetch(`productos.php?action=get&id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const product = data.product;

                    // Guardar datos originales para comparación posterior
                    originalProductData = {
                        name: String(product.name || ''),
                        description: String(product.description || ''),
                        price: String(product.price || ''),
                        stock: String(product.stock || ''),
                        category: String(product.category || ''),
                        size: String(product.size || ''),
                        status: String(product.status || ''),
                        subcategory: String(product.subcategory || '')
                    };

                    console.log('Datos originales guardados:', originalProductData);
                    // Llenar el formulario
                    document.getElementById('name').value = product.name;
                    document.getElementById('description').value = product.description || '';
                    document.getElementById('price').value = product.price;
                    document.getElementById('stock').value = product.stock;
                    document.getElementById('category').value = product.category || '';
                    document.getElementById('size').value = product.size || '';
                    document.getElementById('subcategory').value = product.subcategory || '';

                    const statusField = document.getElementById('status');
                    const currentStatus = product.status || '';

                    if (currentStatus === 'OUT_OF_STOCK') {
                        // Verifica que no exista ya una opción "OUT_OF_STOCK" para evitar duplicados
                        if (!Array.from(statusField.options).some(opt => opt.value === 'OUT_OF_STOCK')) {
                            const option = document.createElement('option');
                            option.value = 'OUT_OF_STOCK';
                            option.textContent = 'Agotado';
                            statusField.appendChild(option);
                        }

                        statusField.value = 'OUT_OF_STOCK';
                        statusField.disabled = true; // Desactiva la edición
                    } else {
                        statusField.value = currentStatus;
                        statusField.disabled = false; // Por si fue desactivado antes
                    }

                    // Actualizar estado del botón después de cargar datos
                    setTimeout(updateSubmitButtonState, 100);


                } else {
                    showFormMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFormMessage('Error al cargar los datos del producto', 'error');
            });

        showModal('productModal');
    }



    // Función para actualizar el estado del botón según los cambios
    function updateSubmitButtonState() {
        const submitButton = document.querySelector('#productModal button[onclick="submitProduct()"]');
        const submitButtonText = document.getElementById('submitProductText');

        if (!submitButton || !submitButtonText) {
            return; // Si no se encuentran los elementos, salir
        }

        const action = document.getElementById('formAction').value;

        if (action === 'update') {
            const hasChanges = hasFormChanges();
            console.log('Estado del botón - Tiene cambios:', hasChanges);

            if (hasChanges) {
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                submitButton.disabled = false;
                submitButtonText.textContent = 'Actualizar Producto';
                submitButton.title = 'Hacer clic para actualizar el producto';
            } else {
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.disabled = false; // Mantenemos habilitado para mostrar el mensaje
                submitButtonText.textContent = 'Sin cambios';
                submitButton.title = 'No hay cambios que guardar';
            }
        } else {
            // Para crear siempre está habilitado
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            submitButton.disabled = false;
            submitButtonText.textContent = 'Crear Producto';
            submitButton.title = 'Hacer clic para crear el producto';
        }
    }

    // Agregar event listeners para detectar cambios en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const formFields = ['name', 'description', 'price', 'stock', 'category', 'size', 'status', 'subcategory'];

        formFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', updateSubmitButtonState);
                field.addEventListener('change', updateSubmitButtonState);
            }
        });

    });

    // Función para abrir modal de eliminar producto
    function openDeleteModal(productId, productName, statusValue) {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const modalTitle = document.getElementById('deleteModalTitle');
        const modalIcon = document.getElementById('deleteModalIcon');
        const modalHeader = document.getElementById('deleteModalHeader');
        const modalCircle = document.getElementById('deleteModalCircle');
        const modalCircleIcon = document.getElementById('deleteModalCircleIcon');
        const modalText = document.getElementById('deleteModalText');
        const modalQuestion = document.getElementById('deleteModalQuestion');
        const modalNote = document.getElementById('deleteModalNote');

        // Rellenar datos
        document.getElementById('deleteProductId').value = productId;
        document.getElementById('deleteProductName').textContent = productName;
        confirmBtn.dataset.status = statusValue;

        const isActivating = parseInt(statusValue) === 1;

        // Cambiar textos e iconos del modal según la acción
        if (isActivating) {
            modalTitle.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-2"></i> Confirmar Activación';
            modalHeader.className = 'bg-gradient-to-r from-green-50 to-lime-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center';
            modalCircle.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4';
            modalCircleIcon.className = 'fas fa-check-circle text-green-600 text-xl';
            modalQuestion.textContent = '¿Deseas activar este producto?';
            modalText.innerHTML = `Esta acción activará al producto <strong id="deleteProductName">${productName}</strong>. Estará disponible para nuevas órdenes.`;
            modalNote.innerHTML = `<p><strong>Nota:</strong> Puedes desactivarlo nuevamente más adelante si es necesario.</p>`;
            confirmBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Sí, Activar';
            confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
        } else {
            modalTitle.innerHTML = '<i class="fas fa-exclamation-triangle text-red-500 mr-2"></i> Confirmar Eliminación';
            modalHeader.className = 'bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center';
            modalCircle.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4';
            modalCircleIcon.className = 'fas fa-exclamation-triangle text-red-600 text-xl';
            modalQuestion.textContent = '¿Estás seguro?';
            modalText.innerHTML = `Esta acción desactivará al producto <strong id="deleteProductName">${productName}</strong>. El producto no podrá aparecer en nuevas órdenes.`;
            modalNote.innerHTML = `<p><strong>Nota:</strong> Esta es una eliminación lógica. El producto puede ser reactivado posteriormente.</p>`;
            confirmBtn.innerHTML = '<i class="fas fa-trash mr-1"></i> Sí, Desactivar';
            confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';
        }

        // Mostrar modal
        showModal('deleteModal');
    }

    // Función para abrir modal de detalles del producto
    function openDetailModal(productId) {
        showModal('detailModal');

        // Cargar detalles del producto
        fetch(`productos.php?action=details&id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayProductDetails(data.product);
                } else {
                    document.getElementById('productDetails').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-400 mb-4"></i>
                        <p class="text-red-500">${data.message}</p>
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('productDetails').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-400 mb-4"></i>
                    <p class="text-red-500">Error al cargar los detalles del producto</p>
                </div>
            `;
            });
    }

    // Función para mostrar detalles del producto
    function displayProductDetails(product) {
        const images = product.images?.length ?
            product.images.map(url => `
                <div class="relative group">
                    <img src="../../${url}" alt="Imagen del producto" class="w-full h-48 object-cover rounded-lg border border-gray-200 hover:border-indigo-300 transition-all duration-300 transform hover:scale-105" />
                </div>
            `).join('') :
            `<div class="text-center py-12 text-gray-400 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                <i class="fas fa-image text-4xl mb-3"></i>
                <p class="text-lg">Sin imágenes disponibles</p>
            </div>`;

        const ratingStars = product.average_rating ?
            `<div class="flex items-center">
                <div class="flex text-yellow-400 mr-2">
                    ${'★'.repeat(Math.round(product.average_rating))}${'☆'.repeat(5 - Math.round(product.average_rating))}
                </div>
                <span class="text-gray-600">(${parseFloat(product.average_rating).toFixed(1)})</span>
            </div>` :
            `<div class="flex items-center text-gray-400">
                <i class="fas fa-star-half-alt mr-1"></i>
                Sin calificaciones
            </div>`;

        const stockStatus = product.stock > 0 ?
            `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i>
                Disponible
            </span>` :
            `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                <i class="fas fa-times-circle mr-1"></i>
                Sin stock
            </span>`;

        const statusColor = {
            'ACTIVE': 'bg-green-100 text-green-800',
            'DISCONTINUED': 'bg-red-100 text-red-800',
            'COMING_SOON': 'bg-blue-100 text-blue-800',
            'ON_SALE': 'bg-purple-100 text-purple-800'
        };

        const statusIcon = {
            'ACTIVE': 'fas fa-check-circle',
            'DISCONTINUED': 'fas fa-times-circle',
            'COMING_SOON': 'fas fa-clock',
            'ON_SALE': 'fas fa-tag'
        };

        document.getElementById('productDetails').innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna izquierda: Información principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Título y descripción -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-xl border border-indigo-100">
                        <h3 class="text-3xl font-bold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-box text-indigo-600 mr-3"></i>
                            ${product.name}
                        </h3>
                        <p class="text-gray-600 text-lg leading-relaxed">${product.description || 'Sin descripción disponible.'}</p>
                    </div>

                    <!-- Información detallada -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h4 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                            Información del Producto
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-dollar-sign text-green-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Precio</p>
                                    <p class="font-semibold text-xl text-gray-800">S/ ${parseFloat(product.price).toFixed(2)}</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-boxes text-blue-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Stock</p>
                                    <p class="font-semibold text-lg text-gray-800">${product.stock} unidades</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-ruler text-orange-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Talla</p>
                                    <p class="font-semibold text-lg text-gray-800">${product.size}</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-tag text-purple-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Categoría</p>
                                    <p class="font-semibold text-lg text-gray-800">${product.category_name || 'Sin categoría'}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Imágenes del producto -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h4 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-images text-indigo-600 mr-2"></i>
                            Galería de Imágenes
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            ${images}
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Estadísticas y estado -->
                <div class="space-y-6">
                    <!-- Estado y disponibilidad -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Estado del Producto</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Estado:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusColor[product.status] || 'bg-gray-100 text-gray-800'}">
                                    <i class="${statusIcon[product.status] || 'fas fa-question-circle'} mr-1"></i>
                                    ${product.status}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Disponibilidad:</span>
                                ${stockStatus}
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Tipo:</span>
                                <span class="text-gray-800 font-medium">${product.product_category_name || 'Sin tipo'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Calificaciones y ventas -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Calificación:</span>
                                ${ratingStars}
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Veces vendido:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    ${product.times_ordered || 0}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">En carritos:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-heart mr-1"></i>
                                    ${product.in_carts || 0}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Información Adicional</h4>
                        <div class="text-sm text-gray-600 space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>
                                <span>Creado ${product.time_ago}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chart-line mr-2 text-gray-500"></i>
                                <span>Popularidad: ${product.times_ordered > 10 ? 'Alta' : product.times_ordered > 5 ? 'Media' : 'Baja'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }


    // Función para detectar si hay cambios en el formulario de edición
    function hasFormChanges() {
        const action = document.getElementById('formAction')?.value || 'create';

        // Si es crear, siempre hay "cambios"
        if (action === 'create') {
            return true;
        }

        // Si no hay datos originales, asumir que sí hay cambios
        if (!originalProductData || Object.keys(originalProductData).length === 0) {
            console.log('No hay datos originales cargados');
            return true;
        }

        // Si es actualizar, comparar con datos originales
        const currentData = {
            name: (document.getElementById('editName')?.value || document.getElementById('name')?.value || '').trim(),
            description: (document.getElementById('editDescription')?.value || document.getElementById('description')?.value || '').trim(),
            price: document.getElementById('editPrice')?.value || document.getElementById('price')?.value || '',
            stock: document.getElementById('editStock')?.value || document.getElementById('stock')?.value || '',
            category: document.getElementById('editCategoryId')?.value || document.getElementById('category')?.value || '',
            size: document.getElementById('editSizeId')?.value || document.getElementById('size')?.value || '',
            subcategory: document.getElementById('editProductTypeId')?.value || document.getElementById('subcategory')?.value || '',
            status: document.getElementById('editStatus')?.value || document.getElementById('status')?.value || ''
        };

        // Comparar cada campo con los datos originales
        const hasDataChanges = (
            String(currentData.name) !== String(originalProductData.name) ||
            String(currentData.description) !== String(originalProductData.description) ||
            String(currentData.price) !== String(originalProductData.price) ||
            String(currentData.stock) !== String(originalProductData.stock) ||
            String(currentData.category) !== String(originalProductData.category) ||
            String(currentData.size) !== String(originalProductData.size) ||
            String(currentData.subcategory) !== String(originalProductData.subcategory) ||
            String(currentData.status) !== String(originalProductData.status)
        );

        // Debug: mostrar comparación en consola
        console.log('Comparación de datos:');
        console.log('Original:', originalProductData);
        console.log('Actual:', currentData);
        console.log('Cambios en datos:', hasDataChanges);

        return hasDataChanges;
    }

    // Función para enviar formulario de producto
    function submitProduct() {
        const form = document.getElementById('productForm');
        const formData = new FormData(form);
        const action = document.getElementById('formAction').value;

        // Validar campos requeridos
        if (!validateForm()) {
            return;
        }

        // Verificar si hay cambios en el formulario (solo para edición)
        if (action === 'update' && !hasFormChanges()) {
            console.log('Frontend: No se detectaron cambios');
            showFormMessage('No se han detectado cambios para guardar. Modifica algún campo para poder actualizar.', 'info');
            return;
        }

        console.log('Frontend: Enviando formulario con cambios detectados');

        // Mostrar loading
        document.getElementById('submitProductText').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...';

        fetch('productos.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFormMessage(data.message, 'success');
                    setTimeout(() => {
                        location.reload(); // Recargar página para mostrar cambios
                    }, 1500);
                } else {
                    // Verificar si es un caso especial de "sin cambios" desde el backend
                    if (data.no_changes) {
                        showFormMessage(data.message, 'info');
                    } else {
                        showFormMessage(data.message, 'error');
                        if (data.errors) {
                            displayFormErrors(data.errors);
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFormMessage('Error al procesar la solicitud', 'error');
            })
            .finally(() => {
                // Restaurar texto del botón
                const action = document.getElementById('formAction').value;
                document.getElementById('submitProductText').textContent = action === 'create' ? 'Crear Producto' : 'Actualizar Producto';
            });
    }

    // Función para enviar formulario de edición
    document.getElementById('editForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'update');

        // Validar campos requeridos
        if (!validateEditForm()) {
            return;
        }

        // Mostrar loading
        const saveBtn = document.getElementById('editSaveBtn');
        const originalText = saveBtn.textContent;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...';

        fetch('productos.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFormMessage(data.message, 'success');
                    setTimeout(() => {
                        location.reload(); // Recargar página para mostrar cambios
                    }, 1500);
                } else {
                    showFormMessage(data.message, 'error');
                    if (data.errors) {
                        displayFormErrors(data.errors);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFormMessage('Error al procesar la solicitud', 'error');
            })
            .finally(() => {
                // Restaurar texto del botón
                saveBtn.textContent = originalText;
            });
    });

    // Función para confirmar eliminación
    function confirmDeleteFromModal() {
        const productId = document.getElementById('deleteProductId').value;
        const statusValue = document.getElementById('confirmDeleteBtn').dataset.status;

        fetch('productos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${productId}&st=${statusValue}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('deleteModal');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cambiar el estado del producto');
            });
    }



    // Función para mostrar mensajes en el formulario
    function showFormMessage(message, type) {
        const messageDiv = document.getElementById('formMessage');
        let className = 'p-3 rounded-md text-sm ';

        switch (type) {
            case 'success':
                className += 'bg-green-50 text-green-800 border border-green-200';
                break;
            case 'error':
                className += 'bg-red-50 text-red-800 border border-red-200';
                break;
            case 'info':
                className += 'bg-blue-50 text-blue-800 border border-blue-200';
                break;
            default:
                className += 'bg-gray-50 text-gray-800 border border-gray-200';
        }

        if (messageDiv) {
            messageDiv.className = className;
            messageDiv.textContent = message;
            messageDiv.classList.remove('hidden');
        }
    }

    // Función para limpiar errores del formulario
    function clearFormErrors() {
        const errorElements = document.querySelectorAll('[id$="-error"]');
        errorElements.forEach(element => {
            element.textContent = '';
            element.classList.add('hidden');
        });

        const formControls = document.querySelectorAll('input, select, textarea');
        formControls.forEach(control => {
            control.classList.remove('border-red-500');
        });

        const formMessage = document.getElementById('formMessage');
        if (formMessage) {
            formMessage.classList.add('hidden');
        }
    }

    // Función para mostrar errores específicos del formulario
    function displayFormErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = document.getElementById(field);
            const errorDiv = document.getElementById(field + '-error');

            if (input && errorDiv) {
                input.classList.add('border-red-500');
                errorDiv.textContent = errors[field];
                errorDiv.classList.remove('hidden');
            }
        });
    }

    // Función para validar formulario de creación
    function validateForm() {
        let isValid = true;
        clearFormErrors();

        const requiredFields = ['name', 'price', 'stock', 'category'];

        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input || !input.value.trim()) {
                if (input) {
                    input.classList.add('border-red-500');
                    const errorDiv = document.getElementById(field + '-error');
                    if (errorDiv) {
                        errorDiv.textContent = 'Este campo es requerido';
                        errorDiv.classList.remove('hidden');
                    }
                }
                isValid = false;
            }
        });

        // Validar precio
        const price = document.getElementById('price')?.value;
        if (price && (isNaN(price) || parseFloat(price) < 0)) {
            const priceInput = document.getElementById('price');
            priceInput.classList.add('border-red-500');
            const errorDiv = document.getElementById('price-error');
            if (errorDiv) {
                errorDiv.textContent = 'El precio debe ser un número válido mayor o igual a 0';
                errorDiv.classList.remove('hidden');
            }
            isValid = false;
        }

        // Validar stock
        const stock = document.getElementById('stock')?.value;
        if (stock && (isNaN(stock) || parseInt(stock) < 0)) {
            const stockInput = document.getElementById('stock');
            stockInput.classList.add('border-red-500');
            const errorDiv = document.getElementById('stock-error');
            if (errorDiv) {
                errorDiv.textContent = 'El stock debe ser un número entero mayor o igual a 0';
                errorDiv.classList.remove('hidden');
            }
            isValid = false;
        }

        return isValid;
    }

    // Función para validar formulario de edición
    function validateEditForm() {
        let isValid = true;

        const requiredFields = [{
                id: 'editName',
                name: 'Nombre'
            },
            {
                id: 'editPrice',
                name: 'Precio'
            },
            {
                id: 'editStock',
                name: 'Stock'
            }
        ];

        requiredFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input || !input.value.trim()) {
                if (input) {
                    input.classList.add('border-red-500');
                    alert(`El campo ${field.name} es requerido`);
                }
                isValid = false;
            }
        });

        // Validar precio
        const price = document.getElementById('editPrice')?.value;
        if (price && (isNaN(price) || parseFloat(price) < 0)) {
            const priceInput = document.getElementById('editPrice');
            priceInput.classList.add('border-red-500');
            alert('El precio debe ser un número válido mayor o igual a 0');
            isValid = false;
        }

        // Validar stock
        const stock = document.getElementById('editStock')?.value;
        if (stock && (isNaN(stock) || parseInt(stock) < 0)) {
            const stockInput = document.getElementById('editStock');
            stockInput.classList.add('border-red-500');
            alert('El stock debe ser un número entero mayor o igual a 0');
            isValid = false;
        }

        return isValid;
    }

    // ==========================================
    // SISTEMA DE NOTIFICACIONES (Compatible con image manager)
    // ==========================================

    function showFormMessage(message, type) {
        const messageDiv = document.getElementById('formMessage');
        let className = 'p-3 rounded-md text-sm ';

        switch (type) {
            case 'success':
                className += 'bg-green-50 text-green-800 border border-green-200';
                break;
            case 'error':
                className += 'bg-red-50 text-red-800 border border-red-200';
                break;
            case 'info':
                className += 'bg-blue-50 text-blue-800 border border-blue-200';
                break;
            default:
                className += 'bg-gray-50 text-gray-800 border border-gray-200';
        }

        if (messageDiv) {
            messageDiv.className = className;
            messageDiv.textContent = message;
            messageDiv.classList.remove('hidden');
        }
    }

    // Inicializar flag para cambios en el gestor de imágenes
    window.imageManagerChanged = false;
</script>