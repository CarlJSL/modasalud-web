<?php

namespace App\Model;

use PDO;
use Exception;
use PDOException;

class RolesModel
{
    protected $pdo;
    protected $table;

    public function __construct($pdo, $table = 'roles')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    /**
     * Obtener todos los roles con información de permisos y usuarios
     */
    public function getAll(int $limit = 10, int $offset = 0, string $search = '', array $filters = [])
    {
        $sql = "SELECT 
                r.id,
                r.name,
                r.description,
                -- Conteo de permisos
                COUNT(DISTINCT p.id) AS total_permissions,
                COUNT(DISTINCT CASE WHEN p.can_create = true THEN p.id END) AS create_permissions,
                COUNT(DISTINCT CASE WHEN p.can_read = true THEN p.id END) AS read_permissions,
                COUNT(DISTINCT CASE WHEN p.can_update = true THEN p.id END) AS update_permissions,
                COUNT(DISTINCT CASE WHEN p.can_delete = true THEN p.id END) AS delete_permissions,
                -- Conteo de usuarios asignados
                COUNT(DISTINCT u.id) AS total_users,
                COUNT(DISTINCT CASE WHEN u.status = 'ACTIVE' THEN u.id END) AS active_users,
                COUNT(DISTINCT CASE WHEN u.status = 'INACTIVE' THEN u.id END) AS inactive_users,
                -- Información de tablas con permisos
                STRING_AGG(DISTINCT p.table_name, ', ' ORDER BY p.table_name) AS tables_with_permissions,
                -- Calcular nivel de permisos
                CASE 
                    WHEN COUNT(DISTINCT p.id) = 0 THEN 'SIN_PERMISOS'
                    WHEN AVG(CASE WHEN p.can_create AND p.can_read AND p.can_update AND p.can_delete THEN 1.0 ELSE 0.0 END) >= 0.75 THEN 'TOTAL'
                    WHEN AVG(CASE WHEN p.can_read AND p.can_update THEN 1.0 ELSE 0.0 END) >= 0.5 THEN 'MODERADO'
                    WHEN COUNT(DISTINCT CASE WHEN p.can_read = true THEN p.id END) > 0 THEN 'LECTURA'
                    ELSE 'LIMITADO'
                END AS permission_level
            FROM {$this->table} r
            LEFT JOIN permissions p ON r.id = p.role_id
            LEFT JOIN users u ON r.id = u.role_id
            WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre o descripción
        if ($search !== '') {
            $conditions[] = "(r.name ILIKE :search OR r.description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por nivel de permisos
        if (!empty($filters['permission_level'])) {
            // Este filtro se aplicará después del GROUP BY en el HAVING
        }

        // Filtro por número mínimo de usuarios
        if (!empty($filters['min_users'])) {
            // Este filtro se aplicará después del GROUP BY en el HAVING
        }

        // Filtro por estado de usuarios
        if (!empty($filters['user_status'])) {
            if ($filters['user_status'] === 'active') {
                $conditions[] = "u.status = 'ACTIVE'";
            } elseif ($filters['user_status'] === 'inactive') {
                $conditions[] = "u.status = 'INACTIVE'";
            }
        }

        // Filtro por tabla específica
        if (!empty($filters['table_name'])) {
            $conditions[] = "p.table_name = :table_name";
            $params[':table_name'] = $filters['table_name'];
        }

        // Agregar condiciones al SQL
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY r.id, r.name, r.description";

        // Agregar filtros que requieren HAVING
        $havingConditions = [];
        
        if (!empty($filters['permission_level'])) {
            switch ($filters['permission_level']) {
                case 'TOTAL':
                    $havingConditions[] = "AVG(CASE WHEN p.can_create AND p.can_read AND p.can_update AND p.can_delete THEN 1.0 ELSE 0.0 END) >= 0.75";
                    break;
                case 'MODERADO':
                    $havingConditions[] = "AVG(CASE WHEN p.can_read AND p.can_update THEN 1.0 ELSE 0.0 END) >= 0.5";
                    break;
                case 'LECTURA':
                    $havingConditions[] = "COUNT(DISTINCT CASE WHEN p.can_read = true THEN p.id END) > 0";
                    break;
                case 'SIN_PERMISOS':
                    $havingConditions[] = "COUNT(DISTINCT p.id) = 0";
                    break;
            }
        }

        if (!empty($filters['min_users'])) {
            $havingConditions[] = "COUNT(DISTINCT u.id) >= :min_users";
            $params[':min_users'] = $filters['min_users'];
        }

        if (!empty($havingConditions)) {
            $sql .= " HAVING " . implode(" AND ", $havingConditions);
        }

        $sql .= " ORDER BY r.name ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        // Vincular parámetros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar información adicional procesada
        foreach ($roles as &$role) {
            $role['permission_level_color'] = $this->getPermissionLevelColor($role['permission_level']);
            $role['user_activity_ratio'] = $role['total_users'] > 0 ? 
                round(($role['active_users'] / $role['total_users']) * 100, 1) : 0;
        }

        return $roles;
    }

    /**
     * Contar total de roles con filtros
     */
    public function count(string $search = '', array $filters = [])
    {
        $sql = "SELECT COUNT(DISTINCT r.id) FROM {$this->table} r
                LEFT JOIN permissions p ON r.id = p.role_id
                LEFT JOIN users u ON r.id = u.role_id
                WHERE 1=1";

        $conditions = [];
        $params = [];

        // Búsqueda por nombre o descripción
        if ($search !== '') {
            $conditions[] = "(r.name ILIKE :search OR r.description ILIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtro por estado de usuarios
        if (!empty($filters['user_status'])) {
            if ($filters['user_status'] === 'active') {
                $conditions[] = "u.status = 'ACTIVE'";
            } elseif ($filters['user_status'] === 'inactive') {
                $conditions[] = "u.status = 'INACTIVE'";
            }
        }

        // Filtro por tabla específica
        if (!empty($filters['table_name'])) {
            $conditions[] = "p.table_name = :table_name";
            $params[':table_name'] = $filters['table_name'];
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
     * Obtener un rol por ID
     */
    public function getById($id)
    {
        $sql = "SELECT 
                r.id,
                r.name,
                r.description
            FROM {$this->table} r
            WHERE r.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener información detallada de un rol
     */
    public function getDetailedById($id)
    {
        $role = $this->getById($id);

        if (!$role) {
            return null;
        }

        // Obtener permisos del rol
        $permissionsStmt = $this->pdo->prepare("
            SELECT 
                p.id,
                p.table_name,
                p.can_create,
                p.can_read,
                p.can_update,
                p.can_delete
            FROM permissions p
            WHERE p.role_id = :role_id
            ORDER BY p.table_name ASC
        ");
        $permissionsStmt->execute(['role_id' => $id]);
        $role['permissions'] = $permissionsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener usuarios asignados al rol
        $usersStmt = $this->pdo->prepare("
            SELECT 
                u.id,
                u.username,
                u.name,
                u.email,
                u.status,
                u.created_at,
                u.updated_at,
                -- Información de actividad
                COUNT(DISTINCT o.id) AS orders_created,
                MAX(o.created_at) AS last_order_created
            FROM users u
            LEFT JOIN orders o ON u.id = o.created_by
            WHERE u.role_id = :role_id
            GROUP BY u.id, u.username, u.name, u.email, u.status, u.created_at, u.updated_at
            ORDER BY u.name ASC
        ");
        $usersStmt->execute(['role_id' => $id]);
        $role['users'] = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular estadísticas del rol
        $statsStmt = $this->pdo->prepare("
            SELECT 
                COUNT(DISTINCT p.id) AS total_permissions,
                COUNT(DISTINCT CASE WHEN p.can_create = true THEN p.id END) AS create_permissions,
                COUNT(DISTINCT CASE WHEN p.can_read = true THEN p.id END) AS read_permissions,
                COUNT(DISTINCT CASE WHEN p.can_update = true THEN p.id END) AS update_permissions,
                COUNT(DISTINCT CASE WHEN p.can_delete = true THEN p.id END) AS delete_permissions,
                COUNT(DISTINCT u.id) AS total_users,
                COUNT(DISTINCT CASE WHEN u.status = 'ACTIVE' THEN u.id END) AS active_users,
                COUNT(DISTINCT CASE WHEN u.status = 'INACTIVE' THEN u.id END) AS inactive_users,
                COUNT(DISTINCT p.table_name) AS tables_managed
            FROM roles r
            LEFT JOIN permissions p ON r.id = p.role_id
            LEFT JOIN users u ON r.id = u.role_id
            WHERE r.id = :role_id
            GROUP BY r.id
        ");
        $statsStmt->execute(['role_id' => $id]);
        $role['statistics'] = $statsStmt->fetch(PDO::FETCH_ASSOC);

        // Obtener resumen de permisos por tabla
        $tablePermissionsStmt = $this->pdo->prepare("
            SELECT 
                p.table_name,
                COUNT(*) AS permission_count,
                BOOL_AND(p.can_create) AS has_create,
                BOOL_AND(p.can_read) AS has_read,
                BOOL_AND(p.can_update) AS has_update,
                BOOL_AND(p.can_delete) AS has_delete,
                STRING_AGG(
                    CASE 
                        WHEN p.can_create THEN 'C'
                        ELSE ''
                    END ||
                    CASE 
                        WHEN p.can_read THEN 'R'
                        ELSE ''
                    END ||
                    CASE 
                        WHEN p.can_update THEN 'U'
                        ELSE ''
                    END ||
                    CASE 
                        WHEN p.can_delete THEN 'D'
                        ELSE ''
                    END, 
                    ', '
                ) AS permission_summary
            FROM permissions p
            WHERE p.role_id = :role_id
            GROUP BY p.table_name
            ORDER BY p.table_name
        ");
        $tablePermissionsStmt->execute(['role_id' => $id]);
        $role['table_permissions'] = $tablePermissionsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar información adicional procesada
        if ($role['statistics']) {
            $role['permission_level'] = $this->calculatePermissionLevel($role['statistics']);
            $role['permission_level_color'] = $this->getPermissionLevelColor($role['permission_level']);
            $role['user_activity_ratio'] = $role['statistics']['total_users'] > 0 ? 
                round(($role['statistics']['active_users'] / $role['statistics']['total_users']) * 100, 1) : 0;
        }

        // Procesar información de usuarios
        foreach ($role['users'] as &$user) {
            $user['time_since_registration'] = $this->timeAgo($user['created_at']);
            $user['status_color'] = $this->getStatusColor($user['status']);
            if ($user['last_order_created']) {
                $user['time_since_last_order'] = $this->timeAgo($user['last_order_created']);
            }
        }

        return $role;
    }

    /**
     * Crear un nuevo rol
     */
    public function create(array $data)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO {$this->table} (name, description)
                    VALUES (:name, :description)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':description', $data['description'] ?? null);

            $result = $stmt->execute();

            if (!$result) {
                throw new Exception('Error al crear el rol');
            }

            $roleId = $this->pdo->lastInsertId();

            // Crear permisos si se proporcionan
            if (!empty($data['permissions']) && is_array($data['permissions'])) {
                $this->createPermissions($roleId, $data['permissions']);
            }

            $this->pdo->commit();
            return $roleId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Actualizar un rol
     */
    public function update($id, array $data)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE {$this->table} SET 
                    name = :name,
                    description = :description
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':description', $data['description'] ?? null);

            $result = $stmt->execute();

            // Actualizar permisos si se proporcionan
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                // Eliminar permisos existentes
                $this->deletePermissionsByRoleId($id);
                // Crear nuevos permisos
                $this->createPermissions($id, $data['permissions']);
            }

            $this->pdo->commit();
            return $result;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception('Error al actualizar el rol: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un rol
     */
    public function delete($id)
    {
        try {
            $this->pdo->beginTransaction();

            // Verificar si hay usuarios asignados al rol
            $userCheckStmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = :role_id");
            $userCheckStmt->execute(['role_id' => $id]);
            $userCount = $userCheckStmt->fetchColumn();

            if ($userCount > 0) {
                throw new Exception("No se puede eliminar el rol porque tiene $userCount usuarios asignados");
            }

            // Eliminar permisos (se eliminan automáticamente por CASCADE)
            // Eliminar rol
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute(['id' => $id]);

            $this->pdo->commit();
            return $result;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Obtener estadísticas generales de roles
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                COUNT(DISTINCT r.id) AS total_roles,
                COUNT(DISTINCT p.id) AS total_permissions,
                COUNT(DISTINCT u.id) AS total_users_with_roles,
                COUNT(DISTINCT CASE WHEN u.status = 'ACTIVE' THEN u.id END) AS active_users_with_roles,
                COUNT(DISTINCT p.table_name) AS tables_with_permissions,
                AVG(role_stats.permissions_per_role) AS avg_permissions_per_role,
                AVG(role_stats.users_per_role) AS avg_users_per_role
            FROM {$this->table} r
            LEFT JOIN permissions p ON r.id = p.role_id
            LEFT JOIN users u ON r.id = u.role_id
            LEFT JOIN (
                SELECT 
                    r_inner.id,
                    COUNT(DISTINCT p_inner.id) AS permissions_per_role,
                    COUNT(DISTINCT u_inner.id) AS users_per_role
                FROM roles r_inner
                LEFT JOIN permissions p_inner ON r_inner.id = p_inner.role_id
                LEFT JOIN users u_inner ON r_inner.id = u_inner.role_id
                GROUP BY r_inner.id
            ) role_stats ON r.id = role_stats.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener roles más utilizados
     */
    public function getMostUsedRoles($limit = 10)
    {
        $sql = "SELECT 
                r.id,
                r.name,
                r.description,
                COUNT(DISTINCT u.id) AS user_count,
                COUNT(DISTINCT CASE WHEN u.status = 'ACTIVE' THEN u.id END) AS active_user_count,
                COUNT(DISTINCT p.id) AS permission_count
            FROM {$this->table} r
            LEFT JOIN users u ON r.id = u.role_id
            LEFT JOIN permissions p ON r.id = p.role_id
            GROUP BY r.id, r.name, r.description
            HAVING COUNT(DISTINCT u.id) > 0
            ORDER BY user_count DESC, active_user_count DESC
            LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todas las tablas disponibles del sistema
     */
    public function getAvailableTables()
    {
        $sql = "SELECT 
                table_name
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_type = 'BASE TABLE'
            AND table_name NOT IN ('audit_log') -- Excluir tablas de sistema
            ORDER BY table_name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Buscar rol por nombre
     */
    public function findByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si un usuario tiene un permiso específico
     */
    public function hasPermission($userId, $tableName, $action)
    {
        $sql = "SELECT p.can_{$action}
                FROM users u
                JOIN roles r ON u.role_id = r.id
                JOIN permissions p ON r.id = p.role_id
                WHERE u.id = :user_id 
                AND p.table_name = :table_name
                AND u.status = 'ACTIVE'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'table_name' => $tableName
        ]);

        $result = $stmt->fetchColumn();
        return (bool) $result;
    }

    /**
     * Obtener todos los permisos de un usuario
     */
    public function getUserPermissions($userId)
    {
        $sql = "SELECT 
                p.table_name,
                p.can_create,
                p.can_read,
                p.can_update,
                p.can_delete,
                r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            JOIN permissions p ON r.id = p.role_id
            WHERE u.id = :user_id 
            AND u.status = 'ACTIVE'
            ORDER BY p.table_name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Métodos auxiliares

    /**
     * Crear permisos para un rol
     */
    private function createPermissions($roleId, array $permissions)
    {
        foreach ($permissions as $permission) {
            $sql = "INSERT INTO permissions (role_id, table_name, can_create, can_read, can_update, can_delete)
                    VALUES (:role_id, :table_name, :can_create, :can_read, :can_update, :can_delete)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':role_id', $roleId, PDO::PARAM_INT);
            $stmt->bindValue(':table_name', $permission['table_name']);
            $stmt->bindValue(':can_create', $permission['can_create'] ?? false, PDO::PARAM_BOOL);
            $stmt->bindValue(':can_read', $permission['can_read'] ?? false, PDO::PARAM_BOOL);
            $stmt->bindValue(':can_update', $permission['can_update'] ?? false, PDO::PARAM_BOOL);
            $stmt->bindValue(':can_delete', $permission['can_delete'] ?? false, PDO::PARAM_BOOL);
            $stmt->execute();
        }
    }

    /**
     * Eliminar permisos por role_id
     */
    private function deletePermissionsByRoleId($roleId)
    {
        $sql = "DELETE FROM permissions WHERE role_id = :role_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['role_id' => $roleId]);
    }

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
     * Obtener color según el nivel de permisos
     */
    private function getPermissionLevelColor($level)
    {
        switch ($level) {
            case 'TOTAL':
                return 'bg-red-100 text-red-800';
            case 'MODERADO':
                return 'bg-blue-100 text-blue-800';
            case 'LECTURA':
                return 'bg-green-100 text-green-800';
            case 'LIMITADO':
                return 'bg-yellow-100 text-yellow-800';
            case 'SIN_PERMISOS':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Calcular nivel de permisos
     */
    private function calculatePermissionLevel($stats)
    {
        if ($stats['total_permissions'] == 0) {
            return 'SIN_PERMISOS';
        }

        $totalPossible = $stats['total_permissions'] * 4; // 4 tipos de permisos por tabla
        $totalGranted = $stats['create_permissions'] + $stats['read_permissions'] + 
                       $stats['update_permissions'] + $stats['delete_permissions'];

        $ratio = $totalPossible > 0 ? $totalGranted / $totalPossible : 0;

        if ($ratio >= 0.75) return 'TOTAL';
        if ($ratio >= 0.5) return 'MODERADO';
        if ($stats['read_permissions'] > 0) return 'LECTURA';
        return 'LIMITADO';
    }
}
