<!-- Modal para crear/editar rol -->
<div id="roleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 modal-backdrop">
    <div class="relative top-5 mx-auto p-5 border max-w-4xl w-full shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 id="roleModalTitle" class="text-lg font-medium text-gray-900">Crear Nuevo Rol</h3>
            <button onclick="document.getElementById('roleModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="my-4 overflow-y-auto" style="max-height: 70vh;">
            <form id="roleForm" onsubmit="saveRole(event)">
                <input type="hidden" id="roleId" name="id" value="">
                
                <div class="mb-4">
                    <label for="roleName" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol*</label>
                    <input type="text" id="roleName" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <div id="nameError" class="text-red-500 text-xs mt-1 hidden"></div>
                </div>

                <div class="mb-4">
                    <label for="roleDescription" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="roleDescription" name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Permisos</h4>
                    <div class="bg-blue-50 p-2 rounded mb-3">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-blue-700">Selecciona los permisos para cada tabla</div>
                            <div>
                                <button type="button" onclick="selectAllPermissions(true)" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">Seleccionar Todo</button>
                                <button type="button" onclick="selectAllPermissions(false)" class="text-xs border border-gray-300 text-gray-700 px-2 py-1 rounded hover:bg-gray-100">Deseleccionar Todo</button>
                            </div>
                        </div>
                    </div>
                    <div id="permissionsContainer" class="space-y-2">
                        <!-- Los permisos se cargarán dinámicamente aquí -->
                        <div class="flex justify-center items-center h-16">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                            <span class="ml-2 text-gray-600">Cargando tablas disponibles...</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-200">
                    <button type="button" onclick="document.getElementById('roleModal').classList.add('hidden')" class="mr-2 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ver detalles del rol -->
<div id="roleDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 modal-backdrop">
    <div class="relative top-5 mx-auto p-5 border max-w-4xl w-full shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 id="roleDetailTitle" class="text-lg font-medium text-gray-900">Detalles del Rol</h3>
            <button onclick="document.getElementById('roleDetailModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="my-4 overflow-y-auto" style="max-height: 70vh;">
            <div id="roleDetailContent">
                <!-- El contenido de los detalles se cargará dinámicamente aquí -->
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <span class="ml-2 text-gray-600">Cargando detalles...</span>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between">
            <div>
                <button id="editRoleBtn" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    Editar
                </button>
            </div>
            <button onclick="document.getElementById('roleDetailModal').classList.add('hidden')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Script para la gestión de roles -->
