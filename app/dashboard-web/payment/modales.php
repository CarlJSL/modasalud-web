<!-- Modal para cambiar estado de pago -->
<div id="paymentStatusModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">Cambiar Estado de Pago</h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('paymentStatusModal')">&times;</button>
        </div>
        <div class="p-6">
            <div class="text-center">
                <div class="mb-4 flex items-center gap-2">
                    <input type="checkbox" id="otherPaymentRejected" name="rejected" onchange="toggleOtherPaymentImage()">
                    <label for="otherPaymentRejected" class="text-sm text-gray-700">Pago rechazado</label>
                </div>
                <div class="mb-4">
                    <label for="otherPaymentImage" class="block text-sm font-medium text-gray-700 mb-2">Subir imagen de comprobante</label>
                    <input type="file" id="otherPaymentImage" name="comprobante" class="w-full px-2 py-1 border rounded">
                </div>
            </div>
            <input type="hidden" id="paymentIdInput">
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" onclick="closeModal('paymentStatusModal')">Cancelar</button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700" onclick="submitPaymentStatusChange()">Guardar</button>
        </div>
    </div>
</div>
<!-- Modal para agregar código de verificación -->
<div id="verificationModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">Agregar Código de Verificación</h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('verificationModal')">&times;</button>
        </div>
        <div class="p-6">
            <div class="mb-4 flex items-center gap-2 justify-center">
                <input type="checkbox" id="yapeRejectedCheckbox" onchange="toggleYapeVerificationInput()">
                <label for="yapeRejectedCheckbox" class="text-sm text-gray-700">Pago rechazado</label>
            </div>
            <div class="text-center" id="yapeVerificationInputDiv">
                <label for="verificationCodeInput" class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                <input type="text" id="verificationCodeInput" class="w-full px-2 py-1 border rounded">
            </div>
            <input type="hidden" id="verificationPaymentIdInput">
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" onclick="closeModal('verificationModal')">Cancelar</button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700" onclick="submitVerificationCode()">Guardar</button>
        </div>
    </div>
</div>
<!-- Modal para pagos en efectivo -->
<div id="cashPaymentModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-money-bill-wave mr-2"></i>Verificar Pago en Efectivo
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('cashPaymentModal')">&times;</button>
        </div>
        <div class="p-6">
            <div class="text-center">
                <div class="mb-4 flex items-center gap-2 justify-center">
                    <input type="checkbox" id="cashRejectedCheckbox">
                    <label for="cashRejectedCheckbox" class="text-sm text-gray-700">Pago rechazado</label>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    Los pagos en efectivo no requieren comprobante.
                    Simplemente confirma si el pago fue recibido o recházalo si es necesario.
                </p>
            </div>
            <input type="hidden" id="cashPaymentIdInput">
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" onclick="closeModal('cashPaymentModal')">Cancelar</button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-md hover:bg-orange-700" onclick="submitCashPayment()">Confirmar</button>
        </div>
    </div>
</div>
<!-- Modal para ver detalles del pago -->
<div id="paymentDetailsModal" class="fixed inset-0 bg-black bg-opacity-15 hidden z-50 items-center justify-center p-4 modal-backdrop">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl animate-fadeIn modal-content">
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-receipt mr-2"></i>Detalles del Pago
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeModal('paymentDetailsModal')">&times;</button>
        </div>
        <div class="p-6 max-h-96 overflow-y-auto">
            <div id="paymentDetailsContent">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" onclick="closeModal('paymentDetailsModal')">Cerrar</button>
        </div>
    </div>
