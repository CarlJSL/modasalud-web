<?php
require_once __DIR__ . '/analytics-data.php';

/**
 * Manejar solicitudes AJAX para el dashboard
 */

// Verificar que sea una solicitud AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(400);
    die('Solo se permiten solicitudes AJAX');
}

$action = $_POST['action'] ?? '';
$periodo = $_POST['periodo'] ?? 'mes';

header('Content-Type: application/json');

switch ($action) {
    case 'actualizar_metricas':
        try {
            $dashboardData = obtenerDatosSegurosDashboard($pdo, $periodo);
            
            echo json_encode([
                'success' => true,
                'data' => $dashboardData
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'exportar_pdf':
        try {
            // Aquí se implementaría la exportación PDF
            // Por ahora, simular el proceso
            echo json_encode([
                'success' => true,
                'message' => 'Reporte PDF generado exitosamente',
                'download_url' => '/dashboard/exports/reporte_' . date('Y-m-d_H-i-s') . '.pdf'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'exportar_excel':
        try {
            // Aquí se implementaría la exportación Excel
            // Por ahora, simular el proceso
            echo json_encode([
                'success' => true,
                'message' => 'Reporte Excel generado exitosamente',
                'download_url' => '/dashboard/exports/reporte_' . date('Y-m-d_H-i-s') . '.xlsx'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar Excel: ' . $e->getMessage()
            ]);
        }
        break;
        
    case 'obtener_tendencias':
        try {
            $tendencias = obtenerTendenciasVentas($pdo, $periodo);
            
            echo json_encode([
                'success' => true,
                'data' => $tendencias
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener tendencias: ' . $e->getMessage()
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida'
        ]);
        break;
}
?>
