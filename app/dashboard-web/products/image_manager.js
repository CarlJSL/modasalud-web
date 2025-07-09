// ==========================================
// GESTIÓN DE IMÁGENES
// ==========================================

function openImageManagerModal(productId) {
    // Inicializar flag de cambios
    window.imageManagerChanged = false;
    
    showModal('imageManagerModal');
    loadProductImages(productId);
}

function loadProductImages(productId) {
    const content = document.getElementById('imageManagerContent');
    content.innerHTML = `
        <div class="text-center text-gray-500 py-8">
            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
            <p>Cargando imágenes...</p>
        </div>
    `;

    fetch(`product_images.php?action=get_images&product_id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayImageManager(productId, data.images);
            } else {
                content.innerHTML = `
                    <div class="text-center text-red-500 py-8">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Error al cargar imágenes: ${data.message}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Error de conexión</p>
                </div>
            `;
        });
}

function displayImageManager(productId, images) {
    const content = document.getElementById('imageManagerContent');
    
    let imagesHtml = '';
    if (images.length > 0) {
        imagesHtml = images.map((image, index) => `
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow" data-image-id="${image.id}">
                <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                    <img src="../../${image.image_url}" 
                         alt="Imagen del producto" 
                         class="w-full h-48 object-cover">
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Imagen ${index + 1}</p>
                            <p class="text-xs text-gray-400">${new Date(image.created_at).toLocaleDateString()}</p>
                            ${index === 0 ? '<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full mt-1">Principal</span>' : ''}
                        </div>
                        <button onclick="deleteProductImage(${image.id}, ${productId})" 
                                class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50 transition-colors"
                                title="Eliminar imagen">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    } else {
        imagesHtml = `
            <div class="text-center text-gray-500 py-12">
                <i class="fas fa-image text-gray-300 text-4xl mb-4"></i>
                <p class="text-lg">No hay imágenes cargadas</p>
                <p class="text-sm">Sube la primera imagen de este producto</p>
            </div>
        `;
    }

    content.innerHTML = `
        <!-- Upload de Imágenes -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h6 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                <i class="fas fa-cloud-upload-alt text-blue-600 mr-2"></i>
                Subir Nueva Imagen
            </h6>
            <div class="flex items-center space-x-4">
                <input type="file" 
                       id="imageUploadManager" 
                       accept="image/*" 
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:cursor-pointer cursor-pointer">
                <button type="button" 
                        onclick="uploadImageManager(${productId})" 
                        class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200 flex items-center space-x-2 whitespace-nowrap">
                    <i class="fas fa-upload"></i>
                    <span>Subir</span>
                </button>
            </div>
            <p class="text-xs text-gray-600 mt-3 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                <span>Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 5MB. La primera imagen será la principal.</span>
            </p>
        </div>

        <!-- Lista de Imágenes -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-4">
                <h6 class="text-lg font-medium text-gray-800 flex items-center">
                    <i class="fas fa-images text-gray-600 mr-2"></i>
                    Imágenes del Producto
                </h6>
                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                    ${images.length} ${images.length === 1 ? 'imagen' : 'imágenes'}
                </span>
            </div>
            
            ${images.length > 0 ? `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    ${imagesHtml}
                </div>
            ` : `
                <div class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <div class="max-w-sm mx-auto">
                        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-images text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">No hay imágenes</h3>
                        <p class="text-sm text-gray-500 mb-4">Este producto aún no tiene imágenes cargadas</p>
                        <p class="text-xs text-gray-400">Sube la primera imagen usando el formulario de arriba</p>
                    </div>
                </div>
            `}
        </div>
    `;
}

function uploadImageManager(productId) {
    const fileInput = document.getElementById('imageUploadManager');
    const file = fileInput.files[0];

    if (!file) {
        showImageManagerMessage('Por favor selecciona una imagen', 'warning');
        return;
    }

    // Validar tipo de archivo
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type.toLowerCase())) {
        showImageManagerMessage('Tipo de archivo no permitido. Solo se permiten JPG, PNG, GIF y WebP', 'error');
        return;
    }

    // Validar tamaño (5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
        showImageManagerMessage('El archivo es demasiado grande. Máximo 5MB permitido', 'error');
        return;
    }

    // Limpiar mensajes anteriores
    clearImageManagerMessage();

    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('product_id', productId);
    formData.append('image', file);

    // Mostrar loading con animación mejorada
    const content = document.getElementById('imageManagerContent');
    const uploadSection = content.querySelector('.bg-gradient-to-r');
    const originalContent = uploadSection.innerHTML;
    
    uploadSection.innerHTML = `
        <div class="text-center py-8">
            <div class="relative">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600 mx-auto mb-4"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-upload text-blue-600 text-lg"></i>
                </div>
            </div>
            <p class="text-blue-600 font-medium">Subiendo imagen...</p>
            <p class="text-sm text-gray-500 mt-1">Por favor espera</p>
        </div>
    `;

    fetch('product_images.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar la lista de imágenes
            loadProductImages(productId);
            showImageManagerMessage('¡Imagen subida exitosamente!', 'success');
            
            // Marcar que hubo cambios para actualizar la tabla al cerrar
            window.imageManagerChanged = true;
        } else {
            uploadSection.innerHTML = originalContent; // Restaurar formulario
            showImageManagerMessage('Error al subir imagen: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        uploadSection.innerHTML = originalContent; // Restaurar formulario
        showImageManagerMessage('Error de conexión. Inténtalo de nuevo', 'error');
    });
}

