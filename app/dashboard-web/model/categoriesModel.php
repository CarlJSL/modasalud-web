<?php

namespace App\Model;

use PDO;
use Exception;
use PDOException;

class CategoriesModel
{
    protected $pdo;
    protected $categoriesTable;
    protected $productCategoriesTable;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->categoriesTable = 'categories';
        $this->productCategoriesTable = 'product_categories';
    }

    /**
     * Obtener todas las categorías con búsqueda opcional, paginados.
     */
    public function getAllCategories(int $limit = 10, int $offset = 0, string $search = '', array $filters = [])
    {
        $sql = "SELECT 
                c.id,
                c.name,
                c.description,
                COUNT(DISTINCT pcm.product_id) AS product_count
            FROM {$this->categoriesTable} c
            LEFT JOIN product_category_mapping pcm ON c.id = pcm.category_id
            WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre o descripción
        if ($search !== '') {
            $conditions[] = "(c.name ILIKE :search OR c.description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Agregar condiciones al SQL
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY c.id, c.name, c.description ORDER BY c.name ASC LIMIT :limit OFFSET :offset";

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
     * Obtener todas las subcategorías con búsqueda opcional, paginados.
     */
    public function getAllSubcategories(int $limit = 10, int $offset = 0, string $search = '', array $filters = [])
    {
        $sql = "SELECT 
                pc.id,
                pc.name,
                pc.description,
                pc.created_at,
                COUNT(DISTINCT pcm.product_id) AS product_count
            FROM {$this->productCategoriesTable} pc
            LEFT JOIN product_category_mapping pcm ON pc.id = pcm.product_category_id
            WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre o descripción
        if ($search !== '') {
            $conditions[] = "(pc.name ILIKE :search OR pc.description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Agregar condiciones al SQL
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY pc.id, pc.name, pc.description, pc.created_at ORDER BY pc.name ASC LIMIT :limit OFFSET :offset";

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
     * Contar total de categorías con filtros
     */
    public function countCategories($search = '', $filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->categoriesTable} c WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre o descripción
        if ($search !== '') {
            $conditions[] = "(c.name ILIKE :search OR c.description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Agregar condiciones WHERE si existen
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Contar total de subcategorías con filtros
     */
    public function countSubcategories($search = '', $filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->productCategoriesTable} pc WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre o descripción
        if ($search !== '') {
            $conditions[] = "(pc.name ILIKE :search OR pc.description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Agregar condiciones WHERE si existen
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Obtener una categoría por ID
     */
    public function getCategoryById($id)
    {
        $sql = "SELECT c.id, c.name, c.description FROM {$this->categoriesTable} c WHERE c.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una subcategoría por ID
     */
    public function getSubcategoryById($id)
    {
        $sql = "SELECT pc.id, pc.name, pc.description, pc.created_at FROM {$this->productCategoriesTable} pc WHERE pc.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una categoría con información detallada
     */
    public function getCategoryDetailedById($id)
    {
        $sql = "SELECT 
                c.id,
                c.name,
                c.description,
                COUNT(DISTINCT pcm.product_id) AS product_count,
                COUNT(DISTINCT pc.id) AS subcategories_count
            FROM {$this->categoriesTable} c
            LEFT JOIN product_category_mapping pcm ON c.id = pcm.category_id
            LEFT JOIN product_categories pc ON pcm.product_category_id = pc.id
            WHERE c.id = :id
            GROUP BY c.id, c.name, c.description";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            // Obtener subcategorías asociadas
            $subCatSql = "SELECT DISTINCT pc.id, pc.name 
                         FROM product_categories pc
                         INNER JOIN product_category_mapping pcm ON pc.id = pcm.product_category_id
                         WHERE pcm.category_id = :id
                         ORDER BY pc.name";
            $subCatStmt = $this->pdo->prepare($subCatSql);
            $subCatStmt->execute(['id' => $id]);
            $category['subcategories'] = $subCatStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $category;
    }

    /**
     * Obtener una subcategoría con información detallada
     */
    public function getSubcategoryDetailedById($id)
    {
        $sql = "SELECT 
                pc.id,
                pc.name,
                pc.description,
                pc.created_at,
                COUNT(DISTINCT pcm.product_id) AS product_count
            FROM {$this->productCategoriesTable} pc
            LEFT JOIN product_category_mapping pcm ON pc.id = pcm.product_category_id
            WHERE pc.id = :id
            GROUP BY pc.id, pc.name, pc.description, pc.created_at";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $subcategory = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($subcategory) {
            $subcategory['time_ago'] = $this->timeAgo($subcategory['created_at']);
        }

        return $subcategory;
    }

    /**
     * Crear una nueva categoría
     */
    public function createCategory(array $data)
    {
        try {
            $sql = "INSERT INTO {$this->categoriesTable} (name, description) VALUES (:name, :description)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':description', $data['description'] ?? null);
            $result = $stmt->execute();

            if ($result) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Crear una nueva subcategoría
     */
    public function createSubcategory(array $data)
    {
        try {
            $sql = "INSERT INTO {$this->productCategoriesTable} (name, description, created_at) VALUES (:name, :description, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':description', $data['description'] ?? null);
            $result = $stmt->execute();

            if ($result) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Actualizar una categoría
     */
    public function updateCategory(int $id, array $data)
    {
        try {
            $sql = "UPDATE {$this->categoriesTable} SET name = :name, description = :description WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':description', $data['description'] ?? null);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Actualizar una subcategoría
     */
    public function updateSubcategory(int $id, array $data)
    {
        try {
            $sql = "UPDATE {$this->productCategoriesTable} SET name = :name, description = :description WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':description', $data['description'] ?? null);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Eliminar una categoría (verificando que no tenga productos asociados)
     */
    public function deleteCategory(int $id)
    {
        try {
            // Verificar si hay productos asociados
            $checkSql = "SELECT COUNT(*) FROM product_category_mapping WHERE category_id = :id";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute(['id' => $id]);
            $productCount = $checkStmt->fetchColumn();

            if ($productCount > 0) {
                throw new Exception('No se puede eliminar la categoría porque tiene productos asociados');
            }

            $sql = "DELETE FROM {$this->categoriesTable} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Eliminar una subcategoría (verificando que no tenga productos asociados)
     */
    public function deleteSubcategory(int $id)
    {
        try {
            // Verificar si hay productos asociados
            $checkSql = "SELECT COUNT(*) FROM product_category_mapping WHERE product_category_id = :id";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute(['id' => $id]);
            $productCount = $checkStmt->fetchColumn();

            if ($productCount > 0) {
                throw new Exception('No se puede eliminar la subcategoría porque tiene productos asociados');
            }

            $sql = "DELETE FROM {$this->productCategoriesTable} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Verificar si un nombre de categoría ya existe
     */
    public function categoryNameExists($name, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->categoriesTable} WHERE name = :name";
        $params = [':name' => $name];

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si un nombre de subcategoría ya existe
     */
    public function subcategoryNameExists($name, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->productCategoriesTable} WHERE name = :name";
        $params = [':name' => $name];

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener estadísticas generales
     */
    public function getGeneralStats()
    {
        $stats = [];

        // Total de categorías
        $sql = "SELECT COUNT(*) FROM {$this->categoriesTable}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['total_categories'] = $stmt->fetchColumn();

        // Total de subcategorías
        $sql = "SELECT COUNT(*) FROM {$this->productCategoriesTable}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['total_subcategories'] = $stmt->fetchColumn();

        // Categorías con productos
        $sql = "SELECT COUNT(DISTINCT category_id) FROM product_category_mapping";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['categories_with_products'] = $stmt->fetchColumn();

        // Subcategorías con productos
        $sql = "SELECT COUNT(DISTINCT product_category_id) FROM product_category_mapping";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['subcategories_with_products'] = $stmt->fetchColumn();

        return $stats;
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
