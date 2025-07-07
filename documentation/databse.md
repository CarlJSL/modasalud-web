**Documentación de la Base de Datos del Proyecto E-Commerce de Moda para el Sector Salud**

---

### 📊 Descripción General

La base de datos ha sido diseñada para gestionar un sistema completo de comercio electrónico orientado a la venta de productos para el sector salud (ropa, accesorios, calzado especializado, etc.). Este sistema está optimizado para mantener integridad, escalabilidad y trazabilidad en sus operaciones.

---

### 🔍 Flujo General de Uso

1. **Autenticación y Registro:**

   * Usuarios pueden registrarse con un rol predefinido (`CUSTOMER`, `SELLER`, `ADMIN`).
   * Se almacenan sus datos, direcciones y estado de cuenta.

2. **Navegación y Catálogo de Productos:**

   * Productos clasificados por categorías.
   * Cada producto puede tener varias tallas, precios, stock, y múltiples imágenes.

3. **Carrito de Compras:**

   * Los usuarios agregan productos al carrito (`cart_items`).

4. **Proceso de Compra:**

   * Se crea una orden (`orders`) que puede usar un cupón de descuento.
   * Se generan los items de la orden (`order_items`).
   * Se registra el pago (`payments`).

5. **Reseñas:**

   * El cliente puede dejar una reseña con estrella, comentario e imágenes.

6. **Auditoría:**

   * Se registra automáticamente cada cambio importante en la tabla `audit_log`.

---

### 📄 Enumeraciones (ENUM)

* `user_status`: `ACTIVE`, `INACTIVE`
* `role`: `ADMIN`, `SELLER`, `CUSTOMER`
* `order_status`: `PENDING`, `COMPLETED`, `CANCELLED`
* `payment_status`: `PAID`, `PENDING`, `FAILED`
* `payment_method`: `YAPE`, `PLIN`, `TRANSFER`, `CASH`
* `discount_type`: `PERCENTAGE`, `FIXED`

---

### 📃 Tablas Principales

#### 1. `users`

* Almacena información del usuario.
* Incluye rol, estado, y campos de auditoría (`created_by`, `updated_by`).
* Trigger: actualiza `updated_at` automáticamente.

#### 2. `roles`

* Tabla que registra los roles predefinidos del sistema.
* Contiene campos `name` (nombre del rol) y `description` (descripción opcional).
* Ejemplos de roles: `ADMIN`, `SELLER`, `CUSTOMER`.

#### 3. `user_addresses`

* Múltiples direcciones por usuario.

#### 4. `categories`

* Clasificación de productos (Ej. "Chompas médicas").

#### 5. `products`

* Información de productos como nombre, precio, stock, etc.
* Soporta "soft delete" con `deleted_at`.

#### 6. `product_categories`

* Relación muchos-a-muchos entre productos y categorías.

#### 7. `product_images`

* Múltiples imágenes por producto.

#### 8. `cart_items`

* Representa el carrito de compras de cada usuario.

#### 9. `orders`

* Ordenes realizadas por los usuarios.
* Puede usar cupones y registra descuento aplicado.

#### 10. `order_items`

* Productos dentro de una orden.

#### 11. `payments`

* Registro de pagos, estado, método y comprobantes.

#### 12. `coupons`

* Cupones de descuento con tipo (porcentaje o monto fijo), vigencia y usos.

#### 13. `product_reviews`

* Reseñas por producto: estrellas, comentarios, usuario.

#### 14. `review_images`

* Imágenes asociadas a una reseña.

#### 15. `permissions`

* Permisos por rol para tablas CRUD.

#### 16. `audit_log`

* Bitácora de cambios con datos antiguos/nuevos, IP y user agent.

---

### 🔧 Buenas Prácticas Implementadas

* **Tipos ENUM:** Para controlar valores restringidos y evitar errores de entrada.
* **Soft delete:** En productos para conservar historial sin eliminar registros.
* **Auditoría:** Registro detallado de cambios con JSON.
* **Índices sugeridos:**

  ```sql
  CREATE INDEX idx_product_reviews_product_id ON product_reviews(product_id);
  CREATE INDEX idx_cart_items_user_id ON cart_items(user_id);
  ```
* **Trigger `updated_at`:** Mantenimiento automático del timestamp en `users`.
* **Integridad referencial:** Uso extensivo de claves foráneas `ON DELETE CASCADE`.
* **Restricción de reseñas duplicadas:** `UNIQUE(product_id, user_id)`

---

### 📆 Escalabilidad y Futuras Extensiones

* Agregar soporte para envíos / tracking de pedidos.
* Panel de administración con control de permisos por tabla (usando `permissions`).
* Lógica para bloquear reseñas hasta que el producto haya sido entregado.
* Agregar tabla `product_variants` para gestionar tallas y colores.

---

### 📄 Conclusión

Esta base de datos está lista para soportar un e-commerce profesional, seguro, escalable y extensible. Se ha seguido un diseño modular que separa bien responsabilidades y garantiza integridad en todos los procesos.
