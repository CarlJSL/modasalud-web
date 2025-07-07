<!-- Modal para Crear/Editar Categoría o Subcategoría -->
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 id="modalTitle" class="text-lg font-semibold text-gray-800 flex items-center">
                <i id="modalIcon" class="fas fa-folder mr-2 text-green-600"></i>
                Nueva Categoría
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('categoryModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <!-- Mensaje del formulario -->
            <div id="formMessage" class="hidden mb-4"></div>

            <form id="categoryForm" class="space-y-4">
                <input type="hidden" id="categoryId" name="id">
                <input type="hidden" id="formAction" name="action" value="create">
                <input type="hidden" id="formType" name="type" value="category">

                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Ingrese el nombre">
                    <div id="name-error" class="hidden text-red-600 text-sm mt-1"></div>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Descripción opcional"></textarea>
                    <div id="description-error" class="hidden text-red-600 text-sm mt-1"></div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            onclick="closeModal('categoryModal')">
                        Cancelar
                    </button>
                    <button type="button" 
                            onclick="submitCategory()"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <span id="submitCategoryText">Crear Categoría</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para eliminar categoría/subcategoría -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 id="deleteModalTitle" class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
                Confirmar Eliminación
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('deleteModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">¿Está seguro?</h3>
                <p id="deleteModalText" class="text-sm text-gray-500 mb-4">
                    Esta acción eliminará permanentemente la categoría "<span id="deleteCategoryName" class="font-medium"></span>".
                </p>
                <div id="deleteModalNote" class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Nota:</strong> Solo se pueden eliminar categorías que no tengan productos asociados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="deleteCategoryId">
            <input type="hidden" id="deleteCategoryType">
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                    onclick="closeModal('deleteModal')">
                Cancelar
            </button>
            <button type="button" 
                    id="confirmDeleteBtn"
                    onclick="confirmDeleteFromModal()"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <i class="fas fa-trash mr-1"></i>
                Eliminar
            </button>
        </div>
    </div>
</div>

<!-- Modal para ver detalles -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-y-auto animate-fadeIn modal-content">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border-b border-gray-200 px-6 py-5 flex justify-between items-center rounded-t-xl">
            <h5 id="detailModalTitle" class="text-xl font-bold text-gray-800 flex items-center">
                <i id="detailModalIcon" class="fas fa-info-circle mr-2 text-indigo-600"></i>
                Detalles de la Categoría
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-all duration-200" onclick="closeModal('detailModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Contenido dinámico -->
        <div class="p-6">
            <div id="categoryDetails">
                <!-- El contenido se cargará dinámicamente aquí -->
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-200 rounded-b-xl">
            <button type="button" 
                    class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200" 
                    onclick="closeModal('detailModal')">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    // Variables globales para almacenar datos originales
    let originalCategoryData = {};

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
                    content.classList.add('scale-100', 'opacity-100');
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
                content.classList.remove('scale-100', 'opacity-100');
            }
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }, 150);
        }
    }

    // Cerrar modal al hacer clic en el fondo
    document.addEventListener('click', function(e) {
        if (e.target.id === 'categoryModal' || e.target.id === 'deleteModal' || e.target.id === 'detailModal') {
            closeModal(e.target.id);
        }
    });

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('categoryModal');
            closeModal('deleteModal');
            closeModal('detailModal');
        }
    });

    // Función para abrir modal de crear
    function openCreateModal(type = 'category') {
        originalCategoryData = {};
        
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('formAction').value = 'create';
        document.getElementById('formType').value = type;
        
        const isSubcategory = type === 'subcategory';
        document.getElementById('modalTitle').innerHTML = `<i class="fas fa-${isSubcategory ? 'folder-open' : 'folder'} mr-2 text-green-600"></i>${isSubcategory ? 'Nueva Subcategoría' : 'Nueva Categoría'}`;
        document.getElementById('submitCategoryText').textContent = isSubcategory ? 'Crear Subcategoría' : 'Crear Categoría';
        
        clearFormErrors();
        showModal('categoryModal');
    }

    // Función para abrir modal de editar
    function openEditModal(id, type = 'category') {
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = id;
        document.getElementById('formAction').value = 'update';
        document.getElementById('formType').value = type;
        
        const isSubcategory = type === 'subcategory';
        document.getElementById('modalTitle').innerHTML = `<i class="fas fa-${isSubcategory ? 'folder-open' : 'folder'} mr-2 text-green-600"></i>Editar ${isSubcategory ? 'Subcategoría' : 'Categoría'}`;
        document.getElementById('submitCategoryText').textContent = `Actualizar ${isSubcategory ? 'Subcategoría' : 'Categoría'}`;
        
        clearFormErrors();

        // Cargar datos
        fetch(`categories.php?action=get&id=${id}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = data.item;
                    originalCategoryData = { ...item };
                    
                    document.getElementById('name').value = item.name || '';
                    document.getElementById('description').value = item.description || '';
                    
                    updateSubmitButtonState();
                } else {
                    showFormMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFormMessage('Error al cargar los datos', 'error');
            });

        showModal('categoryModal');
    }

    // Función para abrir modal de eliminar
    function openDeleteModal(id, name, type = 'category') {
        const isSubcategory = type === 'subcategory';
        
        document.getElementById('deleteCategoryId').value = id;
        document.getElementById('deleteCategoryName').textContent = name;
        document.getElementById('deleteCategoryType').value = type;
        
        document.getElementById('deleteModalText').innerHTML = `Esta acción eliminará permanentemente la ${isSubcategory ? 'subcategoría' : 'categoría'} "<span class="font-medium">${name}</span>".`;
        document.getElementById('deleteModalNote').innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Nota:</strong> Solo se pueden eliminar ${isSubcategory ? 'subcategorías' : 'categorías'} que no tengan productos asociados.
                    </p>
                </div>
            </div>
        `;
        
        showModal('deleteModal');
    }

    // Función para abrir modal de detalles
    function openDetailModal(id, type = 'category') {
        const isSubcategory = type === 'subcategory';
        
        document.getElementById('detailModalTitle').innerHTML = `<i class="fas fa-info-circle mr-2 text-indigo-600"></i>Detalles de la ${isSubcategory ? 'Subcategoría' : 'Categoría'}`;
        
        showModal('detailModal');

        // Cargar detalles
        fetch(`categories.php?action=details&id=${id}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCategoryDetails(data.item, type);
                } else {
                    document.getElementById('categoryDetails').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-red-400 text-3xl mb-2"></i>
                            <p class="text-red-600">${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('categoryDetails').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-red-400 text-3xl mb-2"></i>
                        <p class="text-red-600">Error al cargar los detalles</p>
                    </div>
                `;
            });
    }

    // Función para mostrar detalles
    function displayCategoryDetails(item, type) {
        const isSubcategory = type === 'subcategory';
        
        let subcategoriesHtml = '';
        if (!isSubcategory && item.subcategories && item.subcategories.length > 0) {
            subcategoriesHtml = `
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Subcategorías Asociadas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        ${item.subcategories.map(sub => `
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <div class="flex items-center">
                                    <i class="fas fa-folder-open text-blue-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-800">${sub.name}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        const timeAgoHtml = isSubcategory && item.time_ago ? `
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center">
                    <i class="fas fa-clock text-gray-500 mr-2"></i>
                    <span class="text-sm text-gray-600">Creado ${item.time_ago}</span>
                </div>
            </div>
        ` : '';

        document.getElementById('categoryDetails').innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Información principal -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200 mb-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-${isSubcategory ? 'folder-open' : 'folder'} text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">${item.name}</h3>
                                <p class="text-sm text-gray-600">${isSubcategory ? 'Subcategoría' : 'Categoría'} #${item.id}</p>
                            </div>
                        </div>
                        
                        ${item.description ? `
                            <div class="bg-white rounded-lg p-4 border border-blue-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Descripción</h4>
                                <p class="text-gray-700">${item.description}</p>
                            </div>
                        ` : `
                            <div class="bg-white rounded-lg p-4 border border-blue-200">
                                <p class="text-gray-500 italic">Sin descripción</p>
                            </div>
                        `}
                    </div>
                    
                    ${subcategoriesHtml}
                </div>

                <!-- Estadísticas laterales -->
                <div class="space-y-4">
                    <!-- Estadística de productos -->
                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-900">Productos</h4>
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                        <div class="text-2xl font-bold text-green-600 mb-1">${item.product_count}</div>
                        <p class="text-xs text-gray-500">productos asociados</p>
                        ${item.product_count > 0 ? `
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Activa
                                </span>
                            </div>
                        ` : `
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-circle mr-1"></i>
                                    Sin productos
                                </span>
                            </div>
                        `}
                    </div>

                    ${!isSubcategory ? `
                        <!-- Estadística de subcategorías -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-900">Subcategorías</h4>
                                <i class="fas fa-folder-open text-gray-400"></i>
                            </div>
                            <div class="text-2xl font-bold text-blue-600 mb-1">${item.subcategories_count || 0}</div>
                            <p class="text-xs text-gray-500">subcategorías</p>
                        </div>
                    ` : ''}

                    ${timeAgoHtml}
                </div>
            </div>
        `;
    }

    // Función para detectar cambios en el formulario
    function hasFormChanges() {
        const action = document.getElementById('formAction')?.value || 'create';

        if (action === 'create') {
            const name = document.getElementById('name')?.value?.trim() || '';
            const description = document.getElementById('description')?.value?.trim() || '';
            return name !== '' || description !== '';
        }

        if (!originalCategoryData || Object.keys(originalCategoryData).length === 0) {
            return true;
        }

        const currentData = {
            name: document.getElementById('name')?.value?.trim() || '',
            description: document.getElementById('description')?.value?.trim() || ''
        };

        return (
            currentData.name !== (originalCategoryData.name || '') ||
            currentData.description !== (originalCategoryData.description || '')
        );
    }

    // Función para actualizar el estado del botón
    function updateSubmitButtonState() {
        const submitButton = document.querySelector('#categoryModal button[onclick="submitCategory()"]');
        const submitButtonText = document.getElementById('submitCategoryText');

        if (!submitButton || !submitButtonText) return;

        const action = document.getElementById('formAction').value;

        if (action === 'update') {
            if (hasFormChanges()) {
                submitButton.disabled = false;
                submitButton.className = 'px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
            } else {
                submitButton.disabled = true;
                submitButton.className = 'px-4 py-2 text-sm font-medium text-gray-400 bg-gray-200 border border-transparent rounded-md cursor-not-allowed';
            }
        } else {
            submitButton.disabled = false;
            submitButton.className = 'px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
        }
    }

    // Event listeners para detectar cambios
    document.addEventListener('DOMContentLoaded', function() {
        const formFields = ['name', 'description'];

        formFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', updateSubmitButtonState);
                field.addEventListener('change', updateSubmitButtonState);
            }
        });
    });

    // Función para enviar formulario
    function submitCategory() {
        const form = document.getElementById('categoryForm');
        const formData = new FormData(form);
        const action = document.getElementById('formAction').value;

        // Validar campos requeridos
        if (!validateForm()) {
            return;
        }

        // Verificar cambios para edición
        if (action === 'update' && !hasFormChanges()) {
            showFormMessage('No se han detectado cambios para actualizar', 'warning');
            return;
        }

        // Mostrar loading
        document.getElementById('submitCategoryText').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...';

        fetch('categories.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showFormMessage(data.message, 'success');
                setTimeout(() => {
                    closeModal('categoryModal');
                    location.reload();
                }, 1500);
            } else {
                if (data.errors) {
                    displayFormErrors(data.errors);
                } else {
                    showFormMessage(data.message, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showFormMessage('Error al procesar la solicitud', 'error');
        })
        .finally(() => {
            // Restaurar texto del botón
            const type = document.getElementById('formType').value;
            const isSubcategory = type === 'subcategory';
            const actionText = action === 'create' ? 'Crear' : 'Actualizar';
            document.getElementById('submitCategoryText').textContent = `${actionText} ${isSubcategory ? 'Subcategoría' : 'Categoría'}`;
        });
    }

    // Función para confirmar eliminación
    function confirmDeleteFromModal() {
        const id = document.getElementById('deleteCategoryId').value;
        const type = document.getElementById('deleteCategoryType').value;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        formData.append('type', type);

        fetch('categories.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('deleteModal');
                // Mostrar mensaje de éxito y recargar
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
    }

    // Funciones de utilidad para formularios
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
            case 'warning':
                className += 'bg-yellow-50 text-yellow-800 border border-yellow-200';
                break;
            default:
                className += 'bg-blue-50 text-blue-800 border border-blue-200';
        }

        if (messageDiv) {
            messageDiv.className = className;
            messageDiv.textContent = message;
            messageDiv.classList.remove('hidden');

            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.classList.add('hidden');
                }, 3000);
            }
        }
    }

    function clearFormErrors() {
        const errorElements = document.querySelectorAll('[id$="-error"]');
        errorElements.forEach(element => {
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

    function displayFormErrors(errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(`${field}-error`);
            const inputElement = document.getElementById(field);

            if (errorElement) {
                errorElement.textContent = errors[field];
                errorElement.classList.remove('hidden');
            }

            if (inputElement) {
                inputElement.classList.add('border-red-500');
            }
        });
    }

    function validateForm() {
        let isValid = true;
        clearFormErrors();

        const name = document.getElementById('name')?.value?.trim();
        if (!name) {
            const errorElement = document.getElementById('name-error');
            const inputElement = document.getElementById('name');

            if (errorElement) {
                errorElement.textContent = 'El nombre es requerido';
                errorElement.classList.remove('hidden');
            }

            if (inputElement) {
                inputElement.classList.add('border-red-500');
            }

            isValid = false;
        }

        return isValid;
    }
</script>
