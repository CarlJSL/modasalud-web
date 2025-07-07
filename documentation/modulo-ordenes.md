# Módulo de Gestión de Órdenes

## Descripción
Este módulo implementa la gestión completa de órdenes en el sistema e-commerce, siguiendo el mismo patrón de diseño establecido para otros módulos como productos.

## Archivos Creados

### 1. `orderModel.php`
Modelo que maneja todas las operaciones CRUD y de consulta para órdenes:
- **getAll()**: Obtiene órdenes con filtros y paginación
- **count()**: Cuenta órdenes con filtros aplicados
- **getById()**: Obtiene una orden específica
- **getDetailedById()**: Obtiene orden con información completa (items, cliente, pago, etc.)
- **create()**: Crea nueva orden con transacciones
- **update()**: Actualiza orden existente
- **updateStatus()**: Cambia estado de orden específica
- **getClients()**: Obtiene clientes activos
- **getClientAddresses()**: Obtiene direcciones de un cliente
- **getCoupons()**: Obtiene cupones disponibles
- **getProducts()**: Obtiene productos disponibles
- **getGeneralStats()**: Estadísticas generales del módulo

### 2. `orders.php`
Controlador principal que maneja:
- **Operaciones AJAX**: create, update, update_status, get, details
- **Consultas auxiliares**: get_clients, get_client_addresses, get_products, get_coupons
- **Interfaz de usuario**: Lista de órdenes con filtros, búsqueda y paginación
- **Estadísticas**: Métricas en tiempo real del módulo

### 3. `modales.php`
Modales interactivos para:
- **orderModal**: Crear/editar órdenes con:
  - Selección de cliente y dirección
  - Gestión de productos (agregar, quitar, modificar cantidad)
  - Información de pago
  - Aplicación de cupones y descuentos
  - Cálculo automático de totales
- **orderDetailModal**: Visualización completa de orden con:
  - Información del cliente y dirección
  - Lista detallada de productos
  - Estado de pago y método
  - Historial y timestamps

## Características Implementadas

### Funcionalidades Principales
- ✅ Creación de órdenes con múltiples productos
- ✅ Gestión de inventario automática
- ✅ Sistema de cupones y descuentos
- ✅ Múltiples métodos de pago
- ✅ Estados de orden (Pendiente, Completada, Cancelada)
- ✅ Estados de pago (Pendiente, Pagado, Fallido)
- ✅ Búsqueda y filtros avanzados
- ✅ Paginación responsive
- ✅ Validaciones frontend y backend

### Características de UX/UI
- ✅ Interfaz consistente con el resto del sistema
- ✅ Modales responsivos con animaciones
- ✅ Cálculo en tiempo real de totales
- ✅ Validaciones en tiempo real
- ✅ Feedback visual de estados
- ✅ Acciones rápidas (aprobar, cancelar)

### Integraciones
- ✅ Sistema de clientes y direcciones
- ✅ Catálogo de productos
- ✅ Sistema de cupones
- ✅ Gestión de pagos
- ✅ Auditoría y logs (preparado)

## Estructura de Base de Datos

### Tablas Principales
- **orders**: Tabla principal de órdenes
- **order_items**: Items/productos de cada orden
- **payments**: Información de pagos
- **clients**: Clientes del sistema
- **client_addresses**: Direcciones de entrega
- **coupons**: Cupones de descuento
- **products**: Catálogo de productos

### Relaciones
- `orders.client_id` → `clients.id`
- `orders.address_id` → `client_addresses.id`
- `orders.coupon_id` → `coupons.id`
- `order_items.order_id` → `orders.id`
- `order_items.product_id` → `products.id`
- `payments.order_id` → `orders.id`

## Instalación y Configuración

### 1. Datos de Ejemplo
Ejecutar el script SQL para datos de prueba:
```sql
-- Ejecutar en la base de datos PostgreSQL
\i db/datos_ejemplo_ordenes.sql
```

### 2. Navegación
El módulo está accesible desde:
- **Dashboard** → **Ventas** → **Órdenes**
- URL directa: `/app/dashboard-web/orden/orders.php`

### 3. Permisos
Asegurar que el usuario web tenga permisos de:
- Lectura en todas las tablas relacionadas
- Escritura en `orders`, `order_items`, `payments`
- Actualización de stock en `products`

## Estados del Sistema

### Estados de Orden
- **PENDING**: Orden creada, pendiente de procesamiento
- **COMPLETED**: Orden completada y entregada
- **CANCELLED**: Orden cancelada

### Estados de Pago
- **PENDING**: Pago pendiente
- **PAID**: Pago confirmado
- **FAILED**: Pago fallido

### Métodos de Pago
- **CASH**: Efectivo
- **YAPE**: Yape
- **PLIN**: Plin
- **TRANSFER**: Transferencia bancaria

## API Endpoints

### Principales
- `POST /orders.php?action=create` - Crear orden
- `POST /orders.php?action=update` - Actualizar orden
- `POST /orders.php?action=update_status` - Cambiar estado
- `GET /orders.php?action=get&id=X` - Obtener orden
- `GET /orders.php?action=details&id=X` - Detalles completos

### Auxiliares
- `GET /orders.php?action=get_clients` - Lista de clientes
- `GET /orders.php?action=get_client_addresses&client_id=X` - Direcciones
- `GET /orders.php?action=get_products` - Productos disponibles
- `GET /orders.php?action=get_coupons` - Cupones disponibles

## Próximas Mejoras

### Funcionalidades Pendientes
- [ ] Sistema de notificaciones por email
- [ ] Integración con APIs de pago
- [ ] Generación de facturas PDF
- [ ] Tracking de envíos
- [ ] Sistema de devoluciones
- [ ] Reportes avanzados
- [ ] Historial de cambios de estado

### Optimizaciones
- [ ] Cache de consultas frecuentes
- [ ] Índices de base de datos optimizados
- [ ] Compresión de imágenes de comprobantes
- [ ] Backup automático de órdenes

---

**Desarrollado siguiendo el patrón arquitectónico establecido en el proyecto**
**Compatible con la estructura existente de productos y usuarios**
