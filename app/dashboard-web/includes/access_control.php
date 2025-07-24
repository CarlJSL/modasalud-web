<?php
/**
 * Control de acceso para las páginas del dashboard
 * 
 * Este archivo debe ser incluido al inicio de cada página del dashboard para verificar
 * si el usuario tiene acceso a la misma según sus permisos.
 * 
 * Uso: require_once __DIR__ . '/../includes/access_control.php';
 */

// Verificar que exista una sesión
if (!isset($_SESSION)) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir al login si no hay sesión
    header('Location: ' . (str_contains($_SERVER['REQUEST_URI'], '/dashboard-web/') 
        ? '../../index.php' 
        : '../index.php'));
    exit();
}

// Incluir el helper de autenticación
require_once __DIR__ . '/auth_helper.php';
global $pdo; // La conexión debe estar disponible

// Obtener el nombre del archivo actual
$currentFile = basename($_SERVER['SCRIPT_FILENAME']);

// Instanciar el helper de autenticación
$auth = new AuthHelper($pdo);

// Verificar si el usuario es admin (siempre tiene acceso)
$isAdmin = $auth->isAdmin();

// Si no es admin, verificar si tiene acceso a esta página específica
if (!$isAdmin && !$auth->canAccessPage($currentFile)) {
    // Redireccionar a una página de acceso denegado o al dashboard principal
    header('Location: ../ventas/analisis.php?error=acceso_denegado');
    exit();
}
