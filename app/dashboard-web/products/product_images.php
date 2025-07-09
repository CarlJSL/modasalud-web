<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php');
    exit();
}

require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/productModel.php';

use App\Model\ProductModel;

$model = new ProductModel($pdo, 'products');

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        switch ($action) {
            case 'upload':
                if (!isset($_POST['product_id']) || empty($_FILES['image'])) {
                    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                    exit;
                }

                $productId = (int)$_POST['product_id'];
                $file = $_FILES['image'];

                // Verificar que el producto existe
                $product = $model->getById($productId);
                if (!$product) {
                    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                    exit;
                }

                try {
                    $result = $model->uploadProductImage($file, $productId);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Imagen subida exitosamente',
                        'data' => $result
                    ]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'get_images':
                $productId = (int)$_GET['product_id'];
                $images = $model->getProductImages($productId);
                echo json_encode(['success' => true, 'images' => $images]);
                break;

            case 'delete':
                if (!isset($_POST['image_id'])) {
                    echo json_encode(['success' => false, 'message' => 'ID de imagen requerido']);
                    exit;
                }

                $imageId = (int)$_POST['image_id'];
                $result = $model->deleteProductImage($imageId);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Imagen eliminada exitosamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar imagen']);
                }
                break;

            case 'get_main_image':
                $productId = (int)$_GET['product_id'];
                $mainImage = $model->getMainProductImage($productId);
                echo json_encode(['success' => true, 'main_image' => $mainImage]);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

    exit;
}

// Si no es AJAX, redirigir
header('Location: productos.php');
exit;
