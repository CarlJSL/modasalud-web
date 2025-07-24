<?php

namespace App\Model;

use PDO;
use Exception;
use PDOException;

class ReportModel
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener reporte general de ventas por período
     */
    public function getSalesReport($dateFrom, $dateTo, $filters = [])
    {
        $sql = "SELECT 
                    COUNT(DISTINCT o.id) as total_orders,
                    SUM(o.total_price) as total_revenue,
                    AVG(o.total_price) as average_ticket,
                    COUNT(DISTINCT o.client_id) as unique_customers,
                    SUM(oi.quantity) as total_items_sold,
                    COUNT(DISTINCT oi.product_id) as unique_products_sold
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN payments p ON o.id = p.order_id
                WHERE o.created_at BETWEEN :date_from AND :date_to
                AND p.status = 'PAID'";

        $params = [
            ':date_from' => $dateFrom . ' 00:00:00',
            ':date_to' => $dateTo . ' 23:59:59'
        ];

        // Agregar filtros adicionales
        if (!empty($filters['payment_method'])) {
            $sql .= " AND p.method = :payment_method";
            $params[':payment_method'] = $filters['payment_method'];
        }

        if (!empty($filters['order_source'])) {
            $sql .= " AND o.order_source = :order_source";
            $params[':order_source'] = $filters['order_source'];
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting sales report: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener ventas por día en el período
     */
    public function getDailySales($dateFrom, $dateTo)
    {
        $sql = "SELECT 
                    o.created_at::date as sale_date,
                    COUNT(DISTINCT o.id) as orders_count,
                    SUM(o.total_price) as daily_revenue,
                    AVG(o.total_price) as avg_ticket,
                    COUNT(DISTINCT o.client_id) as unique_customers
                FROM orders o
                LEFT JOIN payments p ON o.id = p.order_id
                WHERE o.created_at BETWEEN :date_from AND :date_to
                AND p.status = 'PAID'
                GROUP BY o.created_at::date
                ORDER BY sale_date ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':date_from' => $dateFrom . ' 00:00:00',
                ':date_to' => $dateTo . ' 23:59:59'
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting daily sales: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener top productos vendidos
     */
    public function getTopProducts($dateFrom, $dateTo, $limit = 10)
    {
        $sql = "SELECT 
                    p.id,
                    p.name,
                    p.price,
                    c.name as category_name,
                    SUM(oi.quantity) as total_quantity,
                    SUM(oi.quantity * oi.price) as total_revenue,
                    COUNT(DISTINCT o.id) as orders_count,
                    ROUND(AVG(oi.price), 2) as avg_selling_price
                FROM products p
                JOIN order_items oi ON p.id = oi.product_id
                JOIN orders o ON oi.order_id = o.id
                JOIN payments pm ON o.id = pm.order_id
                LEFT JOIN product_category_mapping pcm ON p.id = pcm.product_id
                LEFT JOIN categories c ON pcm.category_id = c.id
                WHERE o.created_at BETWEEN :date_from AND :date_to
                AND pm.status = 'PAID'
                GROUP BY p.id, p.name, p.price, c.name
                ORDER BY total_revenue DESC
                LIMIT :limit";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':date_from', $dateFrom . ' 00:00:00');
            $stmt->bindValue(':date_to', $dateTo . ' 23:59:59');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting top products: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener ventas por categoría
     */
    public function getSalesByCategory($dateFrom, $dateTo)
    {
        // Primero intentamos con la consulta completa
        $sql = "SELECT 
                    c.name as category_name,
                    COUNT(DISTINCT o.id) as orders_count,
                    SUM(oi.quantity) as total_quantity,
                    SUM(oi.quantity * oi.price) as total_revenue,
                    ROUND(AVG(oi.price), 2) as avg_price
                FROM categories c
                INNER JOIN product_category_mapping pcm ON c.id = pcm.category_id
                INNER JOIN products p ON pcm.product_id = p.id
                INNER JOIN order_items oi ON p.id = oi.product_id
                INNER JOIN orders o ON oi.order_id = o.id
                LEFT JOIN payments pm ON o.id = pm.order_id
                WHERE o.created_at BETWEEN :date_from AND :date_to
                AND (pm.status = 'PAID' OR pm.status IS NULL)
                AND p.deleted_at IS NULL
                GROUP BY c.id, c.name
                HAVING SUM(oi.quantity * oi.price) > 0
                ORDER BY total_revenue DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':date_from' => $dateFrom . ' 00:00:00',
                ':date_to' => $dateTo . ' 23:59:59'
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Si no hay resultados, intentamos con una consulta más simple
            if (empty($result)) {
                $simpleSql = "SELECT 
                            c.name as category_name,
                            COUNT(DISTINCT o.id) as orders_count,
                            SUM(oi.quantity) as total_quantity,
                            SUM(oi.quantity * oi.price) as total_revenue,
                            ROUND(AVG(oi.price), 2) as avg_price
                        FROM categories c
                        INNER JOIN product_category_mapping pcm ON c.id = pcm.category_id
                        INNER JOIN products p ON pcm.product_id = p.id
                        INNER JOIN order_items oi ON p.id = oi.product_id
                        INNER JOIN orders o ON oi.order_id = o.id
                        WHERE o.created_at BETWEEN :date_from AND :date_to
                        AND (p.deleted_at IS NULL OR p.deleted_at IS NULL)
                        GROUP BY c.id, c.name
                        HAVING SUM(oi.quantity * oi.price) > 0
                        ORDER BY total_revenue DESC";
                
                $stmt = $this->pdo->prepare($simpleSql);
                $stmt->execute([
                    ':date_from' => $dateFrom . ' 00:00:00',
                    ':date_to' => $dateTo . ' 23:59:59'
                ]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Using simple category query, results: " . json_encode($result));
            }
            
            // Debug: Log la consulta y resultados
            error_log("Sales by category query: " . $sql);
            error_log("Sales by category params: " . json_encode([
                ':date_from' => $dateFrom . ' 00:00:00',
                ':date_to' => $dateTo . ' 23:59:59'
            ]));
            error_log("Sales by category result count: " . count($result));
            error_log("Sales by category result: " . json_encode($result));
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error getting sales by category: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener top clientes
     */
    public function getTopCustomers($dateFrom, $dateTo, $limit = 10)
    {
        $sql = "SELECT 
                    c.id,
                    c.name,
                    c.email,
                    c.phone,
                    COUNT(DISTINCT o.id) as orders_count,
                    SUM(o.total_price) as total_spent,
                    ROUND(AVG(o.total_price), 2) as avg_order_value,
                    MAX(o.created_at) as last_order_date,
                    MIN(o.created_at) as first_order_date
                FROM clients c
                JOIN orders o ON c.id = o.client_id
                JOIN payments p ON o.id = p.order_id
                WHERE o.created_at BETWEEN :date_from AND :date_to
                AND p.status = 'PAID'
                GROUP BY c.id, c.name, c.email, c.phone
                ORDER BY total_spent DESC
                LIMIT :limit";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':date_from', $dateFrom . ' 00:00:00');
            $stmt->bindValue(':date_to', $dateTo . ' 23:59:59');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting top customers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener métodos de pago más utilizados
     */
    public function getPaymentMethodsReport($dateFrom, $dateTo)
    {
        $sql = "SELECT 
                    p.method as payment_method,
                    COUNT(*) as transaction_count,
                    SUM(o.total_price) as total_amount,
                    ROUND(AVG(o.total_price), 2) as avg_amount,
                    ROUND((COUNT(*) * 100.0 / SUM(COUNT(*)) OVER()), 2) as usage_percentage
                FROM payments p
                JOIN orders o ON p.order_id = o.id
                WHERE o.created_at BETWEEN :date_from AND :date_to
                AND p.status = 'PAID'
                GROUP BY p.method
                ORDER BY transaction_count DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':date_from' => $dateFrom . ' 00:00:00',
                ':date_to' => $dateTo . ' 23:59:59'
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting payment methods report: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de inventario
     */
    public function getInventoryReport()
    {
        $sql = "SELECT 
                    p.id,
                    p.name,
                    p.price,
                    p.stock,
                    c.name as category_name,
                    CASE 
                        WHEN p.stock = 0 THEN 'Sin stock'
                        WHEN p.stock <= 5 THEN 'Stock bajo'
                        WHEN p.stock <= 20 THEN 'Stock normal'
                        ELSE 'Stock alto'
                    END as stock_status,
                    COALESCE(SUM(oi.quantity), 0) as total_sold_last_30_days
                FROM products p
                LEFT JOIN product_category_mapping pcm ON p.id = pcm.product_id
                LEFT JOIN categories c ON pcm.category_id = c.id
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id
                LEFT JOIN payments pm ON o.id = pm.order_id
                WHERE (o.created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) OR o.created_at IS NULL)
                AND (pm.status = 'PAID' OR pm.status IS NULL)
                AND p.deleted_at IS NULL
                GROUP BY p.id, p.name, p.price, p.stock, c.name
                ORDER BY p.stock ASC, total_sold_last_30_days DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting inventory report: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener comparación con período anterior
     */
    public function getPeriodComparison($dateFrom, $dateTo)
    {
        // Calcular el período anterior
        $periodDays = (strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24) + 1;
        $previousDateTo = date('Y-m-d', strtotime($dateFrom . ' -1 day'));
        $previousDateFrom = date('Y-m-d', strtotime($previousDateTo . ' -' . ($periodDays - 1) . ' days'));

        // Obtener datos del período actual
        $currentPeriod = $this->getSalesReport($dateFrom, $dateTo);
        
        // Obtener datos del período anterior
        $previousPeriod = $this->getSalesReport($previousDateFrom, $previousDateTo);

        // Calcular porcentajes de cambio
        $comparison = [];
        foreach ($currentPeriod as $key => $currentValue) {
            $previousValue = $previousPeriod[$key] ?? 0;
            
            if ($previousValue > 0) {
                $percentageChange = (($currentValue - $previousValue) / $previousValue) * 100;
            } else {
                $percentageChange = $currentValue > 0 ? 100 : 0;
            }
            
            $comparison[$key] = [
                'current' => $currentValue,
                'previous' => $previousValue,
                'change' => $currentValue - $previousValue,
                'percentage_change' => round($percentageChange, 2)
            ];
        }

        return $comparison;
    }
}