function deleteProductImage(imageId, productId) {
    // Modal de confirmación con el mismo estilo que los modales de productos
    showImageConfirmationModal(
        'Confirmar Eliminación',
        'Esta acción eliminará permanentemente la imagen. No se puede deshacer.',
        'Sí, Eliminar',
        'Cancelar',
        () => {
            // Mostrar loading durante eliminación
            const imageCard = document.querySelector(`[data-image-id="${imageId}"]`);
            if (imageCard) {
                imageCard.innerHTML = `
                    <div class="flex items-center justify-center h-48 bg-gray-100 rounded-lg">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-2 border-red-200 border-t-red-600 mx-auto mb-2"></div>
                            <p class="text-sm text-gray-600">Eliminando...</p>
                        </div>
                    </div>
                `;
            }

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('image_id', imageId);

            fetch('product_images.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadProductImages(productId); // Recargar la lista
                    showImageManagerMessage('¡Imagen eliminada exitosamente!', 'success');
                    
                    // Marcar que hubo cambios para actualizar la tabla al cerrar
                    window.imageManagerChanged = true;
                } else {
                    loadProductImages(productId); // Recargar para restaurar
                    showImageManagerMessage('Error al eliminar imagen: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadProductImages(productId); // Recargar para restaurar
                showImageManagerMessage('Error de conexión. Inténtalo de nuevo', 'error');
            });
        }
    );
}

// ==========================================
// SISTEMA DE NOTIFICACIONES (Compatible con modales de productos)
// ==========================================

function showImageManagerMessage(message, type = 'info') {
    // Crear o reutilizar el div de mensajes
    let messageDiv = document.getElementById('imageManagerMessage');
    
    if (!messageDiv) {
        // Crear el div si no existe
        messageDiv = document.createElement('div');
        messageDiv.id = 'imageManagerMessage';
        messageDiv.className = 'hidden mb-4';
        
        // Insertar al inicio del contenido del modal
        const content = document.getElementById('imageManagerContent');
        if (content) {
            content.insertBefore(messageDiv, content.firstChild);
        }
    }

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
        case 'warning':
            className += 'bg-yellow-50 text-yellow-800 border border-yellow-200';
            break;
        default:
            className += 'bg-gray-50 text-gray-800 border border-gray-200';
    }

    messageDiv.className = className;
    messageDiv.textContent = message;
    messageDiv.classList.remove('hidden');

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        if (messageDiv) {
            messageDiv.classList.add('hidden');
        }
    }, 5000);
}

function clearImageManagerMessage() {
    const messageDiv = document.getElementById('imageManagerMessage');
    if (messageDiv) {
        messageDiv.classList.add('hidden');
    }
}

// ==========================================
// MODAL DE CONFIRMACIÓN (Estilo consistente con modales de productos)
// ==========================================

function showImageConfirmationModal(title, message, confirmText = 'Confirmar', cancelText = 'Cancelar', onConfirm = null) {
    // Remover modales de confirmación existentes
    const existingModals = document.querySelectorAll('.image-confirmation-modal');
    existingModals.forEach(modal => modal.remove());

    const modal = document.createElement('div');
    modal.className = 'image-confirmation-modal fixed inset-0 bg-black bg-opacity-15 z-50 flex items-center justify-center p-4 modal-backdrop';
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn">
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    ${title}
                </h5>
                <button onclick="this.closest('.image-confirmation-modal').remove()" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">¿Estás seguro?</h3>
                    <p class="text-sm text-gray-500 mb-4">${message}</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-yellow-400 mr-2 mt-0.5"></i>
                            <div class="text-sm text-yellow-800">
                                <p><strong>Nota:</strong> Esta acción no se puede deshacer.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                <button onclick="this.closest('.image-confirmation-modal').remove()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-times mr-1"></i>
                    ${cancelText}
                </button>
                <button onclick="confirmImageAction()" 
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-trash mr-1"></i>
                    ${confirmText}
                </button>
            </div>
        </div>
    `;

    // Función para confirmar acción
    window.confirmImageAction = function() {
        modal.remove();
        if (onConfirm && typeof onConfirm === 'function') {
            onConfirm();
        }
        // Limpiar función global
        delete window.confirmImageAction;
    };

    document.body.appendChild(modal);

    // Aplicar estilos similares a los otros modales
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.15)';

    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
            delete window.confirmImageAction;
        }
    });

    // Cerrar modal con Escape
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            modal.remove();
            delete window.confirmImageAction;
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);
}

// ==========================================
// COMPATIBILIDAD CON SISTEMA ANTERIOR
// ==========================================

// Mantener compatibilidad con showAdvancedNotification pero usar el nuevo sistema
function showAdvancedNotification(message, type = 'info', icon = 'fas fa-info-circle', duration = 4000) {
    showImageManagerMessage(message, type);
}

// Función legacy para notificaciones básicas
function showNotification(message, type = 'info') {
    showImageManagerMessage(message, type);
}
