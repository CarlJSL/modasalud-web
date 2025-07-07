<!-- Modal para Crear/Editar Usuario -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                <span id="modalTitle">Nuevo Usuario</span>
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('userModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="userForm" class="space-y-4">
                <input type="hidden" id="userId" name="id">
                <input type="hidden" id="formAction" name="action" value="create">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Username -->
                    <div class="form-group">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-1"></i>
                            Nombre de Usuario
                        </label>
                        <input type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            id="username"
                            name="username"
                            required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="username-error"></div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-1"></i>
                            Email
                        </label>
                        <input type="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            id="email"
                            name="email"
                            required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="email-error"></div>
                    </div>

                    <!-- Nombre completo -->
                    <div class="form-group">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tag text-gray-400 mr-1"></i>
                            Nombre Completo
                        </label>
                        <input type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            id="name"
                            name="name"
                            required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="name-error"></div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-400 mr-1"></i>
                            Contraseña
                            <span class="text-xs text-gray-500 hidden" id="passwordHint">(Dejar en blanco para mantener actual)</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm pr-10"
                                id="password"
                                name="password">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordToggle"></i>
                            </button>
                        </div>
                        <div class="text-red-600 text-xs mt-1 hidden" id="password-error"></div>
                    </div>

                    <!-- Rol -->
                    <div class="form-group">
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-shield text-gray-400 mr-1"></i>
                            Rol
                        </label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            id="role_id"
                            name="role_id"
                            required>
                            <option value="">Seleccionar rol...</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-red-600 text-xs mt-1 hidden" id="role_id-error"></div>
                    </div>

                    <!-- Status -->
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

                <!-- Mensaje de error/éxito -->
                <div id="formMessage" class="hidden p-3 rounded-md text-sm"></div>
            </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="closeModal('userModal')">
                <i class="fas fa-times mr-1"></i>
                Cancelar
            </button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="submitUserForm()">
                <i class="fas fa-save mr-1"></i>
                <span id="submitButtonText">Guardar Usuario</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Usuario -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Confirmar Eliminación
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('deleteModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">¿Estás seguro?</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Esta acción desactivará al usuario <strong id="deleteUserName"></strong>.
                    El usuario no podrá acceder al sistema, pero sus datos se mantendrán.
                </p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-yellow-400 mr-2 mt-0.5"></i>
                        <div class="text-sm text-yellow-800">
                            <p><strong>Nota:</strong> Esta es una eliminación lógica. El usuario puede ser reactivado posteriormente.</p>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="deleteUserId">
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="closeModal('deleteModal')">
                <i class="fas fa-times mr-1"></i>
                Cancelar
            </button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="confirmDelete()">
                <i class="fas fa-trash mr-1"></i>
                Sí, Desactivar
            </button>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalles del Usuario -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                Detalles del Usuario
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('detailModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="userDetails">
                <!-- El contenido se cargará dinámicamente aquí -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Cargando detalles del usuario...</p>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="closeModal('detailModal')">
                <i class="fas fa-times mr-1"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    // Funciones JavaScript para manejar los modales con Tailwind CSS

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
            modal.classList.add('hidden');
            modal.style.display = 'none';

            // Limpiar cualquier estado del formulario si es necesario
            if (modalId === 'userModal') {
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
        if (e.target.id === 'userModal' || e.target.id === 'deleteModal' || e.target.id === 'detailModal') {
            closeModal(e.target.id);
        }
    });

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = ['userModal', 'deleteModal', 'detailModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }
    });

    // Función para alternar visibilidad de contraseña
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordToggle.classList.remove('fa-eye');
            passwordToggle.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordToggle.classList.remove('fa-eye-slash');
            passwordToggle.classList.add('fa-eye');
        }
    }

    // Función para abrir modal de crear usuario
    function openCreateModal() {
        // Limpiar datos originales
        originalUserData = {};

        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('formAction').value = 'create';
        document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
        document.getElementById('submitButtonText').textContent = 'Crear Usuario';
        document.getElementById('passwordHint').classList.add('hidden');
        document.getElementById('password').required = true;
        clearFormErrors();

        showModal('userModal');

        // Actualizar estado del botón
        setTimeout(updateSubmitButtonState, 100);
    }

    // Variable global para guardar los datos originales del usuario
    let originalUserData = {};

    // Función para abrir modal de editar usuario
    function openEditModal(userId) {
        // Limpiar formulario
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = userId;
        document.getElementById('formAction').value = 'update';
        document.getElementById('modalTitle').textContent = 'Editar Usuario';
        document.getElementById('submitButtonText').textContent = 'Actualizar Usuario';
        document.getElementById('passwordHint').classList.remove('hidden');
        document.getElementById('password').required = false;
        clearFormErrors();

        // Cargar datos del usuario
        fetch(`usuarios.php?action=get&id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;

                    // Guardar datos originales para comparación posterior (convertir a string)
                    originalUserData = {
                        username: String(user.username || ''),
                        email: String(user.email || ''),
                        name: String(user.name || ''),
                        role_id: String(user.role_id || ''),
                        status: String(user.status || '')
                    };

                    console.log('Datos originales guardados:', originalUserData);

                    // Llenar el formulario
                    document.getElementById('username').value = user.username;
                    document.getElementById('email').value = user.email;
                    document.getElementById('name').value = user.name;
                    document.getElementById('role_id').value = user.role_id;
                    document.getElementById('status').value = user.status;

                    // Actualizar estado del botón después de cargar datos
                    setTimeout(updateSubmitButtonState, 100);
                } else {
                    showFormMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFormMessage('Error al cargar los datos del usuario', 'error');
            });

        showModal('userModal');
    }

    // Función para actualizar el estado del botón según los cambios
    function updateSubmitButtonState() {
        const action = document.getElementById('formAction').value;
        const submitButton = document.querySelector('#userModal button[onclick="submitUserForm()"]');
        const submitButtonText = document.getElementById('submitButtonText');

        if (!submitButton || !submitButtonText) {
            return; // Si no se encuentran los elementos, salir
        }

        if (action === 'update') {
            const hasChanges = hasFormChanges();
            console.log('Estado del botón - Tiene cambios:', hasChanges);

            if (hasChanges) {
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                submitButton.disabled = false;
                submitButtonText.textContent = 'Actualizar Usuario';
                submitButton.title = 'Hacer clic para actualizar el usuario';
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
            submitButtonText.textContent = 'Crear Usuario';
            submitButton.title = 'Hacer clic para crear el usuario';
        }
    }

    // Agregar event listeners para detectar cambios en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const formFields = ['username', 'email', 'name', 'role_id', 'status', 'password'];

        formFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', updateSubmitButtonState);
                field.addEventListener('change', updateSubmitButtonState);
            }
        });
    });

    // Función para abrir modal de eliminar usuario
    function openDeleteModal(userId, userName) {
        console.log('Opening delete modal for user:', userId, userName); // Debug

        document.getElementById('deleteUserId').value = userId;
        document.getElementById('deleteUserName').textContent = userName;

        showModal('deleteModal');
    }

    // Función para abrir modal de detalles del usuario
    function openDetailModal(userId) {
        showModal('detailModal');

        // Cargar detalles del usuario
        fetch(`usuarios.php?action=details&id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUserDetails(data.user);
                } else {
                    document.getElementById('userDetails').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-400 mb-4"></i>
                        <p class="text-red-500">${data.message}</p>
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('userDetails').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-400 mb-4"></i>
                    <p class="text-red-500">Error al cargar los detalles del usuario</p>
                </div>
            `;
            });
    }

    // Función para mostrar detalles del usuario
    function displayUserDetails(user) {
        const statusBadge = user.status === 'ACTIVE' ?
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-circle text-green-400 mr-1"></i>Activo</span>' :
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-circle text-red-400 mr-1"></i>Inactivo</span>';

        const recentBadge = user.is_recent ?
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-star text-blue-400 mr-1"></i>Usuario Reciente</span>' : '';

        let permissionsHtml = '';
        if (user.permissions && user.permissions.length > 0) {
            permissionsHtml = user.permissions.map(perm => `
            <div class="bg-gray-50 rounded-lg p-3">
                <h4 class="font-medium text-gray-900 mb-2">${perm.table_name}</h4>
                <div class="flex flex-wrap gap-2">
                    ${perm.can_create ? '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Crear</span>' : ''}
                    ${perm.can_read ? '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Leer</span>' : ''}
                    ${perm.can_update ? '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">Actualizar</span>' : ''}
                    ${perm.can_delete ? '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Eliminar</span>' : ''}
                </div>
            </div>
        `).join('');
        } else {
            permissionsHtml = '<p class="text-gray-500 text-sm">No se encontraron permisos específicos para este rol.</p>';
        }

        document.getElementById('userDetails').innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información básica -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nombre de Usuario</label>
                            <p class="mt-1 text-sm text-gray-900">${user.username}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">${user.email}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nombre Completo</label>
                            <p class="mt-1 text-sm text-gray-900">${user.name}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Rol</label>
                            <p class="mt-1 text-sm text-gray-900">${user.role_name}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Estado</label>
                            <p class="mt-1">${statusBadge}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Badges</label>
                            <p class="mt-1">${recentBadge}</p>
                        </div>
                    </div>
                </div>

                <!-- Permisos -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Permisos del Rol</h3>
                    <div class="space-y-3">
                        ${permissionsHtml}
                    </div>
                </div>
            </div>

            <!-- Estadísticas y metadatos -->
            <div class="space-y-6">
                <!-- Estadísticas -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Estadísticas</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Registros creados</span>
                            <span class="text-sm font-medium text-gray-900">${user.statistics.created_records}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Registros actualizados</span>
                            <span class="text-sm font-medium text-gray-900">${user.statistics.updated_records}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Días desde creación</span>
                            <span class="text-sm font-medium text-gray-900">${Math.floor(user.days_since_creation)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Última actualización</span>
                            <span class="text-sm font-medium text-gray-900">${Math.floor(user.days_since_last_update)} días</span>
                        </div>
                    </div>
                </div>

                <!-- Fechas importantes -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Fechas</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Fecha de creación</label>
                            <p class="mt-1 text-sm text-gray-900">${user.formatted_created_at}</p>
                            <p class="text-xs text-gray-500">${user.created_ago}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Última actualización</label>
                            <p class="mt-1 text-sm text-gray-900">${user.formatted_updated_at}</p>
                            <p class="text-xs text-gray-500">${user.updated_ago}</p>
                        </div>
                    </div>
                </div>

                <!-- Metadatos -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Metadatos</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Creado por</label>
                            <p class="mt-1 text-sm text-gray-900">${user.created_by || 'Sistema'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Actualizado por</label>
                            <p class="mt-1 text-sm text-gray-900">${user.updated_by || 'Sistema'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    }

    // Función para detectar si hay cambios en el formulario de edición
    function hasFormChanges() {
        const action = document.getElementById('formAction').value;

        // Si es crear, siempre hay "cambios"
        if (action === 'create') {
            return true;
        }

        // Si no hay datos originales, asumir que sí hay cambios
        if (!originalUserData || Object.keys(originalUserData).length === 0) {
            console.log('No hay datos originales cargados');
            return true;
        }

        // Si es actualizar, comparar con datos originales
        const currentData = {
            username: document.getElementById('username').value.trim(),
            email: document.getElementById('email').value.trim(),
            name: document.getElementById('name').value.trim(),
            role_id: document.getElementById('role_id').value,
            status: document.getElementById('status').value
        };

        // Verificar si la contraseña ha sido modificada
        const password = document.getElementById('password').value;
        const hasPasswordChange = password && password.trim() !== '';

        // Comparar cada campo con los datos originales (conversión a string para comparación)
        const hasDataChanges = (
            String(currentData.username) !== String(originalUserData.username) ||
            String(currentData.email) !== String(originalUserData.email) ||
            String(currentData.name) !== String(originalUserData.name) ||
            String(currentData.role_id) !== String(originalUserData.role_id) ||
            String(currentData.status) !== String(originalUserData.status)
        );

        // Debug: mostrar comparación en consola
        console.log('Comparación de datos:');
        console.log('Original:', originalUserData);
        console.log('Actual:', currentData);
        console.log('Cambios en datos:', hasDataChanges);
        console.log('Cambio de contraseña:', hasPasswordChange);

        return hasDataChanges || hasPasswordChange;
    }

    // Función para enviar formulario de usuario
    function submitUserForm() {
        const form = document.getElementById('userForm');
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
        document.getElementById('submitButtonText').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...';

        fetch('usuarios.php', {
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
                document.getElementById('submitButtonText').textContent = action === 'create' ? 'Crear Usuario' : 'Actualizar Usuario';
            });
    }

    // Función para confirmar eliminación
    function confirmDelete() {
        const userId = document.getElementById('deleteUserId').value;

        fetch('usuarios.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('deleteModal');
                    // Mostrar mensaje de éxito y recargar
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el usuario');
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

        messageDiv.className = className;
        messageDiv.textContent = message;
        messageDiv.classList.remove('hidden');
    }

    // Función para limpiar errores del formulario
    function clearFormErrors() {
        const errorElements = document.querySelectorAll('[id$="-error"]');
        errorElements.forEach(element => {
            element.textContent = '';
            element.classList.add('hidden');
        });

        const formControls = document.querySelectorAll('input, select');
        formControls.forEach(control => {
            control.classList.remove('border-red-500');
        });

        document.getElementById('formMessage').classList.add('hidden');
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

    // Función para validar formulario
    function validateForm() {
        let isValid = true;
        clearFormErrors();

        const requiredFields = ['username', 'email', 'name', 'role_id', 'status'];
        const action = document.getElementById('formAction').value;

        // Si es crear, password también es requerido
        if (action === 'create') {
            requiredFields.push('password');
        }

        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                const errorDiv = document.getElementById(field + '-error');
                if (errorDiv) {
                    errorDiv.textContent = 'Este campo es requerido';
                    errorDiv.classList.remove('hidden');
                }
                isValid = false;
            }
        });

        // Validar email
        const email = document.getElementById('email').value;
        if (email && !isValidEmail(email)) {
            const emailInput = document.getElementById('email');
            emailInput.classList.add('border-red-500');
            const errorDiv = document.getElementById('email-error');
            if (errorDiv) {
                errorDiv.textContent = 'El email no tiene un formato válido';
                errorDiv.classList.remove('hidden');
            }
            isValid = false;
        }

        return isValid;
    }

    // Función para validar email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
</script>