# 🛍️ E-Commerce Moda y Salud

> Una aplicación web moderna para comercio electrónico especializada en productos de moda y salud, desarrollada con PHP, PostgreSQL y TailwindCSS.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-336791?style=for-the-badge&logo=postgresql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.1-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)

## 📋 Tabla de Contenidos

- [✨ Características](#-características)
- [🚀 Instalación](#-instalación)
- [🐳 Docker](#-docker)
- [🔧 Configuración](#-configuración)
- [📊 Base de Datos](#-base-de-datos)
- [🎨 Desarrollo](#-desarrollo)
- [📁 Estructura del Proyecto](#-estructura-del-proyecto)
- [🤝 Contribución](#-contribución)

## ✨ Características

### 🎯 Funcionalidades Principales
- **Dashboard Administrativo** completo con autenticación
- **Gestión de Productos** - CRUD completo con categorías
- **Sistema de Usuarios** - Login seguro con roles
- **Gestión de Órdenes** - Seguimiento completo del proceso de compra
- **Análisis de Ventas** - Reportes y estadísticas
- **Generación de PDF** - Facturas y reportes automáticos

### 🛠️ Tecnologías
- **Backend**: PHP 8.2 con programación orientada a objetos
- **Base de Datos**: PostgreSQL 15
- **Frontend**: TailwindCSS 4.1 para diseño responsive
- **Contenedores**: Docker con docker-compose
- **PDF**: DomPDF para generación de documentos

## 🚀 Instalación

### Prerequisitos
- Docker & Docker Compose
- Git

### Instalación Rápida

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/e-commerce-moda-salud.git
cd e-commerce-moda-salud

# Configurar variables de entorno
cp .env.template .env

# Construir y ejecutar con Docker
docker-compose up --build -d

# Instalar dependencias PHP (ejecutar en el contenedor)
docker-compose exec app composer install

# Compilar CSS
npm install
npm run dev
```

## 🐳 Docker

El proyecto incluye una configuración completa de Docker con:

- **PHP 8.2** con extensiones necesarias
- **PostgreSQL 15** como base de datos
- **pgAdmin** para administración de BD
- **Nginx** como servidor web

### Comandos Útiles

```bash
# Levantar todos los servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Ejecutar comandos en el contenedor PHP
docker-compose exec app php -v

# Parar servicios
docker-compose down
```

## 🔧 Configuración

### Variables de Entorno

Renombra el archivo `.env.template` por `.env` y configura:

```env
# Base de datos
POSTGRES_DB=tienda_db
POSTGRES_USER=admin
POSTGRES_PASSWORD=admin123
POSTGRES_PORT=5432

# Aplicación
APP_PORT=8080

# pgAdmin
PGADMIN_EMAIL=admin@admin.com
PGADMIN_PASSWORD=admin
```

### Configuración de Base de Datos

```php
// config/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tienda_db');
define('DB_USER', 'admin');
define('DB_PASS', 'admin123');
```

## 📊 Base de Datos

### Instalación de Esquema

```bash
# Ejecutar script principal
psql -U admin -d tienda_db -f db/tienda.sql

# Datos de ejemplo (opcional)
psql -U admin -d tienda_db -f db/datos_usuarios_prueba.sql
psql -U admin -d tienda_db -f db/datos_ejemplo_ordenes.sql
```

### Esquema Principal

El proyecto incluye las siguientes tablas:
- `users` - Usuarios del sistema
- `categories` - Categorías de productos
- `products` - Productos del catálogo
- `orders` - Órdenes de compra
- `order_items` - Items de cada orden

## 🎨 Desarrollo

### Compilar CSS

```bash
# Desarrollo (watch mode)
npm run dev

# Producción
npm run build
```

### Estructura de Archivos CSS

```
app/css/
├── input.css    # Archivo fuente con @tailwind
├── output.css   # CSS compilado (no editar)
└── style.css    # Estilos personalizados
```

### Acceso al Sistema

- **Frontend**: http://localhost:8080
- **Dashboard**: http://localhost:8080/app/dashboard-web/
- **pgAdmin**: http://localhost:5050

### Credenciales por Defecto

```
Usuario: admin
Contraseña: admin123
```

## 📁 Estructura del Proyecto

```
e-commerce-moda-salud/
├── app/
│   ├── client-web/          # Frontend cliente
│   ├── dashboard-web/       # Panel administrativo
│   │   ├── categories/      # Gestión de categorías
│   │   ├── products/        # Gestión de productos
│   │   ├── users/           # Gestión de usuarios
│   │   ├── orden/           # Gestión de órdenes
│   │   └── ventas/          # Análisis de ventas
│   ├── conexion/            # Conexión a BD
│   ├── css/                 # Estilos CSS
│   └── model/               # Modelos de datos
├── config/                  # Configuración
├── db/                      # Scripts de BD
├── docker/                  # Configuración Docker
├── documentation/           # Documentación
└── vendor/                  # Dependencias PHP
```

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-feature`)
3. Commit tus cambios (`git commit -m 'Añadir nueva feature'`)
4. Push a la rama (`git push origin feature/nueva-feature`)
5. Abre un Pull Request

---

<div align="center">
  <p>Desarrollado con ❤️ para el comercio electrónico moderno</p>
  <p>
    <a href="#top">⬆️ Volver arriba</a>
  </p>
</div>

```
docker exec -it php-app bash
```
#### Ejemplo
