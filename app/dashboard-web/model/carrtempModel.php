<?php

namespace App\Model;

use PDO;
use Exception;
use PDOException;

class CarrTempModel
{
    protected $pdo;
    protected $table;

    public function __construct($pdo, $table = 'cart_items_temp')
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function getCartItems(string $token)
    {
        $sql = "SELECT 
                    c.product_id,
                    p.name,
                    p.price,
                    p.stock,
                    pi.image_url AS main_image,
                    c.quantity,
                    c.selected,
                    (p.price * c.quantity) as subtotal
                FROM {$this->table} c
                JOIN products p ON p.id = c.product_id
                LEFT JOIN (
                    SELECT product_id, image_url,
                           ROW_NUMBER() OVER (PARTITION BY product_id ORDER BY created_at ASC) as rn
                    FROM product_images
                ) pi ON p.id = pi.product_id AND pi.rn = 1
                WHERE c.cart_token = :token
                ORDER BY c.added_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToCart(string $token, int $product_id, int $quantity = 1)
    {
        // Verificar si ya existe el producto en el carrito
        $check_sql = "SELECT id, quantity FROM {$this->table} WHERE cart_token = :token AND product_id = :product_id";
        $check_stmt = $this->pdo->prepare($check_sql);
        $check_stmt->execute(['token' => $token, 'product_id' => $product_id]);
        $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Actualizar cantidad
            $sql = "UPDATE {$this->table} SET quantity = quantity + :quantity WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'quantity' => $quantity,
                'id' => $existing['id']
            ]);
        } else {
            // Insertar nuevo registro
            $sql = "INSERT INTO {$this->table} (cart_token, product_id, quantity, selected)
                    VALUES (:token, :product_id, :quantity, TRUE)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }

    public function updateQuantity(string $token, int $product_id, int $quantity)
    {
        $sql = "UPDATE {$this->table} 
                SET quantity = :quantity
                WHERE cart_token = :token AND product_id = :product_id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'quantity' => $quantity,
            'token' => $token,
            'product_id' => $product_id
        ]);
    }

    public function toggleSelection(string $token, int $product_id, bool $selected)
    {
        $sql = "UPDATE {$this->table} 
                SET selected = :selected
                WHERE cart_token = :token AND product_id = :product_id";

        // Asegurarse de que el valor es un booleano verdadero para PostgreSQL
        $boolValue = $selected ? 'TRUE' : 'FALSE';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':selected', $boolValue, PDO::PARAM_STR);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function deleteItem(string $token, int $product_id)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE cart_token = :token AND product_id = :product_id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'product_id' => $product_id
        ]);
    }

    public function clearCart(string $token)
    {
        $sql = "DELETE FROM {$this->table} WHERE cart_token = :token";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['token' => $token]);
    }

    public function getSelectedItems(string $token)
    {
        $sql = "SELECT 
                    c.product_id,
                    p.name,
                    p.price,
                    p.stock,
                    pi.image_url AS main_image,
                    c.quantity,
                    (p.price * c.quantity) as subtotal
                FROM {$this->table} c
                JOIN products p ON p.id = c.product_id
                LEFT JOIN (
                    SELECT product_id, image_url,
                           ROW_NUMBER() OVER (PARTITION BY product_id ORDER BY created_at ASC) as rn
                    FROM product_images
                ) pi ON p.id = pi.product_id AND pi.rn = 1
                WHERE c.cart_token = :token AND c.selected = TRUE
                ORDER BY c.added_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkStock(string $token, int $product_id, int $quantity)
    {
        $sql = "SELECT stock FROM products WHERE id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['product_id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return false;
        }

        return $product['stock'] >= $quantity;
    }

    public function getCartTotal(string $token)
    {
        $sql = "SELECT SUM(p.price * c.quantity) as total
                FROM {$this->table} c
                JOIN products p ON p.id = c.product_id
                WHERE c.cart_token = :token AND c.selected = TRUE";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }

    public function getCartItemCount(string $token)
    {
        $sql = "SELECT SUM(quantity) as total_items
                FROM {$this->table}
                WHERE cart_token = :token";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total_items'] ?? 0;
    }
    
    /**
     * Elimina carritos temporales que no han sido modificados en X días
     * @param int $days Días de inactividad antes de eliminar
     * @return int Cantidad de carritos eliminados
     */
    public function cleanupOldCarts(int $days = 30)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE added_at < NOW() - INTERVAL :days DAY";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['days' => $days]);
        
        return $stmt->rowCount();
    }
    
    /**
     * Verifica el stock disponible para todos los productos en el carrito
     * @param string $token Token del carrito
     * @return array Productos con stock insuficiente [product_id => stock_disponible]
     */
    public function validateCartStock(string $token)
    {
        $sql = "SELECT c.product_id, p.name, c.quantity, p.stock
                FROM {$this->table} c
                JOIN products p ON p.id = c.product_id
                WHERE c.cart_token = :token AND c.quantity > p.stock";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        
        $insufficientStock = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $insufficientStock[$item['product_id']] = [
                'name' => $item['name'],
                'requested' => $item['quantity'],
                'available' => $item['stock']
            ];
        }
        
        return $insufficientStock;
    }
    
    /**
     * Transfiere los productos seleccionados del carrito temporal a una orden
     * @param string $token Token del carrito
     * @param int $orderId ID de la orden creada
     * @return bool Éxito de la operación
     */
    public function moveCartToOrder(string $token, int $orderId)
    {
        try {
            $this->pdo->beginTransaction();
            
            // 1. Obtener items seleccionados
            $items = $this->getSelectedItems($token);
            
            if (empty($items)) {
                throw new Exception("No hay productos seleccionados en el carrito");
            }
            
            // 2. Verificar stock
            $insufficientStock = $this->validateCartStock($token);
            if (!empty($insufficientStock)) {
                throw new Exception("Stock insuficiente para algunos productos");
            }
            
            // 3. Insertar items en order_items
            $sql = "INSERT INTO order_items 
                    (order_id, product_id, price, quantity) 
                    VALUES (:order_id, :product_id, :price, :quantity)";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($items as $item) {
                $stmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity']
                ]);
                
                // 4. Actualizar stock del producto
                $updateStock = "UPDATE products 
                                SET stock = stock - :quantity 
                                WHERE id = :product_id";
                $stmtStock = $this->pdo->prepare($updateStock);
                $stmtStock->execute([
                    'quantity' => $item['quantity'],
                    'product_id' => $item['product_id']
                ]);
            }
            
            // 5. Eliminar items seleccionados del carrito
            $deleteItems = "DELETE FROM {$this->table} 
                           WHERE cart_token = :token AND selected = TRUE";
            $stmtDelete = $this->pdo->prepare($deleteItems);
            $stmtDelete->execute(['token' => $token]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtiene estadísticas de productos populares en los carritos
     * @param int $limit Cantidad máxima de productos a retornar
     * @return array Lista de productos populares con cantidad total en carritos
     */
    public function getPopularCartProducts(int $limit = 10)
    {
        $sql = "SELECT 
                    p.id as product_id,
                    p.name,
                    pi.image_url AS main_image,
                    p.price,
                    SUM(c.quantity) as total_in_carts,
                    COUNT(DISTINCT c.cart_token) as cart_count
                FROM {$this->table} c
                JOIN products p ON p.id = c.product_id
                LEFT JOIN (
                    SELECT product_id, image_url,
                           ROW_NUMBER() OVER (PARTITION BY product_id ORDER BY created_at ASC) as rn
                    FROM product_images
                ) pi ON p.id = pi.product_id AND pi.rn = 1
                GROUP BY p.id, p.name, pi.image_url, p.price
                ORDER BY total_in_carts DESC, cart_count DESC
                LIMIT :limit";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Actualiza todos los elementos del carrito para verificar su disponibilidad
     * @param string $token Token del carrito
     * @return array Productos actualizados (ajustados a stock disponible)
     */
    public function refreshCartItemsStock(string $token)
    {
        try {
            // Obtener productos con stock insuficiente
            $stockCheckSql = "SELECT c.id, c.product_id, c.quantity, p.stock
                              FROM {$this->table} c
                              JOIN products p ON c.product_id = p.id
                              WHERE c.cart_token = :token AND c.quantity > p.stock";
            $stockCheckStmt = $this->pdo->prepare($stockCheckSql);
            $stockCheckStmt->execute(['token' => $token]);
            $itemsToUpdate = $stockCheckStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Actualizar cada producto con stock insuficiente
            if (!empty($itemsToUpdate)) {
                $updateSql = "UPDATE {$this->table} SET quantity = :new_quantity WHERE id = :id";
                $updateStmt = $this->pdo->prepare($updateSql);
                
                foreach ($itemsToUpdate as $item) {
                    $updateStmt->execute([
                        'new_quantity' => $item['stock'],
                        'id' => $item['id']
                    ]);
                }
            }
            
            return $this->getCartItems($token);
        } catch (Exception $e) {
            throw new Exception("Error al actualizar el stock del carrito: " . $e->getMessage());
        }
    }
}
