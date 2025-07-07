<?php

namespace App\Model;

use PDO;
use Exception;
use PDOException;

class ProductModel
{
    protected $pdo;
    protected $table;

    public function __construct($pdo, $table = 'products')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Obtener registros con búsqueda opcional, paginados.
     */
    public function getAll(int $limit = 10, int $offset = 0, string $search = '', array $filters = [])
    {
        $sql = "SELECT 
                p.id,
                p.name,
                p.description,
                p.price,
                p.stock,
                p.size,
                p.status,
                p.created_at,
                c.name AS category_name,
                pc.name AS product_category_name
            FROM {$this->table} p
            LEFT JOIN product_category_mapping pcm ON p.id = pcm.product_id
            LEFT JOIN categories c ON pcm.category_id = c.id
            LEFT JOIN product_categories pc ON pcm.product_category_id = pc.id
            WHERE p.deleted_at IS NULL";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre o descripción
        if ($search !== '') {
            $conditions[] = "(p.name ILIKE :search OR p.description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por categoría (nombre)
        if (!empty($filters['category'])) {
            $conditions[] = "c.name = :category";
            $params[':category'] = $filters['category'];
        }

        // Filtro por subcategoría (nombre)
        if (!empty($filters['product_category'])) {
            $conditions[] = "pc.name = :product_category";
            $params[':product_category'] = $filters['product_category'];
        }

        // Filtro por talla
        if (!empty($filters['size'])) {
            $conditions[] = "p.size = :size";
            $params[':size'] = $filters['size'];
        }

        // Filtro por precio mínimo
        if (!empty($filters['price_min'])) {
            $conditions[] = "p.price >= :price_min";
            $params[':price_min'] = $filters['price_min'];
        }

        // Filtro por precio máximo
        if (!empty($filters['price_max'])) {
            $conditions[] = "p.price <= :price_max";
            $params[':price_max'] = $filters['price_max'];
        }

        // Filtro por estado del stock
        if (!empty($filters['stock_status'])) {
            if ($filters['stock_status'] === 'in_stock') {
                $conditions[] = "p.stock > 0";
            } elseif ($filters['stock_status'] === 'out_of_stock') {
                $conditions[] = "p.stock = 0";
            } elseif ($filters['stock_status'] === 'low_stock') {
                $conditions[] = "p.stock > 0 AND p.stock <= 10";
            }
        }

        if (!empty($filters['status'])) {
            $conditions[] = "p.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Agregar condiciones al SQL
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        // Vincular parámetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * Contar total de productos con filtros
     */
    public function count($search = '', $filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} p
         LEFT JOIN product_category_mapping pcm ON p.id = pcm.product_id
            LEFT JOIN categories c ON pcm.category_id = c.id
            LEFT JOIN product_categories pc ON pcm.product_category_id = pc.id";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre o descripción
        if ($search !== '') {
            $conditions[] = "(p.name ILIKE :search OR p.description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por categoría (nombre)
        if (!empty($filters['category'])) {
            $conditions[] = "c.name = :category";
            $params[':category'] = $filters['category'];
        }

        // Filtro por subcategoría (nombre)
        if (!empty($filters['product_category'])) {
            $conditions[] = "pc.name = :product_category";
            $params[':product_category'] = $filters['product_category'];
        }

        // Filtro por talla
        if (!empty($filters['size'])) {
            $conditions[] = "p.size = :size";
            $params[':size'] = $filters['size'];
        }

        // Filtro por precio mínimo
        if (!empty($filters['price_min'])) {
            $conditions[] = "p.price >= :price_min";
            $params[':price_min'] = $filters['price_min'];
        }

        // Filtro por precio máximo
        if (!empty($filters['price_max'])) {
            $conditions[] = "p.price <= :price_max";
            $params[':price_max'] = $filters['price_max'];
        }

        // Filtro por estado del stock
        if (!empty($filters['stock_status'])) {
            if ($filters['stock_status'] === 'in_stock') {
                $conditions[] = "p.stock > 0";
            } elseif ($filters['stock_status'] === 'out_of_stock') {
                $conditions[] = "p.stock = 0";
            } elseif ($filters['stock_status'] === 'low_stock') {
                $conditions[] = "p.stock > 0 AND p.stock <= 10";
            }
        }

        if (!empty($filters['status'])) {
            $conditions[] = "p.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Agregar condiciones WHERE si existen
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Obtener un producto por ID
     */
    public function getById($id)
    {
        $sql = "SELECT  p.id,
                p.name,
                p.description,
                p.price,
                p.stock,
                p.size,
                p.status,
                p.created_at,
                c.id AS category,
                pc.id AS subcategory
                 FROM {$this->table} p 
                 LEFT JOIN product_category_mapping pcm ON p.id = pcm.product_id
            LEFT JOIN categories c ON pcm.category_id = c.id
            LEFT JOIN product_categories pc ON pcm.product_category_id = pc.id
                 WHERE p.id = :id AND p.deleted_at IS NULL";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un producto por ID con información detallada
     */
  public function getDetailedById($id)
{
    $sql = "SELECT 
                p.id,
                p.name,
                p.description,
                p.price,
                p.stock,
                p.size,
                p.status,
                p.created_at,
                c.name AS category_name,
                pc.name AS product_category_name,
                COUNT(DISTINCT oi.order_id) AS times_ordered,
                COUNT(DISTINCT ci.id) AS in_carts,
                ROUND(AVG(r.rating), 1) AS average_rating
            FROM {$this->table} p
            LEFT JOIN product_category_mapping pcm ON p.id = pcm.product_id
            LEFT JOIN categories c ON pcm.category_id = c.id
            LEFT JOIN product_categories pc ON pcm.product_category_id = pc.id
            LEFT JOIN order_items oi ON oi.product_id = p.id
            LEFT JOIN cart_items ci ON ci.product_id = p.id
            LEFT JOIN product_reviews r ON r.product_id = p.id
            WHERE p.deleted_at IS NULL AND p.id = :id
            GROUP BY p.id, c.name, pc.name";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $product['stock_status'] = $this->getStockStatus($product['stock']);
        $product['time_ago'] = $this->timeAgo($product['created_at']);

        // Obtener imágenes
        $imgStmt = $this->pdo->prepare("SELECT image_url FROM product_images WHERE product_id = :id");
        $imgStmt->execute(['id' => $id]);
        $product['images'] = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
    }

    return $product;
}




    /**
     * Crear un nuevo producto
     */
    public function create(array $data)
    {
        try {
            // Iniciar transacción
            $this->pdo->beginTransaction();

            // Insertar producto en products
            $sql = "INSERT INTO {$this->table} (name, description, price, stock, size, status, created_at)
                VALUES (:name, :description, :price, :stock, :size, :status, NOW())";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':price', $data['price']);
            $stmt->bindValue(':stock', $data['stock'], PDO::PARAM_INT);
            $stmt->bindValue(':size', $data['size']);

            // Lógica para establecer el estado según el stock
            $status = $data['status'] ?? 'ACTIVE';
            if (isset($data['stock']) && (int)$data['stock'] === 0) {
                $status = 'OUT_OF_STOCK';
            }
            $stmt->bindValue(':status', $status);


            $result = $stmt->execute();

            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }

            // Obtener ID del producto recién insertado
            $productId = $this->pdo->lastInsertId();

            // Insertar en product_category_mapping
            $mappingSql = "INSERT INTO product_category_mapping (product_id, category_id, product_category_id, created_at)
                       VALUES (:product_id, :category_id, :product_category_id, NOW())";

            $mappingStmt = $this->pdo->prepare($mappingSql);
            $mappingStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $mappingStmt->bindValue(':category_id', $data['category'], PDO::PARAM_INT); // ID de category
            $mappingStmt->bindValue(':product_category_id', $data['subcategory'], PDO::PARAM_INT); // ID de subcategoría

            $mappingResult = $mappingStmt->execute();

            if (!$mappingResult) {
                $this->pdo->rollBack();
                return false;
            }

            // Confirmar transacción
            $this->pdo->commit();
            return $productId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }



    /**
     * Actualizar un producto
     */
    public function update(int $id, array $data)
    {
        try {
            $this->pdo->beginTransaction();

            $fields = [];
            $params = [];

            if (isset($data['name'])) {
                $fields[] = "name = :name";
                $params[':name'] = $data['name'];
            }

            if (isset($data['description'])) {
                $fields[] = "description = :description";
                $params[':description'] = $data['description'];
            }

            if (isset($data['price'])) {
                $fields[] = "price = :price";
                $params[':price'] = $data['price'];
            }

            // Controlar stock y status juntos
            if (isset($data['stock'])) {
                $fields[] = "stock = :stock";
                $params[':stock'] = $data['stock'];

                // Si no viene status manual, lo calculamos por stock
                if (!isset($data['status'])) {
                    $status = ((int)$data['stock'] > 0) ? 'ACTIVE' : 'OUT_OF_STOCK';
                    $fields[] = "status = :status";
                    $params[':status'] = $status;
                }
            }

            // Solo usar status si no se estableció anteriormente
            if (isset($data['status']) && !array_key_exists(':status', $params)) {
                $fields[] = "status = :status";
                $params[':status'] = $data['status'];
            }

            if (isset($data['size'])) {
                $fields[] = "size = :size";
                $params[':size'] = $data['size'];
            }

            $fields[] = "updated_at = NOW()";

            if (empty($fields)) {
                return false; // Nada que actualizar
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
            $params[':id'] = $id;

            $stmt = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                if (in_array($key, [':price', ':stock', ':id'])) {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }

            $result = $stmt->execute();
            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }

            // Actualizar categoría y subcategoría si se proporcionan
            if (isset($data['category']) && isset($data['subcategory'])) {
                $mappingSql = "UPDATE product_category_mapping
                        SET category_id = :category_id,
                            product_category_id = :product_category_id,
                            updated_at = NOW()
                        WHERE product_id = :product_id";

                $mappingStmt = $this->pdo->prepare($mappingSql);
                $mappingStmt->bindValue(':category_id', $data['category'], PDO::PARAM_INT);
                $mappingStmt->bindValue(':product_category_id', $data['subcategory'], PDO::PARAM_INT);
                $mappingStmt->bindValue(':product_id', $id, PDO::PARAM_INT);

                $mappingResult = $mappingStmt->execute();

                if (!$mappingResult) {
                    $this->pdo->rollBack();
                    return false;
                }
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }



    /**
     * Eliminar (soft delete) un producto
     */


    public function softDelete(int $id, int $statusValue)
    {
        // Convertir 1/0 en 'ACTIVE'/'INACTIVE'
        $status = ($statusValue === 1) ? 'ACTIVE' : 'INACTIVE';

        $sql = "UPDATE {$this->table} 
            SET status = :status,  updated_at = NOW()
            WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    /**
     * Verificar si un producto ya existe por nombre
     */
    public function nameExists($name, $size, $description, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
            WHERE name = :name 
              AND size = :size 
              AND description = :description 
              AND deleted_at IS NULL";

        $params = [
            'name' => $name,
            'size' => $size,
            'description' => $description
        ];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }


    /**
     * Obtener categorías disponibles
     */
    public function getCategories()
    {
        $sql = "SELECT DISTINCT id, name FROM categories ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubCategories()
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT id, name FROM product_categories ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener tallas disponibles
     */
    public function getSizes()
    {
        return [
            ['name' => 'XS'],
            ['name' => 'S'],
            ['name' => 'M'],
            ['name' => 'L'],
            ['name' => 'XL'],
            ['name' => 'XXL'],
            ['name' => 'UNIQUE']
        ];
    }

    /**
     * Obtener estadísticas generales
     */
    public function getGeneralStats()
    {
        $stats = [];

        // Total de productos
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['total_products'] = $stmt->fetchColumn();

        // Productos en stock
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE stock > 0 AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['in_stock'] = $stmt->fetchColumn();

        // Productos sin stock
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE stock = 0 AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['out_of_stock'] = $stmt->fetchColumn();

        // Productos con stock bajo
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE stock > 0 AND stock <= 10 AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['low_stock'] = $stmt->fetchColumn();

        // Valor total del inventario
        $sql = "SELECT SUM(price * stock) FROM {$this->table} WHERE deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['total_value'] = $stmt->fetchColumn() ?: 0;

        return $stats;
    }

    /**
     * Obtener estado del stock
     */
    private function getStockStatus($stock)
    {
        if ($stock == 0) {
            return 'out_of_stock';
        } elseif ($stock <= 10) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Calcular tiempo transcurrido
     */
    private function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) return 'hace menos de 1 minuto';
        if ($time < 3600) return 'hace ' . floor($time / 60) . ' minutos';
        if ($time < 86400) return 'hace ' . floor($time / 3600) . ' horas';
        if ($time < 2592000) return 'hace ' . floor($time / 86400) . ' días';
        if ($time < 31536000) return 'hace ' . floor($time / 2592000) . ' meses';
        return 'hace ' . floor($time / 31536000) . ' años';
    }
}
