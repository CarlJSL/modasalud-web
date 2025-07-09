<?php
/**
 * Configuración para gestión de imágenes de productos
 */

// Configuración de upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('UPLOAD_DIR', __DIR__ . '/../app/uploads/products/');
define('UPLOAD_URL', 'uploads/products/');

// Configuración de redimensionamiento
define('MAX_WIDTH', 800);
define('MAX_HEIGHT', 600);
define('JPEG_QUALITY', 85);
define('PNG_COMPRESSION', 6);

// Configuración de seguridad
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Mensajes de error
define('ERROR_MESSAGES', [
    'FILE_TOO_LARGE' => 'El archivo es demasiado grande. Máximo ' . (MAX_FILE_SIZE / (1024*1024)) . 'MB permitido.',
    'INVALID_TYPE' => 'Tipo de archivo no permitido. Solo se permiten JPG, PNG, GIF y WebP.',
    'UPLOAD_FAILED' => 'Error al subir el archivo. Inténtalo de nuevo.',
    'PRODUCT_NOT_FOUND' => 'Producto no encontrado.',
    'IMAGE_NOT_FOUND' => 'Imagen no encontrada.',
    'DELETE_FAILED' => 'Error al eliminar la imagen.',
    'RESIZE_FAILED' => 'Error al redimensionar la imagen.',
    'DIRECTORY_ERROR' => 'Error al crear el directorio de uploads.'
]);

// Configuración de thumbnails (para futuras implementaciones)
define('THUMBNAIL_WIDTH', 150);
define('THUMBNAIL_HEIGHT', 150);
define('THUMBNAIL_DIR', __DIR__ . '/../app/uploads/products/thumbnails/');

// Configuración de placeholder
define('PLACEHOLDER_IMAGE', 'assets/images/product-placeholder.png');

// Configuración de watermark (para futuras implementaciones)
define('WATERMARK_ENABLED', false);
define('WATERMARK_IMAGE', 'assets/images/watermark.png');
define('WATERMARK_POSITION', 'bottom-right');
define('WATERMARK_OPACITY', 50);
