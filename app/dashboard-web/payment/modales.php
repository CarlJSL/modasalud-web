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
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
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
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
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
    let alertDiv = document.createElement('div');
    alertDiv.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-4 py-2 rounded shadow-lg text-white ' + (success ? 'bg-green-600' : 'bg-red-600');
    alertDiv.innerText = message;
    document.body.appendChild(alertDiv);
    setTimeout(() => {
        alertDiv.remove();
    }, 2000);
}

function openPaymentDetailsModal(payment) {
    const order = payment.order_info;
    const statusMap = {
        'PAID': { text: 'Pagado', class: 'bg-green-100 text-green-800', icon: 'fas fa-check-circle' },
        'PENDING': { text: 'Pendiente', class: 'bg-yellow-100 text-yellow-800', icon: 'fas fa-clock' },
        'FAILED': { text: 'Fallido', class: 'bg-red-100 text-red-800', icon: 'fas fa-times-circle' }
    };
    const methodMap = {
        'YAPE': { text: 'Yape', icon: 'fas fa-mobile-alt' },
        'PLIN': { text: 'Plin', icon: 'fas fa-mobile-alt' },
        'TRANSFER': { text: 'Transferencia', icon: 'fas fa-university' },
        'CASH': { text: 'Efectivo', icon: 'fas fa-money-bill-wave' }
    };
    
    const status = statusMap[payment.status] || { text: payment.status, class: 'bg-gray-100 text-gray-800', icon: 'fas fa-question' };
    const method = methodMap[payment.method] || { text: payment.method, icon: 'fas fa-question' };
    
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
                        <span class="text-gray-600">Verificado:</span>
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
