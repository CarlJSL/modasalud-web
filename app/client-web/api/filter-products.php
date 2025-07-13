<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../../dashboard-web/model/productModel.php';

use App\Model\ProductModel;

try {
    $productModel = new ProductModel($pdo, 'products');
    
    // Parámetros de paginación
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
    $offset = ($page - 1) * $limit;
    
    // Parámetros de filtro
    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';
    $subcategory = $_GET['subcategory'] ?? '';
    $price = $_GET['price'] ?? '';
    $size = $_GET['size'] ?? '';
    $stock_status = $_GET['stock_status'] ?? 'all';
    
    // Construir filtros
    $filters = ['status' => 'ACTIVE']; // Solo productos activos
    
    if ($category) {
        // Convertir ID a nombre de categoría si es necesario
        if (is_numeric($category)) {
            $catStmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
            $catStmt->execute([$category]);
            $catName = $catStmt->fetchColumn();
            if ($catName) {
                $filters['category'] = $catName;
            } else {
                $filters['category_id'] = $category; // Fallback a filtrar por ID
            }
        } else {
            $filters['category'] = $category;
        }
    }
    
    if ($subcategory) {
        // Convertir ID a nombre de subcategoría si es necesario
        if (is_numeric($subcategory)) {
            $subCatStmt = $pdo->prepare("SELECT name FROM product_categories WHERE id = ?");
            $subCatStmt->execute([$subcategory]);
            $subCatName = $subCatStmt->fetchColumn();
            if ($subCatName) {
                $filters['product_category'] = $subCatName;
            } else {
                $filters['product_category_id'] = $subcategory; // Fallback a filtrar por ID
            }
        } else {
            $filters['product_category'] = $subcategory;
        }
    }
    
    if ($size) {
        $filters['size'] = $size;
    }
    
    // Filtro de precio
    if ($price) {
        switch ($price) {
            case '0-50':
                $filters['price_min'] = 0;
                $filters['price_max'] = 50;
                break;
            case '50-100':
                $filters['price_min'] = 50;
                $filters['price_max'] = 100;
                break;
            case '100-200':
                $filters['price_min'] = 100;
                $filters['price_max'] = 200;
                break;
            case '200+':
                $filters['price_min'] = 200;
                break;
        }
    }
    
    // Filtro por estado de stock
    if ($stock_status !== 'all') {
        $filters['stock_status'] = $stock_status;
    }
    
    // Obtener productos
    $products = $productModel->getAll($limit, $offset, $search, $filters);
    
    // Obtener conteo total para paginación
    $totalProducts = $productModel->getTotalCount($search, $filters);
    
    // Calcular total de páginas
    $totalPages = ceil($totalProducts / $limit);
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'pagination' => [
            'total' => $totalProducts,
            'page' => $page,
            'limit' => $limit,
            'pages' => $totalPages
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos: ' . $e->getMessage()
    ]);
}
?>
