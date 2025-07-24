<?php

namespace App\Model;

use PDO;
use Exception;
use PDOException;

class PaymentModel
{
    protected $pdo;
    protected $table;

    public function __construct($pdo, $table = 'payments')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Obtener pagos con búsqueda y filtros
     */
    public function getAll(int $limit = 10, int $offset = 0, array $filters = [])
    {
        $sql = "SELECT 
                p.*, 
                o.total_price, 
                o.status AS order_status, 
                o.created_at AS order_created_at, 
                o.discount_amount, 
                o.client_id, 
                c.name AS client_name, 
                c.email AS client_email, 
                o.coupon_id, 
                cp.code AS coupon_code
            FROM {$this->table} p
            LEFT JOIN orders o ON p.order_id = o.id
            LEFT JOIN clients c ON o.client_id = c.id
            LEFT JOIN coupons cp ON o.coupon_id = cp.id
            WHERE 1=1";

        $params = [];

        if (isset($filters['order_id']) && $filters['order_id'] !== '') {
            $orderId = trim($filters['order_id']);
            if (is_numeric($orderId)) {
                $sql .= " AND p.order_id = :order_id";
                $params[':order_id'] = (int)$orderId;
            }
        }

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['method'])) {
            $sql .= " AND p.method = :method";
            $params[':method'] = $filters['method'];
        }

        if (isset($filters['admin_verified']) && $filters['admin_verified'] !== '') {
            $sql .= " AND p.admin_verified = :admin_verified";
            $params[':admin_verified'] = (bool)$filters['admin_verified'];
        }

        if (!empty($filters['order_status'])) {
            $sql .= " AND o.status = :order_status";
            $params[':order_status'] = $filters['order_status'];
        }

        $sql .= " ORDER BY p.order_id DESC";

        // ⚠️ Parámetros directos para LIMIT y OFFSET
        if ($limit > 0) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obtener pago por ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear un nuevo pago
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (order_id, method, status, paid_at, proof_url, verification_code, verified_at, admin_verified, verified_by) VALUES (:order_id, :method, :status, :paid_at, :proof_url, :verification_code, :verified_at, :admin_verified, :verified_by)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':order_id', $data['order_id'], PDO::PARAM_INT);
        $stmt->bindValue(':method', $data['method']);
        $stmt->bindValue(':status', $data['status'] ?? 'PENDING');
        $stmt->bindValue(':paid_at', $data['paid_at'] ?? null);
        $stmt->bindValue(':proof_url', $data['proof_url'] ?? null);
        $stmt->bindValue(':verification_code', $data['verification_code'] ?? null);
        $stmt->bindValue(':verified_at', $data['verified_at'] ?? null);
        $stmt->bindValue(':admin_verified', $data['admin_verified'] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(':verified_by', $data['verified_by'] ?? null);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    /**
     * Actualizar pago
     */
    public function update($id, array $data)
    {
        $sql = "UPDATE {$this->table} SET 
            method = COALESCE(:method, method),
            status = COALESCE(:status, status),
            paid_at = COALESCE(:paid_at, paid_at),
            proof_url = COALESCE(:proof_url, proof_url),
            verification_code = COALESCE(:verification_code, verification_code),
            verified_at = COALESCE(:verified_at, verified_at),
            admin_verified = COALESCE(:admin_verified, admin_verified),
            verified_by = COALESCE(:verified_by, verified_by),
            observacion_admin = COALESCE(:observacion_admin, observacion_admin)
            WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':method', $data['method'] ?? null);
        $stmt->bindValue(':status', $data['status'] ?? null);
        $stmt->bindValue(':paid_at', $data['paid_at'] ?? null);
        $stmt->bindValue(':proof_url', $data['proof_url'] ?? null);
        $stmt->bindValue(':verification_code', $data['verification_code'] ?? null);
        $stmt->bindValue(':verified_at', $data['verified_at'] ?? null);
        $stmt->bindValue(':admin_verified', isset($data['admin_verified']) ? (bool)$data['admin_verified'] : null, PDO::PARAM_BOOL);
        $stmt->bindValue(':verified_by', $data['verified_by'] ?? null);
        $stmt->bindValue(':observacion_admin', $data['observacion_admin'] ?? null);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verificar pago YAPE
     */
    public function verifyYapePayment($id, $verification_code, $verified_by, $status)
    {
        $verified_at = date('Y-m-d H:i:s');
        $paid_at = ($status === 'PAID') ? $verified_at : null;
        $sql = "UPDATE {$this->table} SET 
            verification_code = :verification_code,
            verified_at = :verified_at,
            admin_verified = :admin_verified,
            verified_by = :verified_by,
            status = :status,
            paid_at = :paid_at
            WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':verification_code', $verification_code);
        $stmt->bindValue(':verified_at', $verified_at);
        $stmt->bindValue(':admin_verified', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':verified_by', $verified_by);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':paid_at', $paid_at);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verificar pago para métodos que no sean YAPE
     */
    public function verifyOtherPayment($id, $rejected, $verified_by, $proof_url = null)
    {
        $verified_at = date('Y-m-d H:i:s');
        $status = $rejected ? 'FAILED' : 'PAID';
        $paid_at = ($status === 'PAID') ? $verified_at : null;
        
        $sql = "UPDATE {$this->table} SET 
            verified_at = :verified_at,
            admin_verified = :admin_verified,
            verified_by = :verified_by,
            status = :status,
            paid_at = :paid_at";
        
        if ($proof_url !== null) {
            $sql .= ", proof_url = :proof_url";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':verified_at', $verified_at);
        $stmt->bindValue(':admin_verified', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':verified_by', $verified_by);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':paid_at', $paid_at);
        
        if ($proof_url !== null) {
            $stmt->bindValue(':proof_url', $proof_url);
        }
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Cambiar solo el estado de pago
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Eliminar pago
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
