<?php

namespace App\Model;

use PDO;
use Exception;

class BaseModel
{
    protected $pdo;
    protected $table;

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Obtener registros con búsqueda opcional por nombre o email, paginados.
     */
    public function getAll(int $limit = 10, int $offset = 0, string $search = '', array $filters = [])
    {
        $sql = "SELECT u.id, u.username, u.email, u.name, u.status, u.role_id, r.name AS role, u.created_at
            FROM {$this->table} u
            INNER JOIN roles r ON u.role_id = r.id";

        $conditions = [];
        $params = [];

        // Búsqueda por texto
        if ($search !== '') {
            $conditions[] = "(u.name ILIKE :search OR u.email ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por status
        if (!empty($filters['status'])) {
            $conditions[] = "u.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filtro por rol
        if (!empty($filters['role_id'])) {
            $conditions[] = "u.role_id = :role_id";
            $params[':role_id'] = $filters['role_id'];
        }

        // Filtro por rango de fechas
        if (!empty($filters['date_from'])) {
            $conditions[] = "u.created_at >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "u.created_at <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        // Agregar condiciones WHERE si existen
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY u.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        // Bind de parámetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar resultados con búsqueda opcional.
     */
    public function count(string $search = '', array $filters = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} u
                INNER JOIN roles r ON u.role_id = r.id";

        $conditions = [];
        $params = [];

        // Búsqueda por texto
        if ($search !== '') {
            $conditions[] = "(u.name ILIKE :search OR u.email ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por status
        if (!empty($filters['status'])) {
            $conditions[] = "u.status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filtro por rol
        if (!empty($filters['role_id'])) {
            $conditions[] = "u.role_id = :role_id";
            $params[':role_id'] = $filters['role_id'];
        }

        // Filtro por rango de fechas
        if (!empty($filters['date_from'])) {
            $conditions[] = "u.created_at >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "u.created_at <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        // Agregar condiciones WHERE si existen
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->pdo->prepare($sql);

        // Bind de parámetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtener todos los roles disponibles.
     */
    public function getRoles()
    {
        $sql = "SELECT id, name FROM roles ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un registro por ID.
     */
    public function getById(int $id)
    {
        $sql = "SELECT u.id, u.username, u.email, u.name, u.status, u.role_id, r.name AS role, u.created_at, u.updated_at
            FROM {$this->table} u
            INNER JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear un nuevo usuario.
     */
    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (username, password, email, name, status, role_id, created_by, updated_by)
                VALUES (:username, :password, :email, :name, :status, :role_id, :created_by, :updated_by)";

        $stmt = $this->pdo->prepare($sql);

        // Hash de la contraseña
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindValue(':username', $data['username']);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':status', $data['status'] ?? 'ACTIVE');
        $stmt->bindValue(':role_id', $data['role_id'], PDO::PARAM_INT);
        $stmt->bindValue(':created_by', $data['created_by'] ?? 'system');
        $stmt->bindValue(':updated_by', $data['updated_by'] ?? 'system');

        $result = $stmt->execute();

        if ($result) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    /**
     * Actualizar un usuario existente.
     */
    public function update(int $id, array $data)
    {
        // Construir la consulta dinámicamente según los campos enviados
        $fields = [];
        $params = [];

        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params[':username'] = $data['username'];
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }

        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }

        if (isset($data['status'])) {
            $fields[] = "status = :status";
            $params[':status'] = $data['status'];
        }

        if (isset($data['role_id'])) {
            $fields[] = "role_id = :role_id";
            $params[':role_id'] = $data['role_id'];
        }

        // Siempre actualizar updated_by
        $fields[] = "updated_by = :updated_by";
        $params[':updated_by'] = $data['updated_by'] ?? 'system';

        if (empty($fields)) {
            return false; // No hay campos para actualizar
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $params[':id'] = $id;

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            if ($key === ':id' || $key === ':role_id') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        return $stmt->execute();
    }

    /**
     * Eliminado lógico - cambiar status a INACTIVE.
     */
    public function softDelete(int $id, string $deletedBy = 'system')
    {
        $sql = "UPDATE {$this->table} 
                SET status = 'INACTIVE', updated_by = :updated_by 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $deletedBy);

        return $stmt->execute();
    }

    /**
     * Restaurar usuario (reactivar).
     */
    public function restore(int $id, string $restoredBy = 'system')
    {
        $sql = "UPDATE {$this->table} 
                SET status = 'ACTIVE', updated_by = :updated_by 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $restoredBy);

        return $stmt->execute();
    }

    /**
     * Eliminado físico (usar con precaución).
     */
    public function hardDelete(int $id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Verificar si un username ya existe.
     */
    public function usernameExists(string $username, int $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = :username";
        $params = [':username' => $username];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':exclude_id') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si un email ya existe.
     */
    public function emailExists(string $email, int $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $params = [':email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':exclude_id') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener información detallada completa de un usuario por ID.
     * Incluye información del rol, estadísticas y metadatos.
     */
    public function getDetailedById(int $id)
    {
        $sql = "SELECT 
                    u.id, 
                    u.username, 
                    u.email, 
                    u.name, 
                    u.status, 
                    u.role_id,
                    u.created_by,
                    u.updated_by,
                    u.created_at, 
                    u.updated_at,
                    r.name AS role_name,
                    r.description AS role_description,
                    CASE 
                        WHEN u.status = 'ACTIVE' THEN 'ACTIVE'
                        WHEN u.status = 'INACTIVE' THEN 'INACTIVE'
                        ELSE u.status 
                    END AS status_label,
                    CASE 
                        WHEN u.created_at > NOW() - INTERVAL '30 days' THEN true
                        ELSE false 
                    END AS is_recent,
                    EXTRACT(DAYS FROM (NOW() - u.created_at)) AS days_since_creation,
                    EXTRACT(DAYS FROM (NOW() - COALESCE(u.updated_at, u.created_at))) AS days_since_last_update
                FROM {$this->table} u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        // Agregar información adicional
        $user['formatted_created_at'] = date('d/m/Y H:i', strtotime($user['created_at']));
        $user['formatted_updated_at'] = $user['updated_at'] ? date('d/m/Y H:i', strtotime($user['updated_at'])) : 'Nunca';

        // Calcular tiempo relativo
        $user['created_ago'] = $this->getTimeAgo($user['created_at']);
        $user['updated_ago'] = $user['updated_at'] ? $this->getTimeAgo($user['updated_at']) : 'Nunca actualizado';

        // Obtener permisos del rol (si existen)
        $user['permissions'] = $this->getUserPermissions($user['role_id']);

        // Obtener estadísticas del usuario
        $user['statistics'] = $this->getUserStatistics($id);

        return $user;
    }

    /**
     * Obtener permisos de un rol específico.
     */
    private function getUserPermissions(int $roleId)
    {
        $sql = "SELECT 
                    p.table_name,
                    p.can_create,
                    p.can_read,
                    p.can_update,
                    p.can_delete
                FROM permissions p
                WHERE p.role_id = :role_id
                ORDER BY p.table_name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener estadísticas básicas del usuario.
     */
    private function getUserStatistics(int $userId)
    {
        $stats = [
            'total_logins' => 0,
            'last_login' => null,
            'created_records' => 0,
            'updated_records' => 0
        ];

        // Contar registros creados por el usuario en audit_log
        try {
            $sql = "SELECT COUNT(*) FROM audit_log WHERE performed_by = (SELECT username FROM {$this->table} WHERE id = :user_id) AND action = 'CREATE'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $stats['created_records'] = (int) $stmt->fetchColumn();

            // Contar registros actualizados por el usuario
            $sql = "SELECT COUNT(*) FROM audit_log WHERE performed_by = (SELECT username FROM {$this->table} WHERE id = :user_id) AND action = 'UPDATE'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $stats['updated_records'] = (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            // Si no existe la tabla audit_log o hay errores, mantener valores por defecto
        }

        return $stats;
    }

    /**
     * Calcular tiempo transcurrido de forma legible.
     */
    private function getTimeAgo(string $datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return 'Hace menos de 1 minuto';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return "Hace {$minutes} minuto" . ($minutes > 1 ? 's' : '');
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return "Hace {$hours} hora" . ($hours > 1 ? 's' : '');
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return "Hace {$days} día" . ($days > 1 ? 's' : '');
        } elseif ($time < 31536000) {
            $months = floor($time / 2592000);
            return "Hace {$months} mes" . ($months > 1 ? 'es' : '');
        } else {
            $years = floor($time / 31536000);
            return "Hace {$years} año" . ($years > 1 ? 's' : '');
        }
    }

    /**
     * Obtener historial de cambios del usuario.
     */
    public function getUserAuditHistory(int $userId, int $limit = 10)
    {
        try {
            $sql = "SELECT 
                        al.action,
                        al.table_name,
                        al.changed_fields,
                        al.old_values,
                        al.new_values,
                        al.performed_at,
                        al.ip_address
                    FROM audit_log al
                    INNER JOIN {$this->table} u ON al.performed_by = u.username
                    WHERE u.id = :user_id
                    ORDER BY al.performed_at DESC
                    LIMIT :limit";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Verificar si el usuario puede realizar una acción específica.
     */
    public function canUserPerformAction(int $userId, string $tableName, string $action)
    {
        $sql = "SELECT p.can_{$action}
                FROM {$this->table} u
                INNER JOIN permissions p ON u.role_id = p.role_id
                WHERE u.id = :user_id AND p.table_name = :table_name";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':table_name', $tableName);
            $stmt->execute();

            $result = $stmt->fetchColumn();
            return (bool) $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public function findByUsernameOrEmail(string $input)
    {
        $sql = "SELECT 
                    u.id,
                    u.username, 
                    u.email, 
                    u.name, 
                    u.password,
                    u.status,
                    u.role_id,
                    r.name AS role
     FROM {$this->table} u 
     INNER JOIN roles r ON u.role_id = r.id
     WHERE u.username = :input OR u.email = :input LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':input', $input);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
