// JavaScript para manejar filtros de productos
document.addEventListener('DOMContentLoaded', function() {
    // Event listeners para los filtros
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);
    document.getElementById('priceFilter').addEventListener('change', filterProducts);
    document.getElementById('sizeFilter').addEventListener('change', filterProducts);
});

function filterProducts() {
    const category = document.getElementById('categoryFilter').value;
    const price = document.getElementById('priceFilter').value;
    const size = document.getElementById('sizeFilter').value;

    // Construir parámetros de consulta
    const params = new URLSearchParams();
    if (category) params.append('category', category);
    if (price) params.append('price', price);
    if (size) params.append('size', size);

    // Hacer petición AJAX para obtener productos filtrados
    fetch(`api/filter-products.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProductsGrid(data.products);
            }
        })
        .catch(error => {
            console.error('Error al filtrar productos:', error);
        });
}

function updateProductsGrid(products) {
    const grid = document.getElementById('productsGrid');
    
    if (products.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron productos</h3>
                <p class="text-gray-500">Prueba ajustando los filtros de búsqueda</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = products.map(product => `
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                ${product.main_image ? 
                    `<img src="../${product.main_image}" 
                         alt="${product.name}"
                         class="w-full h-64 object-cover">` :
                    `<div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>`
                }
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2">${product.name}</h3>
                <p class="text-gray-600 text-sm mb-3">${product.description || ''}</p>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-gray-900">S/ ${parseFloat(product.price).toFixed(2)}</span>
                    <button onclick="addToCart(${product.id}, '${product.name}', ${product.price})" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-cart-plus mr-2"></i>Agregar
                    </button>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    <span>Stock: ${product.stock}</span>
                    <span class="mx-2">|</span>
                    <span>Talla: ${product.size}</span>
                </div>
            </div>
        </div>
    `).join('');
}

// Función de búsqueda
function searchProducts(query) {
    const params = new URLSearchParams();
    if (query) params.append('search', query);
    
    // Agregar filtros actuales
    const category = document.getElementById('categoryFilter').value;
    const price = document.getElementById('priceFilter').value;
    const size = document.getElementById('sizeFilter').value;
    
    if (category) params.append('category', category);
    if (price) params.append('price', price);
    if (size) params.append('size', size);

    fetch(`api/filter-products.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProductsGrid(data.products);
            }
        })
        .catch(error => {
            console.error('Error en búsqueda:', error);
        });
}
