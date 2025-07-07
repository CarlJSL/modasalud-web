# Módulo de Categorías - Documentación

## Descripción General

El módulo de categorías permite gestionar las categorías principales y las subcategorías (relaciones producto-categoría) del sistema de e-commerce. Está diseñado con una interfaz de pestañas que permite alternar entre la gestión de categorías principales y subcategorías.

## Estructura de Archivos

### Archivos Principales
- **`categories.php`**: Controlador principal y vista del módulo
- **`modales.php`**: Modales para CRUD de categorías y subcategorías
- **`categoryModel.php`**: Modelo con lógica de negocio y acceso a datos

### Archivos de Datos
- **`datos_ejemplo_categorias.sql`**: Datos de ejemplo para categorías y subcategorías

## Funcionalidades

### Gestión de Categorías Principales

#### Características:
- **Listado paginado**: Visualización de categorías con paginación (10 por página)
- **Búsqueda**: Filtros por nombre y descripción
- **Estadísticas**: Contador de productos por categoría
- **CRUD completo**: Crear, leer, actualizar y eliminar categorías

#### Campos de Categoría:
- **ID**: Identificador único
- **Nombre**: Nombre de la categoría (requerido, único)
- **Descripción**: Descripción opcional de la categoría
- **Productos**: Contador de productos asociados

### Gestión de Subcategorías (Product Categories)

#### Características:
- **Listado paginado**: Visualización de relaciones producto-categoría
- **Búsqueda**: Filtros por nombre de producto o categoría
- **Selección dinámica**: Dropdowns para seleccionar productos y categorías
- **CRUD completo**: Crear, leer, actualizar y eliminar relaciones

#### Campos de Subcategoría:
- **ID**: Identificador único de la relación
- **Producto**: Producto seleccionado (requerido)
- **Categoría**: Categoría seleccionada (requerido)
- **Validación**: Prevención de duplicados en la misma combinación

## Interfaz de Usuario

### Diseño
- **Pestañas navegables**: Alternancia entre categorías y subcategorías
- **Diseño responsive**: Compatible con dispositivos móviles
- **Consistencia visual**: Sigue el mismo patrón de diseño que otros módulos
- **Feedback visual**: Toasts para confirmaciones y errores

### Elementos de Interfaz
- **Botones de acción**: Crear, editar, eliminar
- **Modales**: Formularios modales para CRUD
- **Filtros**: Búsqueda y paginación
- **Estadísticas**: Información resumida en cards

## Funciones JavaScript

### Funciones Principales
- **`openCategoryModal(id)`**: Abre modal para crear/editar categorías
- **`openSubcategoryModal(id)`**: Abre modal para crear/editar subcategorías
- **`confirmDelete(type, id)`**: Confirma eliminación de elementos
- **`loadProducts()`**: Carga productos para dropdowns
- **`loadCategories()`**: Carga categorías para dropdowns

### Validaciones
- **Validación de formularios**: HTML5 y JavaScript
- **Prevención de duplicados**: Validación de nombres únicos
- **Validación de relaciones**: Prevención de duplicados en subcategorías

## Modelo de Datos

### Métodos del Modelo

#### Categorías Principales:
- `getAllCategories()`: Obtiene categorías paginadas
- `getCategoryById()`: Obtiene una categoría específica
- `createCategory()`: Crea nueva categoría
- `updateCategory()`: Actualiza categoría existente
- `deleteCategory()`: Elimina categoría
- `categoryNameExists()`: Verifica nombres únicos

#### Subcategorías:
- `getAllProductCategories()`: Obtiene subcategorías paginadas
- `getSubcategoryById()`: Obtiene subcategoría específica
- `createSubcategory()`: Crea nueva subcategoría
- `updateSubcategory()`: Actualiza subcategoría
- `deleteSubcategory()`: Elimina subcategoría
- `subcategoryExists()`: Verifica combinaciones únicas

#### Auxiliares:
- `getAllProductsForSelect()`: Productos para dropdowns
- `getAllCategoriesForSelect()`: Categorías para dropdowns
- `getGeneralStats()`: Estadísticas generales

## Endpoints AJAX

### Categorías:
- **POST** `categories.php?action=create_category`
- **POST** `categories.php?action=update_category`
- **POST** `categories.php?action=delete_category`
- **POST** `categories.php?action=get_category`

### Subcategorías:
- **POST** `categories.php?action=create_subcategory`
- **POST** `categories.php?action=update_subcategory`
- **POST** `categories.php?action=delete_subcategory`
- **POST** `categories.php?action=get_subcategory`

### Auxiliares:
- **POST** `categories.php?action=get_products`
- **POST** `categories.php?action=get_categories_list`

## Seguridad

### Validaciones
- **Sanitización**: Todos los inputs se sanitizan
- **Validación de tipos**: Verificación de tipos de datos
- **Prevención de duplicados**: Validación de unicidad
- **Validación de relaciones**: Verificación de existencia de productos/categorías

### Protección
- **Prepared statements**: Prevención de SQL injection
- **Validación de entrada**: Verificación de datos obligatorios
- **Manejo de errores**: Respuestas JSON estructuradas

## Instalación y Configuración

### Requisitos
- PHP 7.4+
- Base de datos PostgreSQL
- Tablas: `categories`, `product_category_mapping`, `products`

### Pasos de Instalación
1. Verificar que existan las tablas necesarias
2. Ejecutar `datos_ejemplo_categorias.sql` para datos de prueba
3. Configurar permisos de acceso al módulo
4. Verificar integración con el menú de navegación

## Integración con el Sistema

### Navegación
- Integrado en el menú principal del dashboard
- Enlace directo desde la barra lateral
- Breadcrumbs para navegación contextual

### Dependencias
- **Bootstrap 5**: Framework CSS
- **Font Awesome**: Iconos
- **jQuery**: Manipulación DOM (opcional)
- **Tailwind CSS**: Estilos adicionales

## Personalización

### Estilos
- Modificar clases CSS en `categories.php`
- Personalizar colores y tipografías
- Ajustar responsive breakpoints

### Funcionalidades
- Agregar campos adicionales a categorías
- Implementar filtros avanzados
- Añadir exportación de datos

## Troubleshooting

### Errores Comunes
- **Error 500**: Verificar conexión a base de datos
- **Duplicados**: Verificar validaciones de unicidad
- **Modales no abren**: Verificar inclusión de Bootstrap JS
- **AJAX falla**: Verificar endpoints y parámetros

### Logs
- Revisar logs del servidor web
- Verificar errores en consola del navegador
- Validar respuestas JSON de endpoints

## Mantenimiento

### Actualizaciones
- Revisar compatibilidad con versiones de PHP
- Actualizar dependencias de frontend
- Validar funcionamiento tras cambios en base de datos

### Monitoreo
- Verificar performance de consultas
- Monitorear uso de memoria
- Revisar logs de errores regularmente
