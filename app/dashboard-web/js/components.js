/**
 * Componentes reutilizables del dashboard
 * Archivo: app/dashboard-web/js/components.js
 */

// Función para inicializar el toggle de filtros
function initializeFiltersToggle(hasActiveFilters = false) {
    const toggleButton = document.getElementById('toggleFilters');
    const filtersPanel = document.getElementById('filtersPanel');
    
    if (toggleButton && filtersPanel) {
        // Agregar evento de click
        toggleButton.addEventListener('click', function() {
            filtersPanel.classList.toggle('hidden');
        });
        
        // Mostrar filtros si hay filtros activos
        if (hasActiveFilters) {
            filtersPanel.classList.remove('hidden');
        }
    }
}

// Función para manejar paginación
function initializePagination() {
    // Código para paginación si es necesario
}

// Función para manejar búsqueda
function initializeSearch() {
    const searchForm = document.querySelector('form[method="get"]');
    if (searchForm) {
        // Lógica adicional de búsqueda si es necesaria
    }
}

// Función para inicializar todos los componentes
function initializeComponents(options = {}) {
    initializeFiltersToggle(options.hasActiveFilters);
    initializePagination();
    initializeSearch();
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Buscar si hay filtros activos automáticamente
    const filtersPanel = document.getElementById('filtersPanel');
    const hasActiveFilters = filtersPanel && !filtersPanel.classList.contains('hidden');
    
    initializeComponents({ hasActiveFilters });
});
