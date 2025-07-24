<?php
/**
 * Helper de autenticación y autorización para el sistema
 * 
 * Proporciona funciones para verificar los permisos de usuario basados en roles
 */
class AuthHelper {
    private $conn;
    private $currentRole;
    private $permissions;
    private $cached = false;

    /**
     * Constructor
     * 
     * @param PDO $conn Conexión a la base de datos
     */
    public function __construct($conn) {
        $this->conn = $conn;
        $this->currentRole = isset($_SESSION['usuario_role_id']) ? $_SESSION['usuario_role_id'] : null;
    }

    /**
     * Verifica si el usuario tiene un permiso específico para una tabla
     * 
     * @param string $table Nombre de la tabla
     * @param string $permission Tipo de permiso (create, read, update, delete)
     * @return bool True si tiene el permiso, False en caso contrario
     */
    public function hasPermission($table, $permission) {
        // Si no hay un rol asignado, no tiene permisos
        if (!$this->currentRole) {
            return false;
        }
        
        // Validar el tipo de permiso
        $permission = strtolower($permission);
        if (!in_array($permission, ['create', 'read', 'update', 'delete'])) {
            return false;
        }
        
        // Cargar permisos desde la base de datos si no se han cargado
        if (!$this->cached) {
            $this->loadPermissions();
        }
        
        // Verificar permiso específico
        foreach ($this->permissions as $perm) {
            if ($perm['table_name'] === $table) {
                $permColumn = 'can_' . $permission;
                return (bool) $perm[$permColumn];
            }
        }
        
        return false;
    }

    /**
     * Verifica si el usuario tiene acceso a cualquier acción en una tabla
     * 
     * @param string $table Nombre de la tabla
     * @return bool True si tiene al menos un permiso, False en caso contrario
     */
    public function canAccessTable($table) {
        // Si no hay un rol asignado, no tiene permisos
        if (!$this->currentRole) {
            return false;
        }
        
        // Cargar permisos desde la base de datos si no se han cargado
        if (!$this->cached) {
            $this->loadPermissions();
        }
        
        // Verificar cualquier permiso para la tabla
        foreach ($this->permissions as $perm) {
            if ($perm['table_name'] === $table) {
                return ($perm['can_create'] || $perm['can_read'] || $perm['can_update'] || $perm['can_delete']);
            }
        }
        
        return false;
    }

    /**
     * Verifica si el usuario es administrador (tiene todos o casi todos los permisos)
     * 
     * @return bool True si es administrador, False en caso contrario
     */
    public function isAdmin() {
        // Si no hay un rol asignado, no es admin
        if (!$this->currentRole) {
            return false;
        }
        
        // Si es el ID del rol de administrador, retornar true directamente
        if ($this->currentRole == 1) { // Asumiendo que el ID 1 es el administrador
            return true;
        }
        
        // Cargar permisos desde la base de datos si no se han cargado
        if (!$this->cached) {
            $this->loadPermissions();
        }
        
        // Contar permisos totales y permisos concedidos
        $totalPermissions = count($this->permissions) * 4; // 4 tipos de permisos por tabla
        $grantedPermissions = 0;
        
        foreach ($this->permissions as $perm) {
            if ($perm['can_create']) $grantedPermissions++;
            if ($perm['can_read']) $grantedPermissions++;
            if ($perm['can_update']) $grantedPermissions++;
            if ($perm['can_delete']) $grantedPermissions++;
        }
        
        // Si tiene más del 80% de los permisos, considerarlo admin
        return ($totalPermissions > 0 && ($grantedPermissions / $totalPermissions) >= 0.8);
    }

    /**
     * Carga los permisos del usuario actual desde la base de datos
     */
    private function loadPermissions() {
        try {
            $sql = "SELECT * FROM permissions WHERE role_id = :role_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':role_id', $this->currentRole, PDO::PARAM_INT);
            $stmt->execute();
            $this->permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->cached = true;
        } catch (PDOException $e) {
            error_log("Error cargando permisos: " . $e->getMessage());
            $this->permissions = [];
            $this->cached = true; // Evitar intentos repetidos si falla
        }
    }

    /**
     * Verifica si el usuario tiene acceso a una página específica
     * 
     * @param string $page Nombre de la página
     * @return bool True si tiene acceso, False en caso contrario
     */
    public function canAccessPage($page) {
        // Mapeo de páginas a permisos necesarios
        $pagePermissions = [
            // Productos
            'productos.php' => ['table' => 'products', 'permission' => 'read'],
            'product_images.php' => ['table' => 'products', 'permission' => 'update'],
            
            // Categorías
            'categories.php' => ['table' => 'categories', 'permission' => 'read'],
            
            // Usuarios
            'usuarios.php' => ['table' => 'users', 'permission' => 'read'],
            
            // Roles
            'roles.php' => ['table' => 'roles', 'permission' => 'read'],
            
            // Órdenes
            'orders.php' => ['table' => 'orders', 'permission' => 'read'],
            'orderPendiente.php' => ['table' => 'orders', 'permission' => 'read'],
            
            // Clientes
            'clientes.php' => ['table' => 'clients', 'permission' => 'read'],
            
            // Pagos
            'payments.php' => ['table' => 'payments', 'permission' => 'read'],
            
            // Análisis/Dashboard puede ser accedido por todos los usuarios con sesión
            'analisis.php' => true,
            'analisis_data.php' => true
        ];
        
        // Si la página no está en el mapeo, denegar acceso por seguridad
        if (!isset($pagePermissions[$page])) {
            return false;
        }
        
        // Si el valor es true, permitir acceso a cualquier usuario con sesión
        if ($pagePermissions[$page] === true) {
            return isset($_SESSION['usuario_id']);
        }
        
        // Verificar permiso específico
        return $this->hasPermission(
            $pagePermissions[$page]['table'], 
            $pagePermissions[$page]['permission']
        );
    }
}
