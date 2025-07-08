<?php

namespace App\Model;

use PDO;
use Exception;
use PDOException;

class OrderModel
{
    protected $pdo;
    protected $table;

    public function __construct($pdo, $table = 'orders')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Obtener órdenes con búsqueda opcional, paginadas.
     */
    public function getAll(int $limit = 10, int $offset = 0, string $search = '', array $filters = [])
    {
        $sql = "SELECT 
                o.id,
                o.total_price,
                o.status,
                o.created_at,
                o.discount_amount,
                c.name AS client_name,
                c.email AS client_email,
                c.phone AS client_phone,
                ca.address AS delivery_address,
                ca.city AS delivery_city,
                ca.region AS delivery_region,
                cp.code AS coupon_code,
                cp.discount_type AS coupon_type,
                cp.discount_value AS coupon_value,
                p.method AS payment_method,
                p.status AS payment_status,
                p.paid_at,
                u.username AS created_by_username,
                u.email AS created_by_email,
                COUNT(DISTINCT oi.product_id) AS total_items,
                SUM(oi.quantity) AS total_quantity
            FROM {$this->table} o
            LEFT JOIN clients c ON o.client_id = c.id
            LEFT JOIN client_addresses ca ON o.address_id = ca.id
            LEFT JOIN coupons cp ON o.coupon_id = cp.id
            LEFT JOIN payments p ON o.id = p.order_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN users u ON o.created_by = u.id
            WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por ID de orden, nombre del cliente o email
        if ($search !== '') {
            $conditions[] = "(o.id::text ILIKE :search OR c.name ILIKE :search OR c.email ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por estado de orden
        if (!empty($filters['status'])) {
            $conditions[] = "o.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filtro por método de pago
        if (!empty($filters['payment_method'])) {
            $conditions[] = "p.method = :payment_method";
            $params[':payment_method'] = $filters['payment_method'];
        }

        // Filtro por estado de pago
        if (!empty($filters['payment_status'])) {
            $conditions[] = "p.status = :payment_status";
            $params[':payment_status'] = $filters['payment_status'];
        }

        // Filtro por fecha desde
        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(o.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        // Filtro por fecha hasta
        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(o.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        // Filtro por precio mínimo
        if (!empty($filters['price_min'])) {
            $conditions[] = "o.total_price >= :price_min";
            $params[':price_min'] = $filters['price_min'];
        }

        // Filtro por precio máximo
        if (!empty($filters['price_max'])) {
            $conditions[] = "o.total_price <= :price_max";
            $params[':price_max'] = $filters['price_max'];
        }

        // Agregar condiciones al SQL
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY o.id, c.name, c.email, c.phone, ca.address, ca.city, ca.region, cp.code, cp.discount_type, cp.discount_value, p.method, p.status, p.paid_at, u.username, u.email";
        $sql .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";

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
     * Contar total de órdenes con filtros
     */
    public function count($search = '', $filters = [])
    {
        $sql = "SELECT COUNT(DISTINCT o.id) FROM {$this->table} o
                LEFT JOIN clients c ON o.client_id = c.id
                LEFT JOIN payments p ON o.id = p.order_id
                WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por ID de orden, nombre del cliente o email
        if ($search !== '') {
            $conditions[] = "(o.id::text ILIKE :search OR c.name ILIKE :search OR c.email ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por estado de orden
        if (!empty($filters['status'])) {
            $conditions[] = "o.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filtro por método de pago
        if (!empty($filters['payment_method'])) {
            $conditions[] = "p.method = :payment_method";
            $params[':payment_method'] = $filters['payment_method'];
        }

        // Filtro por estado de pago
        if (!empty($filters['payment_status'])) {
            $conditions[] = "p.status = :payment_status";
            $params[':payment_status'] = $filters['payment_status'];
        }

        // Filtro por fecha desde
        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(o.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        // Filtro por fecha hasta
        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(o.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        // Filtro por precio mínimo
        if (!empty($filters['price_min'])) {
            $conditions[] = "o.total_price >= :price_min";
            $params[':price_min'] = $filters['price_min'];
        }

        // Filtro por precio máximo
        if (!empty($filters['price_max'])) {
            $conditions[] = "o.total_price <= :price_max";
            $params[':price_max'] = $filters['price_max'];
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
     * Obtener una orden por ID
     */
    public function getById($id)
    {
        $sql = "SELECT 
                o.id,
                o.total_price,
                o.status,
                o.created_at,
                o.discount_amount,
                o.client_id,
                o.address_id,
                o.coupon_id,
                o.created_by,
                c.name AS client_name,
                c.email AS client_email,
                c.phone AS client_phone,
                c.gender AS client_gender,
                ca.address AS delivery_address,
                ca.city AS delivery_city,
                ca.region AS delivery_region,
                ca.postal_code AS delivery_postal_code,
                ca.phone AS delivery_phone,
                cp.code AS coupon_code,
                cp.discount_type AS coupon_type,
                cp.discount_value AS coupon_value,
                u.username AS created_by_username,
                u.email AS created_by_email
            FROM {$this->table} o
            LEFT JOIN clients c ON o.client_id = c.id
            LEFT JOIN client_addresses ca ON o.address_id = ca.id
            LEFT JOIN coupons cp ON o.coupon_id = cp.id
            LEFT JOIN users u ON o.created_by = u.id
            WHERE o.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una orden por ID con información detallada
     */
    public function getDetailedById($id)
    {
        $order = $this->getById($id);

        if (!$order) {
            return null;
        }

        // Obtener items de la orden
        $itemsStmt = $this->pdo->prepare("
            SELECT 
                oi.product_id,
                oi.quantity,
                oi.price,
                p.name AS product_name,
                p.description AS product_description,
                p.size AS product_size,
                p.stock AS product_stock
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $itemsStmt->execute(['order_id' => $id]);
        $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener información de pago
        $paymentStmt = $this->pdo->prepare("
            SELECT 
                method,
                status,
                paid_at,
                proof_url
            FROM payments
            WHERE order_id = :order_id
        ");
        $paymentStmt->execute(['order_id' => $id]);
        $order['payment'] = $paymentStmt->fetch(PDO::FETCH_ASSOC);

        // Agregar información adicional
        $order['time_ago'] = $this->timeAgo($order['created_at']);
        $order['status_color'] = $this->getStatusColor($order['status']);
        $order['payment_status_color'] = $this->getPaymentStatusColor($order['payment']['status'] ?? 'PENDING');

        return $order;
    }

    /**
     * Crear una nueva orden
     */
    public function create(array $data)
    {
        try {
            // Iniciar transacción
            $this->pdo->beginTransaction();

            // Insertar orden
            $sql = "INSERT INTO {$this->table} (client_id, address_id, total_price, status, discount_amount, coupon_id, created_by, created_at)
                    VALUES (:client_id, :address_id, :total_price, :status, :discount_amount, :coupon_id, :created_by, NOW())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':client_id', $data['client_id'], PDO::PARAM_INT);
            $stmt->bindValue(':address_id', $data['address_id'], PDO::PARAM_INT);
            $stmt->bindValue(':total_price', $data['total_price']);
            $stmt->bindValue(':status', $data['status'] ?? 'PENDING');
            $stmt->bindValue(':discount_amount', $data['discount_amount'] ?? 0);
            $stmt->bindValue(':coupon_id', $data['coupon_id'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':created_by', $data['created_by'] ?? null, PDO::PARAM_INT);

            $result = $stmt->execute();

            if (!$result) {
                throw new Exception('Error al crear la orden');
            }

            // Obtener ID de la orden recién creada
            $orderId = $this->pdo->lastInsertId();

            // Insertar items de la orden
            if (!empty($data['items'])) {
                $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
                $itemStmt = $this->pdo->prepare($itemSql);

                foreach ($data['items'] as $item) {
                    $itemStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
                    $itemStmt->bindValue(':product_id', $item['product_id'], PDO::PARAM_INT);
                    $itemStmt->bindValue(':quantity', $item['quantity'], PDO::PARAM_INT);
                    $itemStmt->bindValue(':price', $item['price']);
                    $itemStmt->execute();
                }
            }

            // Crear registro de pago
            if (!empty($data['payment'])) {
                $paymentSql = "INSERT INTO payments (order_id, method, status, paid_at, proof_url) VALUES (:order_id, :method, :status, :paid_at, :proof_url)";
                $paymentStmt = $this->pdo->prepare($paymentSql);
                $paymentStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
                $paymentStmt->bindValue(':method', $data['payment']['method']);
                $paymentStmt->bindValue(':status', $data['payment']['status'] ?? 'PENDING');
                $paymentStmt->bindValue(':paid_at', $data['payment']['paid_at'] ?? null);
                $paymentStmt->bindValue(':proof_url', $data['payment']['proof_url'] ?? null);
                $paymentStmt->execute();
            }

            // Confirmar transacción
            $this->pdo->commit();

            return $orderId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception('Error en la base de datos: ' . $e->getMessage());
        }
    }


    public function createCompleteOrder(array $data)
{
    try {
        $this->pdo->beginTransaction();

        // 1. Verificar si el cliente ya existe
        $stmt = $this->pdo->prepare("SELECT id FROM clients WHERE email = :email OR dni = :dni");
        $stmt->execute([
            ':email' => $data['client']['email'],
            ':dni'   => $data['client']['dni']
        ]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            $clientId = $client['id'];
        } else {
            // 2. Crear cliente
            $stmt = $this->pdo->prepare("
                INSERT INTO clients (name, email, phone, dni, gender, birth_date, status, created_at, updated_at)
                VALUES (:name, :email, :phone, :dni, :gender, :birth_date, 'ACTIVE', NOW(), NOW())
            ");
            $stmt->execute([
                ':name'       => $data['client']['name'],
                ':email'      => $data['client']['email'],
                ':phone'      => $data['client']['phone'],
                ':dni'        => $data['client']['dni'],
                ':gender'     => $data['client']['gender'],
                ':birth_date' => $data['client']['birth_date'],
            ]);
            $clientId = $this->pdo->lastInsertId();
        }

        // 3. Crear dirección del cliente
        $stmt = $this->pdo->prepare("
            INSERT INTO client_addresses (client_id, address, city, region, postal_code, phone, is_default, created_at, updated_at)
            VALUES (:client_id, :address, :city, :region, :postal_code, :phone, true, NOW(), NOW())
        ");
        $stmt->execute([
            ':client_id'   => $clientId,
            ':address'     => $data['address']['address'],
            ':city'        => $data['address']['city'],
            ':region'      => $data['address']['region'],
            ':postal_code' => $data['address']['postal_code'],
            ':phone'       => $data['address']['phone'],
        ]);
        $addressId = $this->pdo->lastInsertId();

        // 4. Crear orden
        $stmt = $this->pdo->prepare("
            INSERT INTO orders (client_id, address_id, total_price, status, discount_amount, coupon_id, created_by, created_at)
            VALUES (:client_id, :address_id, :total_price, :status, :discount_amount, :coupon_id, :created_by, NOW())
        ");
        $stmt->execute([
            ':client_id'      => $clientId,
            ':address_id'     => $addressId,
            ':total_price'    => $data['order']['total_price'],
            ':status'         => $data['order']['status'] ?? 'PENDING',
            ':discount_amount'=> $data['order']['discount_amount'] ?? 0,
            ':coupon_id'      => $data['order']['coupon_id'] ?? null,
            ':created_by'     => $data['order']['created_by'] ?? null,
        ]);
        $orderId = $this->pdo->lastInsertId();

        // 5. Insertar items
        if (!empty($data['items'])) {
            $stmt = $this->pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)
            ");
            foreach ($data['items'] as $item) {
                $stmt->execute([
                    ':order_id'   => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity'   => $item['quantity'],
                    ':price'      => $item['price'],
                ]);
            }
        }

        // 6. Registrar pago (opcional)
        if (!empty($data['payment'])) {
            $stmt = $this->pdo->prepare("
                INSERT INTO payments (order_id, method, status, paid_at, proof_url)
                VALUES (:order_id, :method, :status, :paid_at, :proof_url)
            ");
            $stmt->execute([
                ':order_id'  => $orderId,
                ':method'    => $data['payment']['method'],
                ':status'    => $data['payment']['status'] ?? 'PENDING',
                ':paid_at'   => $data['payment']['paid_at'] ?? null,
                ':proof_url' => $data['payment']['proof_url'] ?? null,
            ]);
        }

        $this->pdo->commit();

        return $orderId;

    } catch (PDOException $e) {
        $this->pdo->rollBack();
        throw new Exception("Error al crear la orden completa: " . $e->getMessage());
    }
}

    /**
     * Actualizar una orden
     */
    public function update(int $id, array $data)
    {
        try {
            $this->pdo->beginTransaction();

            // Actualizar datos principales de la orden
            $sql = "UPDATE {$this->table} SET 
                    status = :status,
                    total_price = :total_price,
                    discount_amount = :discount_amount,
                    coupon_id = :coupon_id
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $data['status']);
            $stmt->bindValue(':total_price', $data['total_price']);
            $stmt->bindValue(':discount_amount', $data['discount_amount'] ?? 0);
            $stmt->bindValue(':coupon_id', $data['coupon_id'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $result = $stmt->execute();

            if (!$result) {
                throw new Exception('Error al actualizar la orden');
            }

            // Actualizar información de pago si se proporciona
            if (!empty($data['payment'])) {
                $paymentSql = "UPDATE payments SET 
                              method = :method,
                              status = :status,
                              paid_at = :paid_at,
                              proof_url = :proof_url
                              WHERE order_id = :order_id";

                $paymentStmt = $this->pdo->prepare($paymentSql);
                $paymentStmt->bindValue(':method', $data['payment']['method']);
                $paymentStmt->bindValue(':status', $data['payment']['status']);
                $paymentStmt->bindValue(':paid_at', $data['payment']['paid_at'] ?? null);
                $paymentStmt->bindValue(':proof_url', $data['payment']['proof_url'] ?? null);
                $paymentStmt->bindValue(':order_id', $id, PDO::PARAM_INT);
                $paymentStmt->execute();
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception('Error en la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de una orden
     */
    public function updateStatus(int $id, string $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtener clientes disponibles
     */
    public function getClients()
    {
        $sql = "SELECT id, name, email, phone FROM clients WHERE status = 'ACTIVE' ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener direcciones de un cliente
     */
    public function getClientAddresses($clientId)
    {
        $sql = "SELECT id, address, city, region, postal_code, phone, is_default 
                FROM client_addresses 
                WHERE client_id = :client_id 
                ORDER BY is_default DESC, created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener cupones disponibles
     */
    public function getCoupons()
    {
        $sql = "SELECT id, code, description, discount_type, discount_value, max_uses, used_count, expires_at
                FROM coupons 
                WHERE is_active = true 
                AND (expires_at IS NULL OR expires_at > NOW())
                AND used_count < max_uses
                ORDER BY code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener productos disponibles
     */
    public function getProducts()
    {
        $sql = "SELECT id, name, price, stock, size, status
                FROM products 
                WHERE deleted_at IS NULL AND status = 'ACTIVE' AND stock > 0
                ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener estados de orden disponibles
     */
    public function getOrderStatuses()
    {
        return [
            'PENDING' => 'Pendiente',
            'COMPLETED' => 'Completada',
            'CANCELLED' => 'Cancelada'
        ];
    }

    /**
     * Obtener métodos de pago disponibles
     */
    public function getPaymentMethods()
    {
        return [
            'YAPE' => 'Yape',
            'PLIN' => 'Plin',
            'TRANSFER' => 'Transferencia',
            'CASH' => 'Efectivo'
        ];
    }

    /**
     * Obtener estados de pago disponibles
     */
    public function getPaymentStatuses()
    {
        return [
            'PENDING' => 'Pendiente',
            'PAID' => 'Pagado',
            'FAILED' => 'Fallido'
        ];
    }

    /**
     * Obtener estadísticas generales
     */
    public function getGeneralStats()
    {
        $stats = [];

        // Total de órdenes
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetchColumn();

        // Órdenes pendientes
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'PENDING'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['pending_orders'] = $stmt->fetchColumn();

        // Órdenes completadas
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'COMPLETED'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['completed_orders'] = $stmt->fetchColumn();

        // Órdenes canceladas
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'CANCELLED'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['cancelled_orders'] = $stmt->fetchColumn();

        // Valor total de ventas
        $sql = "SELECT SUM(total_price) FROM {$this->table} WHERE status = 'COMPLETED'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['total_sales'] = $stmt->fetchColumn() ?: 0;

        // Valor promedio de orden
        $sql = "SELECT AVG(total_price) FROM {$this->table} WHERE status != 'CANCELLED'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['average_order_value'] = $stmt->fetchColumn() ?: 0;

        return $stats;
    }

    /**
     * Obtener color para el estado de la orden
     */
    private function getStatusColor($status)
    {
        $colors = [
            'PENDING' => 'yellow',
            'COMPLETED' => 'green',
            'CANCELLED' => 'red'
        ];
        return $colors[$status] ?? 'gray';
    }

    /**
     * Obtener color para el estado del pago
     */
    private function getPaymentStatusColor($status)
    {
        $colors = [
            'PENDING' => 'yellow',
            'PAID' => 'green',
            'FAILED' => 'red'
        ];
        return $colors[$status] ?? 'gray';
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