</div>
<script>
    function viewClientPaymentImage(proofUrl, paymentId, paymentStatus) {
        // Crear modal para mostrar la imagen
        const modalHtml = `
            <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 modal-backdrop">

                <div class="bg-white rounded-lg p-6 max-w-4xl max-h-screen overflow-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-image text-purple-500 mr-2"></i>
                            Comprobante de Pago - Cliente Web
                        </h3>
                        <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <img src="../../${proofUrl}" alt="Comprobante de pago" class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg border">
                        <div class="mt-4 text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Este comprobante fue subido desde el cliente web
                        </div>
                        <div class="mt-4 flex gap-3 justify-center items-center">
                            <a href="../../${proofUrl}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Abrir en nueva pestaña
                            </a>
                            ${paymentStatus === 'PENDING' ? `
                                <button onclick="verifyClientWebPayment(${paymentId}, false)" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    <i class="fas fa-check mr-2"></i>
                                    Verificar y Aprobar
                                </button>
                                <button onclick="verifyClientWebPayment(${paymentId}, true)" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    <i class="fas fa-times mr-2"></i>
                                    Rechazar
                                </button>
                            ` : `
                                <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Ya verificado
                                </span>
                            `}
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Agregar el modal al body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function openPaymentStatusModal(paymentId) {
        document.getElementById('paymentIdInput').value = paymentId;
        document.getElementById('otherPaymentRejected').checked = false;
        document.getElementById('otherPaymentImage').value = '';
        document.getElementById('otherPaymentImage').parentElement.style.display = '';
        showModal('paymentStatusModal');
    }

    function toggleOtherPaymentImage() {
        var rejected = document.getElementById('otherPaymentRejected').checked;
        document.getElementById('otherPaymentImage').parentElement.style.display = rejected ? 'none' : '';
    }

    function submitPaymentStatusChange() {
        const paymentId = document.getElementById('paymentIdInput').value;
        const rejected = document.getElementById('otherPaymentRejected').checked;
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', paymentId);
        formData.append('rejected', rejected ? 'on' : '');

        if (!rejected) {
            const imageFile = document.getElementById('otherPaymentImage').files[0];
            if (imageFile) {
                formData.append('payment_image', imageFile);
            }
        }

        fetch('payments.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                showAlert(data.message, data.success);
                if (data.success) {
                    setTimeout(() => location.reload(), 1200);
                }
            });
    }

    function openVerificationModal(paymentId) {
        document.getElementById('verificationPaymentIdInput').value = paymentId;
        document.getElementById('verificationCodeInput').value = '';
        document.getElementById('yapeRejectedCheckbox').checked = false;
        document.getElementById('yapeVerificationInputDiv').style.display = '';
        showModal('verificationModal');
    }

    function submitVerificationCode() {
        const paymentId = document.getElementById('verificationPaymentIdInput').value;
        const rejected = document.getElementById('yapeRejectedCheckbox').checked;
        let code = '';
        let body = `action=add_verification_code&id=${paymentId}`;
        if (!rejected) {
            code = document.getElementById('verificationCodeInput').value;
            body += `&verification_code=${encodeURIComponent(code)}`;
        } else {
            body += `&verification_code=&status=FAILED`;
        }
        fetch('payments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: body
            })
            .then(res => res.json())
            .then(data => {
                showAlert(data.message, data.success);
                if (data.success) {
                    setTimeout(() => location.reload(), 1200);
                }
            });
    }

    function submitCashPayment() {
        const paymentId = document.getElementById('cashPaymentIdInput').value;
        const rejected = document.getElementById('cashRejectedCheckbox').checked;
        let body = `action=cash_payment&id=${paymentId}`;
        body += `&rejected=${rejected ? 'on' : ''}`;

        fetch('payments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: body
            })
            .then(res => res.json())
            .then(data => {
                showAlert(data.message, data.success);
                if (data.success) {
                    setTimeout(() => location.reload(), 1200);
                }
            });
    }

    function showAlert(message, success) {
        showStyledAlert(message, success ? 'success' : 'error');
    }

    // Función para mostrar alertas con estilo (duplicada desde payments.php para compatibilidad)
    function showStyledAlert(message, type = 'info') {
        // Remover alertas existentes
        const existingAlerts = document.querySelectorAll('.styled-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Configurar estilos según el tipo
        const alertConfigs = {
            success: {
                bgColor: 'bg-green-500',
                icon: 'fas fa-check-circle',
                title: '¡Éxito!'
            },
            error: {
                bgColor: 'bg-red-500',
                icon: 'fas fa-exclamation-triangle',
                title: 'Error'
            },
            warning: {
                bgColor: 'bg-yellow-500',
                icon: 'fas fa-exclamation-circle',
                title: 'Advertencia'
            },
            info: {
                bgColor: 'bg-blue-500',
                icon: 'fas fa-info-circle',
                title: 'Información'
            }
        };

        const config = alertConfigs[type] || alertConfigs.info;

        // Crear el elemento de alerta
        const alertElement = document.createElement('div');
        alertElement.className = `styled-alert fixed top-4 right-4 z-[9999] ${config.bgColor} text-white rounded-lg shadow-2xl transform transition-all duration-500 ease-in-out translate-x-full opacity-0`;
        alertElement.style.minWidth = '320px';
        alertElement.style.maxWidth = '400px';

        alertElement.innerHTML = `
            <div class="flex items-start p-4">
                <div class="flex-shrink-0">
                    <i class="${config.icon} text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h4 class="font-semibold text-sm">${config.title}</h4>
                    <p class="text-sm mt-1 opacity-90">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-3 text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="h-1 bg-white bg-opacity-20">
                <div class="h-full bg-white bg-opacity-40 rounded-full animate-progress" style="animation: progress 5s linear forwards;"></div>
            </div>
        `;

        // Agregar estilos CSS para la animación
        if (!document.querySelector('#alert-styles')) {
            const style = document.createElement('style');
            style.id = 'alert-styles';
            style.textContent = `
                @keyframes progress {
                    from { width: 100%; }
                    to { width: 0%; }
                }
                .animate-progress {
                    width: 100%;
                }
                .styled-alert:hover .animate-progress {
                    animation-play-state: paused;
                }
            `;
            document.head.appendChild(style);
        }

        // Agregar al DOM
        document.body.appendChild(alertElement);

        // Animar la entrada
        setTimeout(() => {
            alertElement.classList.remove('translate-x-full', 'opacity-0');
            alertElement.classList.add('translate-x-0', 'opacity-100');
        }, 100);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alertElement.parentElement) {
                alertElement.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => alertElement.remove(), 500);
            }
        }, 5000);

        // Efecto de sonido (opcional)
        if (type === 'success') {
            // Pequeño efecto visual adicional para éxito
            alertElement.style.boxShadow = '0 10px 25px rgba(34, 197, 94, 0.3)';
        } else if (type === 'error') {
            alertElement.style.boxShadow = '0 10px 25px rgba(239, 68, 68, 0.3)';
        }
    }

    function openPaymentDetailsModal(payment) {
        const order = payment.order_info;
        const statusMap = {
            'PAID': {
                text: 'Pagado',
                class: 'bg-green-100 text-green-800',
                icon: 'fas fa-check-circle'
            },
            'PENDING': {
                text: 'Pendiente',
                class: 'bg-yellow-100 text-yellow-800',
                icon: 'fas fa-clock'
            },
            'FAILED': {
                text: 'Fallido',
                class: 'bg-red-100 text-red-800',
                icon: 'fas fa-times-circle'
            }
        };
        const methodMap = {
            'YAPE': {
                text: 'Yape',
                icon: 'fas fa-mobile-alt'
            },
            'PLIN': {
                text: 'Plin',
                icon: 'fas fa-mobile-alt'
            },
            'TRANSFER': {
                text: 'Transferencia',
                icon: 'fas fa-university'
            },
            'CASH': {
                text: 'Efectivo',
                icon: 'fas fa-money-bill-wave'
            }
        };

        const status = statusMap[payment.status] || {
            text: payment.status,
            class: 'bg-gray-100 text-gray-800',
            icon: 'fas fa-question'
        };
        const method = methodMap[payment.method] || {
            text: payment.method,
            icon: 'fas fa-question'
        };

        let content = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h6 class="font-semibold text-gray-800 border-b pb-2">Información del Pago</h6>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Pago:</span>
                        <span class="font-medium">#${payment.id}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Método:</span>
                        <span class="flex items-center"><i class="${method.icon} mr-1"></i>${method.text}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs ${status.class}">
                            <i class="${status.icon} mr-1"></i>${status.text}
                        </span>
                    </div>
                    ${payment.verification_code && payment.method === 'YAPE' ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Código de seguridad:</span>
                        <span class="font-medium">${payment.verification_code}</span>
                    </div>` : ''}
                    ${payment.verified_at ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha de Verificado:</span>
                        <span class="font-medium">${new Date(payment.verified_at).toLocaleString()}</span>
                    </div>` : ''}
                    ${payment.paid_at ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha de pago:</span>
                        <span class="font-medium">${new Date(payment.paid_at).toLocaleString()}</span>
                    </div>` : ''}
                    ${payment.verified_by ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Aprobado por:</span>
                        <span class="font-medium">${payment.verified_by_name || payment.verified_by}</span>
                    </div>` : ''}
                </div>
            </div>
            <div class="space-y-4">
                <h6 class="font-semibold text-gray-800 border-b pb-2">Información de la Orden</h6>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Orden:</span>
                        <span class="font-medium">#${payment.order_id}</span>
                    </div>
                    ${order ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cliente:</span>
                        <span class="font-medium">${order.client_name || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">${order.client_email || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Teléfono:</span>
                        <span class="font-medium">${order.client_phone || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fuente de Orden:</span>
                        <span class="font-medium px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200">
                            ${order.order_source || 'N/A'}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-medium text-lg">S/ ${parseFloat(order.total_price || 0).toFixed(2)}</span>
                    </div>` : ''}
                </div>
            </div>
        </div>
        ${payment.proof_url ? `
        <div class="mt-6">
            <h6 class="font-semibold text-gray-800 border-b pb-2 mb-4">Comprobante de Pago</h6>
            <div class="text-center">
                <img src="../../${payment.proof_url}" alt="Comprobante" class="max-w-full max-h-64 mx-auto rounded-lg shadow-md border">
            </div>
        </div>` : ''}
    `;

        document.getElementById('paymentDetailsContent').innerHTML = content;
        showModal('paymentDetailsModal');
    }

    function toggleYapeVerificationInput() {
        var rejected = document.getElementById('yapeRejectedCheckbox').checked;
        document.getElementById('yapeVerificationInputDiv').style.display = rejected ? 'none' : '';
    }

    function showModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }
</script>