<script>
    // Variables globales
    let availableTables = [];
    let currentRoleId = null;
    let currentRoleDetails = null;

    // Cargar las tablas disponibles para asignar permisos
    function loadAvailableTables() {
        fetch('?action=get_tables')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availableTables = data.tables;
                    renderPermissionsForm(availableTables);
                } else {
                    console.error('Error al cargar tablas:', data.message);
                    document.getElementById('permissionsContainer').innerHTML = `
                        <div class="text-red-500 text-center p-4">
                            Error al cargar las tablas disponibles. Por favor intente nuevamente.
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('permissionsContainer').innerHTML = `
                    <div class="text-red-500 text-center p-4">
                        Error de conexión. Por favor intente nuevamente.
                    </div>
                `;
            });
    }

    // Generar el formulario de permisos con las tablas disponibles
    function renderPermissionsForm(tables) {
        if (!tables || tables.length === 0) {
            document.getElementById('permissionsContainer').innerHTML = `
                <div class="text-gray-500 text-center p-4">
                    No hay tablas disponibles para asignar permisos.
                </div>
            `;
            return;
        }

        let html = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        `;

        tables.forEach(table => {
            html += `
                <div class="border border-gray-200 rounded-md p-2">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">${table}</span>
                        <div class="flex space-x-1">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="permissions[${table}][create]" class="permission-checkbox form-checkbox h-3 w-3 text-blue-600">
                                <span class="ml-1 text-xs text-gray-700">Crear</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="permissions[${table}][read]" class="permission-checkbox form-checkbox h-3 w-3 text-blue-600">
                                <span class="ml-1 text-xs text-gray-700">Leer</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="permissions[${table}][update]" class="permission-checkbox form-checkbox h-3 w-3 text-blue-600">
                                <span class="ml-1 text-xs text-gray-700">Actualizar</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="permissions[${table}][delete]" class="permission-checkbox form-checkbox h-3 w-3 text-blue-600">
                                <span class="ml-1 text-xs text-gray-700">Eliminar</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
            </div>
        `;

        document.getElementById('permissionsContainer').innerHTML = html;
    }

    // Cargar los permisos de un rol específico
    function loadRolePermissions(roleId) {
        fetch(`?action=get_detailed&id=${roleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.role.permissions) {
                    // Primero cargar las tablas disponibles si no están cargadas
                    if (!availableTables.length) {
                        fetch('?action=get_tables')
                            .then(resp => resp.json())
                            .then(tableData => {
                                if (tableData.success) {
                                    availableTables = tableData.tables;
                                    renderPermissionsForm(availableTables);
                                    setRolePermissions(data.role.permissions);
                                }
                            });
                    } else {
                        renderPermissionsForm(availableTables);
                        setRolePermissions(data.role.permissions);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Establecer los valores de los permisos en el formulario
    function setRolePermissions(permissions) {
        permissions.forEach(permission => {
            const tableName = permission.table_name;
            if (permission.can_create) {
                document.querySelector(`input[name="permissions[${tableName}][create]"]`).checked = true;
            }
            if (permission.can_read) {
                document.querySelector(`input[name="permissions[${tableName}][read]"]`).checked = true;
            }
            if (permission.can_update) {
                document.querySelector(`input[name="permissions[${tableName}][update]"]`).checked = true;
            }
            if (permission.can_delete) {
                document.querySelector(`input[name="permissions[${tableName}][delete]"]`).checked = true;
            }
        });
    }

    // Seleccionar o deseleccionar todos los permisos
    function selectAllPermissions(select) {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = select;
        });
    }

    // Limpiar permisos del formulario
    function clearPermissions() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    // Guardar rol (crear o actualizar)
    function saveRole(event) {
        event.preventDefault();
        
        // Limpiar errores anteriores
        document.getElementById('nameError').textContent = '';
        document.getElementById('nameError').classList.add('hidden');
        
        const formData = new FormData(document.getElementById('roleForm'));
        const roleId = document.getElementById('roleId').value;
        
        // Determinar si es crear o actualizar
        formData.append('action', roleId ? 'update' : 'create');
        
        // Mostrar indicador de carga
        const submitButton = event.submitter || document.querySelector('#roleForm button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
        `;
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('roleModal').classList.add('hidden');
                showNotification(data.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                if (data.errors) {
                    // Mostrar errores específicos
                    if (data.errors.name) {
                        document.getElementById('nameError').textContent = data.errors.name;
                        document.getElementById('nameError').classList.remove('hidden');
                        showNotification('Por favor corrige los errores en el formulario', 'error');
                    }
                } else {
                    showNotification(data.message || 'Error al guardar el rol', 'error');
                }
                
                // Restaurar el botón
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al guardar el rol: Error de conexión', 'error');
            
            // Restaurar el botón
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    }

    // Mostrar detalles del rol
    function showRoleDetails(role) {
        currentRoleId = role.id;
        currentRoleDetails = role;
        
        document.getElementById('roleDetailTitle').textContent = `Detalles del Rol: ${role.name}`;
        
        // Configurar el botón de editar
        document.getElementById('editRoleBtn').onclick = function() {
            document.getElementById('roleDetailModal').classList.add('hidden');
            editRole(role.id);
        };

        // Construir la vista de detalles
        let html = `
            <div class="space-y-4">
                <!-- Información básica -->
                <div class="bg-gray-50 p-3 rounded-md">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Información del Rol</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Nombre</p>
                            <p class="text-sm font-medium text-gray-900">${role.name}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Descripción</p>
                            <p class="text-sm text-gray-900">${role.description || 'N/A'}</p>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas del rol -->
                <div class="bg-blue-50 p-3 rounded-md">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Estadísticas del Rol</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white p-2 rounded shadow-sm">
                            <p class="text-xs text-gray-500">Total Permisos</p>
                            <p class="text-sm font-bold text-gray-900">${role.statistics.total_permissions}</p>
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm">
                            <p class="text-xs text-gray-500">Tablas Gestionadas</p>
                            <p class="text-sm font-bold text-gray-900">${role.statistics.tables_managed}</p>
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm">
                            <p class="text-xs text-gray-500">Total Usuarios</p>
                            <p class="text-sm font-bold text-gray-900">${role.statistics.total_users}</p>
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm">
                            <p class="text-xs text-gray-500">Nivel de Permisos</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${role.permission_level_color}">
                                ${role.permission_level}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Desglose de permisos por tabla -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Permisos por Tabla</h4>
                    <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tabla</th>
                                    <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Crear</th>
                                    <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Leer</th>
                                    <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actualizar</th>
                                    <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
        `;

        if (role.table_permissions && role.table_permissions.length > 0) {
            role.table_permissions.forEach(perm => {
                html += `
                    <tr>
                        <td class="px-2 py-1 whitespace-nowrap text-sm text-gray-900">${perm.table_name}</td>
                        <td class="px-2 py-1 whitespace-nowrap text-center">
                            ${perm.has_create ? 
                                '<svg class="h-4 w-4 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : 
                                '<svg class="h-4 w-4 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'}
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap text-center">
                            ${perm.has_read ? 
                                '<svg class="h-4 w-4 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : 
                                '<svg class="h-4 w-4 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'}
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap text-center">
                            ${perm.has_update ? 
                                '<svg class="h-4 w-4 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : 
                                '<svg class="h-4 w-4 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'}
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap text-center">
                            ${perm.has_delete ? 
                                '<svg class="h-4 w-4 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : 
                                '<svg class="h-4 w-4 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'}
                        </td>
                    </tr>
                `;
            });
        } else {
            html += `
                <tr>
                    <td colspan="5" class="px-2 py-2 text-center text-sm text-gray-500">No hay permisos configurados para este rol</td>
                </tr>
            `;
        }

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
        `;

        // Mostrar usuarios asignados
        html += `
                <!-- Usuarios asignados -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Usuarios Asignados (${role.users ? role.users.length : 0})</h4>
        `;

        if (role.users && role.users.length > 0) {
            html += `
                    <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-2 py-1 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Órdenes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
            `;

            role.users.forEach(user => {
                html += `
                    <tr>
                        <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900">${user.username}</td>
                        <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-900">${user.name || 'N/A'}</td>
                        <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-500">${user.email}</td>
                        <td class="px-2 py-1 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${user.status_color}">
                                ${user.status}
                            </span>
                        </td>
                        <td class="px-2 py-1 whitespace-nowrap text-right text-xs text-gray-900">${user.orders_created}</td>
                    </tr>
                `;
            });

            html += `
                            </tbody>
                        </table>
                    </div>
            `;
        } else {
            html += `
                    <div class="bg-white border border-gray-200 rounded-md p-4 text-center">
                        <p class="text-gray-500">No hay usuarios asignados a este rol</p>
                    </div>
            `;
        }

        html += `
                </div>
            </div>
        `;

        document.getElementById('roleDetailContent').innerHTML = html;
    }

    // Sistema de notificaciones mejorado
    function showNotification(message, type = 'success') {
        // Remover notificaciones previas
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(notification => {
            notification.remove();
        });

        // Crear nueva notificación
        const notification = document.createElement('div');
        notification.className = 'notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out translate-x-0';
        
        // Configurar estilo según el tipo
        if (type === 'success') {
            notification.classList.add('bg-green-50', 'border-l-4', 'border-green-500');
        } else if (type === 'error') {
            notification.classList.add('bg-red-50', 'border-l-4', 'border-red-500');
        } else if (type === 'warning') {
            notification.classList.add('bg-yellow-50', 'border-l-4', 'border-yellow-500');
        } else if (type === 'info') {
            notification.classList.add('bg-blue-50', 'border-l-4', 'border-blue-500');
        }

        // Configurar contenido
        const iconClasses = {
            success: 'text-green-500',
            error: 'text-red-500',
            warning: 'text-yellow-500',
            info: 'text-blue-500'
        };

        const iconPaths = {
            success: 'M5 13l4 4L19 7',
            error: 'M6 18L18 6M6 6l12 12',
            warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        };

        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 ${iconClasses[type]}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPaths[type]}"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium ${iconClasses[type]}">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button onclick="this.parentNode.parentNode.parentNode.parentNode.remove()" class="inline-flex ${iconClasses[type]} rounded-md p-1.5 hover:bg-gray-100 focus:outline-none">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Añadir al DOM
        document.body.appendChild(notification);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 5000);
    }
</script>

<!-- Modal de confirmación para eliminar roles -->
<div id="deleteRoleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 modal-backdrop">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2" id="deleteRoleTitle">Eliminar Rol</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="deleteRoleMessage">
                    ¿Estás seguro que deseas eliminar este rol?
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <input type="hidden" id="deleteRoleId" value="">
                <button id="confirmDeleteButton" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Sí, eliminar
                </button>
                <button onclick="document.getElementById('deleteRoleModal').classList.add('hidden')" class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales para mejorar la interfaz -->
<style>
    /* Animaciones para notificaciones */
    .notification-toast {
        animation: slideIn 0.5s ease-out forwards;
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    /* Mejoras para tablas con mucho contenido */
    .table-responsive {
        overflow-x: auto;
        max-width: 100%;
    }
    
    .table-responsive table {
        min-width: 100%;
    }
    
    .truncate-text {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .expand-cell:hover .truncate-text {
        white-space: normal;
        overflow: visible;
        position: relative;
        z-index: 10;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 0.5rem;
        border-radius: 0.25rem;
    }
</style>
