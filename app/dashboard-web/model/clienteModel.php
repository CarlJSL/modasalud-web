<?php

namespace App\Model;

use PDO;
use Exception;
use PDOException;

class ClienteModel
{
    protected $pdo;
    protected $table;

    public function __construct($pdo, $table = 'clients')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Obtener todos los clientes con información relacionada
     */
    public function getAll(int $limit = 10, int $offset = 0, string $search = '', array $filters = [])
    {
        $sql = "SELECT 
                c.id,
                c.name,
                c.email,
                c.phone,
                c.dni,
                c.gender,
                c.birth_date,
                c.status,
                c.created_at,
                c.updated_at,
                -- Información de direcciones
                COUNT(DISTINCT ca.id) AS total_addresses,
                ca_default.address AS default_address,
                ca_default.city AS default_city,
                ca_default.region AS default_region,
                -- Información de órdenes
                COUNT(DISTINCT o.id) AS total_orders,
                COALESCE(SUM(CASE WHEN o.status = 'COMPLETED' THEN o.total_price ELSE 0 END), 0) AS total_spent,
                MAX(o.created_at) AS last_order_date,
                COUNT(DISTINCT CASE WHEN o.status = 'PENDING' THEN o.id END) AS pending_orders,
                COUNT(DISTINCT CASE WHEN o.status = 'COMPLETED' THEN o.id END) AS completed_orders,
                COUNT(DISTINCT CASE WHEN o.status = 'CANCELLED' THEN o.id END) AS cancelled_orders,
                -- Información del carrito
                COUNT(DISTINCT ci.id) AS cart_items_count,
                COALESCE(SUM(ci.quantity), 0) AS cart_total_quantity,
                -- Información de reseñas
                COUNT(DISTINCT pr.id) AS total_reviews,
                ROUND(AVG(pr.rating), 2) AS avg_review_rating,
                -- Calcular tiempo desde la última actividad
                GREATEST(
                    COALESCE(MAX(o.created_at), c.created_at),
                    COALESCE(MAX(ci.added_at), c.created_at),
                    COALESCE(MAX(pr.created_at), c.created_at)
                ) AS last_activity
            FROM {$this->table} c
            LEFT JOIN client_addresses ca ON c.id = ca.client_id
            LEFT JOIN client_addresses ca_default ON c.id = ca_default.client_id AND ca_default.is_default = true
            LEFT JOIN orders o ON c.id = o.client_id
            LEFT JOIN cart_items ci ON c.id = ci.client_id
            LEFT JOIN product_reviews pr ON c.id = pr.client_id
            WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre, email, teléfono o DNI
        if ($search !== '') {
            $conditions[] = "(c.name ILIKE :search OR c.email ILIKE :search OR c.phone ILIKE :search OR c.dni ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por estado
        if (!empty($filters['status'])) {
            $conditions[] = "c.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filtro por género
        if (!empty($filters['gender'])) {
            $conditions[] = "c.gender = :gender";
            $params[':gender'] = $filters['gender'];
        }

        // Filtro por fecha de registro desde
        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(c.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        // Filtro por fecha de registro hasta
        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(c.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        // Filtro por gasto mínimo
        if (!empty($filters['min_spent'])) {
            $conditions[] = "COALESCE(SUM(CASE WHEN o.status = 'COMPLETED' THEN o.total_price ELSE 0 END), 0) >= :min_spent";
            $params[':min_spent'] = $filters['min_spent'];
        }

        // Filtro por número mínimo de órdenes
        if (!empty($filters['min_orders'])) {
            $conditions[] = "COUNT(DISTINCT o.id) >= :min_orders";
            $params[':min_orders'] = $filters['min_orders'];
        }

        // Filtro por clientes con carrito activo
        if (!empty($filters['has_cart'])) {
            $conditions[] = "COUNT(DISTINCT ci.id) > 0";
        }

        // Agregar condiciones al SQL
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY c.id, ca_default.address, ca_default.city, ca_default.region";
        $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        // Vincular parámetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar información adicional procesada
        foreach ($clients as &$client) {
            $client['time_since_registration'] = $this->timeAgo($client['created_at']);
            $client['time_since_last_activity'] = $this->timeAgo($client['last_activity']);
            $client['status_color'] = $this->getStatusColor($client['status']);
            $client['customer_tier'] = $this->getCustomerTier($client['total_spent'], $client['total_orders']);
            $client['age'] = $this->calculateAge($client['birth_date']);
        }

        return $clients;
    }

    /**
     * Contar total de clientes con filtros
     */
    public function count(string $search = '', array $filters = [])
    {
        $sql = "SELECT COUNT(DISTINCT c.id) FROM {$this->table} c
                LEFT JOIN orders o ON c.id = o.client_id
                LEFT JOIN cart_items ci ON c.id = ci.client_id
                WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre, email, teléfono o DNI
        if ($search !== '') {
            $conditions[] = "(c.name ILIKE :search OR c.email ILIKE :search OR c.phone ILIKE :search OR c.dni ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por estado
        if (!empty($filters['status'])) {
            $conditions[] = "c.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filtro por género
        if (!empty($filters['gender'])) {
            $conditions[] = "c.gender = :gender";
            $params[':gender'] = $filters['gender'];
        }

        // Filtro por fecha de registro desde
        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(c.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        // Filtro por fecha de registro hasta
        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(c.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
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
     * Obtener un cliente por ID
     */
    public function getById($id)
    {
        $sql = "SELECT 
                c.id,
                c.name,
                c.email,
                c.phone,
                c.dni,
                c.gender,
                c.birth_date,
                c.status,
                c.created_at,
                c.updated_at
            FROM {$this->table} c
            WHERE c.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener información detallada de un cliente
     */
    public function getDetailedById($id)
    {
        $client = $this->getById($id);

        if (!$client) {
            return null;
        }

        // Obtener direcciones del cliente
        $addressesStmt = $this->pdo->prepare("
            SELECT 
                id,
                address,
                city,
                region,
                postal_code,
                phone,
                is_default,
                created_at,
                updated_at
            FROM client_addresses
            WHERE client_id = :client_id
            ORDER BY is_default DESC, created_at DESC
        ");
        $addressesStmt->execute(['client_id' => $id]);
        $client['addresses'] = $addressesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener órdenes del cliente
        $ordersStmt = $this->pdo->prepare("
            SELECT 
                o.id,
                o.total_price,
                o.status,
                o.created_at,
                o.discount_amount,
                o.order_source,
                p.method AS payment_method,
                p.status AS payment_status,
                p.paid_at,
                COUNT(oi.product_id) AS total_items,
                SUM(oi.quantity) AS total_quantity
            FROM orders o
            LEFT JOIN payments p ON o.id = p.order_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.client_id = :client_id
            GROUP BY o.id, p.method, p.status, p.paid_at
            ORDER BY o.created_at DESC
            LIMIT 10
        ");
        $ordersStmt->execute(['client_id' => $id]);
        $client['recent_orders'] = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener items del carrito actual
        $cartStmt = $this->pdo->prepare("
            SELECT 
                ci.id,
                ci.product_id,
                ci.quantity,
                ci.added_at,
                ci.selected,
                p.name AS product_name,
                p.price AS product_price,
                p.stock AS product_stock,
                (ci.quantity * p.price) AS subtotal
            FROM cart_items ci
            LEFT JOIN products p ON ci.product_id = p.id
            WHERE ci.client_id = :client_id
            ORDER BY ci.added_at DESC
        ");
        $cartStmt->execute(['client_id' => $id]);
        $client['cart_items'] = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener reseñas del cliente
        $reviewsStmt = $this->pdo->prepare("
            SELECT 
                pr.id,
                pr.product_id,
                pr.rating,
                pr.comment,
                pr.created_at,
                p.name AS product_name
            FROM product_reviews pr
            LEFT JOIN products p ON pr.product_id = p.id
            WHERE pr.client_id = :client_id
            ORDER BY pr.created_at DESC
            LIMIT 5
        ");
        $reviewsStmt->execute(['client_id' => $id]);
        $client['recent_reviews'] = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular estadísticas del cliente
        $statsStmt = $this->pdo->prepare("
            SELECT 
                COUNT(DISTINCT o.id) AS total_orders,
                COALESCE(SUM(CASE WHEN o.status = 'COMPLETED' THEN o.total_price ELSE 0 END), 0) AS total_spent,
                COALESCE(AVG(CASE WHEN o.status = 'COMPLETED' THEN o.total_price ELSE NULL END), 0) AS avg_order_value,
                COUNT(DISTINCT CASE WHEN o.status = 'PENDING' THEN o.id END) AS pending_orders,
                COUNT(DISTINCT CASE WHEN o.status = 'COMPLETED' THEN o.id END) AS completed_orders,
                COUNT(DISTINCT CASE WHEN o.status = 'CANCELLED' THEN o.id END) AS cancelled_orders,
                COUNT(DISTINCT ci.id) AS cart_items_count,
                COALESCE(SUM(ci.quantity), 0) AS cart_total_quantity,
                COUNT(DISTINCT pr.id) AS total_reviews,
                ROUND(AVG(pr.rating), 2) AS avg_review_rating,
                MAX(o.created_at) AS last_order_date
            FROM clients c
            LEFT JOIN orders o ON c.id = o.client_id
            LEFT JOIN cart_items ci ON c.id = ci.client_id
            LEFT JOIN product_reviews pr ON c.id = pr.client_id
            WHERE c.id = :client_id
            GROUP BY c.id
        ");
        $statsStmt->execute(['client_id' => $id]);
        $client['statistics'] = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // Agregar información adicional procesada
        $client['age'] = $this->calculateAge($client['birth_date']);
        $client['time_since_registration'] = $this->timeAgo($client['created_at']);
        $client['status_color'] = $this->getStatusColor($client['status']);
        $client['customer_tier'] = $this->getCustomerTier(
            $client['statistics']['total_spent'], 
            $client['statistics']['total_orders']
        );

        // Agregar tiempo desde la última orden
        if ($client['statistics']['last_order_date']) {
            $client['time_since_last_order'] = $this->timeAgo($client['statistics']['last_order_date']);
        }

        return $client;
    }

    /**
     * Crear un nuevo cliente
     */
    public function create(array $data)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO {$this->table} (name, email, phone, dni, gender, birth_date, status, created_at, updated_at)
                    VALUES (:name, :email, :phone, :dni, :gender, :birth_date, :status, NOW(), NOW())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':phone', $data['phone'] ?? null);
            $stmt->bindValue(':dni', $data['dni'] ?? null);
            $stmt->bindValue(':gender', $data['gender'] ?? null);
            $stmt->bindValue(':birth_date', $data['birth_date'] ?? null);
            $stmt->bindValue(':status', $data['status'] ?? 'ACTIVE');

            $result = $stmt->execute();

            if (!$result) {
                throw new Exception('Error al crear el cliente');
            }

            $clientId = $this->pdo->lastInsertId();

            // Crear dirección por defecto si se proporciona
            if (!empty($data['address'])) {
                $addressSql = "INSERT INTO client_addresses (client_id, address, city, region, postal_code, phone, is_default, created_at, updated_at)
                              VALUES (:client_id, :address, :city, :region, :postal_code, :phone, true, NOW(), NOW())";
                
                $addressStmt = $this->pdo->prepare($addressSql);
                $addressStmt->bindValue(':client_id', $clientId, PDO::PARAM_INT);
                $addressStmt->bindValue(':address', $data['address']);
                $addressStmt->bindValue(':city', $data['city'] ?? null);
                $addressStmt->bindValue(':region', $data['region'] ?? null);
                $addressStmt->bindValue(':postal_code', $data['postal_code'] ?? null);
                $addressStmt->bindValue(':phone', $data['address_phone'] ?? $data['phone']);
                $addressStmt->execute();
            }

            $this->pdo->commit();
            return $clientId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar un cliente
     */
    public function update($id, array $data)
    {
        try {
            $sql = "UPDATE {$this->table} SET 
                    name = :name,
                    email = :email,
                    phone = :phone,
                    dni = :dni,
                    gender = :gender,
                    birth_date = :birth_date,
                    status = :status,
                    updated_at = NOW()
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':phone', $data['phone'] ?? null);
            $stmt->bindValue(':dni', $data['dni'] ?? null);
            $stmt->bindValue(':gender', $data['gender'] ?? null);
            $stmt->bindValue(':birth_date', $data['birth_date'] ?? null);
            $stmt->bindValue(':status', $data['status']);

            return $stmt->execute();

        } catch (PDOException $e) {
            throw new Exception('Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un cliente (soft delete)
     */
    public function delete($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET status = 'INACTIVE', updated_at = NOW() WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new Exception('Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas generales de clientes
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                COUNT(*) AS total_clients,
                COUNT(CASE WHEN status = 'ACTIVE' THEN 1 END) AS active_clients,
                COUNT(CASE WHEN status = 'INACTIVE' THEN 1 END) AS inactive_clients,
                COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 END) AS new_today,
                COUNT(CASE WHEN DATE(created_at) >= CURRENT_DATE - INTERVAL '7 days' THEN 1 END) AS new_this_week,
                COUNT(CASE WHEN DATE(created_at) >= CURRENT_DATE - INTERVAL '30 days' THEN 1 END) AS new_this_month
            FROM {$this->table}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener clientes más activos
     */
    public function getTopCustomers($limit = 10)
    {
        $sql = "SELECT 
                c.id,
                c.name,
                c.email,
                COUNT(DISTINCT o.id) AS total_orders,
                COALESCE(SUM(CASE WHEN o.status = 'COMPLETED' THEN o.total_price ELSE 0 END), 0) AS total_spent,
                MAX(o.created_at) AS last_order_date
            FROM {$this->table} c
            LEFT JOIN orders o ON c.id = o.client_id
            WHERE c.status = 'ACTIVE'
            GROUP BY c.id, c.name, c.email
            HAVING COUNT(DISTINCT o.id) > 0
            ORDER BY total_spent DESC, total_orders DESC
            LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar clientes por email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar clientes por DNI
     */
    public function findByDni($dni)
    {
        $sql = "SELECT * FROM {$this->table} WHERE dni = :dni";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['dni' => $dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Métodos auxiliares

    /**
     * Calcular tiempo transcurrido
     */
    private function timeAgo($datetime)
    {
        if (!$datetime) return 'Nunca';
        
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'Hace ' . $time . ' segundos';
        if ($time < 3600) return 'Hace ' . floor($time/60) . ' minutos';
        if ($time < 86400) return 'Hace ' . floor($time/3600) . ' horas';
        if ($time < 2592000) return 'Hace ' . floor($time/86400) . ' días';
        if ($time < 31536000) return 'Hace ' . floor($time/2592000) . ' meses';
        return 'Hace ' . floor($time/31536000) . ' años';
    }

    /**
     * Obtener color según el estado
     */
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'ACTIVE':
                return 'bg-green-100 text-green-800';
            case 'INACTIVE':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Determinar nivel de cliente
     */
    private function getCustomerTier($totalSpent, $totalOrders)
    {
        if ($totalSpent >= 1000 || $totalOrders >= 10) {
            return ['tier' => 'VIP', 'color' => 'bg-purple-100 text-purple-800'];
        } elseif ($totalSpent >= 500 || $totalOrders >= 5) {
            return ['tier' => 'Premium', 'color' => 'bg-blue-100 text-blue-800'];
        } elseif ($totalOrders >= 1) {
            return ['tier' => 'Regular', 'color' => 'bg-green-100 text-green-800'];
        } else {
            return ['tier' => 'Nuevo', 'color' => 'bg-yellow-100 text-yellow-800'];
        }
    }

    /**
     * Calcular edad
     */
    private function calculateAge($birthDate)
    {
        if (!$birthDate) return null;
        
        $birth = new \DateTime($birthDate);
        $today = new \DateTime();
        return $birth->diff($today)->y;
    }
}
