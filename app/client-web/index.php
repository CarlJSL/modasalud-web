<?php
session_start();
// index.php principal de ModaSalud
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ModaSalud - Tu tienda de moda y salud</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/submenu.php'; ?>
    <?php include 'includes/filtro-superior.php'; ?>
    <main class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 md:grid-cols-[260px_1fr] gap-8">
        <!-- Filtros laterales -->
        <?php include 'includes/filtros.php'; ?>
        <!-- Área de productos -->
        <div class="flex flex-col h-full">
            <?php include __DIR__ . '/includes/product-grid.php'; ?>
            <?php /* include 'includes/paginacion.php'; */ ?>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts para el carrito -->
    <script>
        // Función para agregar producto al carrito
        async function addToCart(productId, quantity = 1) {
            const loadingBtn = document.querySelector(`[data-product-id="${productId}"]`);
            const originalText = loadingBtn.textContent;
            
            loadingBtn.disabled = true;
            loadingBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agregando...';
            
            try {
                const response = await fetch('cart-ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add&product_id=${productId}&quantity=${quantity}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    loadingBtn.innerHTML = '<i class="fas fa-check"></i>';
                    loadingBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                    loadingBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                    updateCartCounter();
                    setTimeout(() => {
                        loadingBtn.innerHTML = '<i class="fas fa-cart-plus"></i>';
                        loadingBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                        loadingBtn.classList.add('bg-purple-600', 'hover:bg-purple-700');
                        loadingBtn.disabled = false;
                    }, 1500);
                } else {
                    showNotification(data.message, 'error');
                    loadingBtn.innerHTML = originalText;
                    loadingBtn.disabled = false;
                }
            } catch (error) {
                showNotification('Error de conexión', 'error');
                loadingBtn.innerHTML = originalText;
                loadingBtn.disabled = false;
            }
        }
        
        // Función para mostrar notificaciones
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            }`;
            notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Función para actualizar el contador del carrito
        async function updateCartCounter() {
            try {
                const response = await fetch('cart-count.php');
                const data = await response.json();
                
                if (data.success) {
                    const cartBadge = document.querySelector('.fas.fa-shopping-cart').parentElement.querySelector('span');
                    if (data.count > 0) {
                        if (cartBadge) {
                            cartBadge.textContent = data.count;
                        } else {
                            // Crear el badge si no existe
                            const cartLink = document.querySelector('.fas.fa-shopping-cart').parentElement;
                            const badge = document.createElement('span');
                            badge.className = 'absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full px-1.5 py-0.5';
                            badge.textContent = data.count;
                            cartLink.appendChild(badge);
                        }
                    } else {
                        if (cartBadge) {
                            cartBadge.remove();
                        }
                    }
                }
            } catch (error) {
                console.error('Error updating cart counter:', error);
            }
        }
    </script>
</body>
</html>