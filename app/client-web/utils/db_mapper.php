<?php
/**
 * Utilidades para mapear valores entre el frontend y la base de datos
 * Este archivo contiene constantes y funciones para asegurar la compatibilidad
 * entre la lógica del cliente web y la estructura de la base de datos.
 */

// Constantes para tipos ENUM en la base de datos
class EnumTypes {
    // Tipos de order_status
    const ORDER_STATUS = [
        'PENDING',
        'COMPLETED',
        'CANCELLED'
    ];
    
    // Tipos de payment_method
    const PAYMENT_METHODS = [
        'YAPE',
        'PLIN',
        'TRANSFER',
        'CASH'
    ];
    
    // Tipos de payment_status
    const PAYMENT_STATUS = [
        'PAID',
        'PENDING',
        'FAILED'
    ];
    
    // Tipos de payment_status_enum (extendido)
    const PAYMENT_STATUS_EXTENDED = [
        'PENDING',
        'PAID',
        'FAILED',
        'REFUNDED'
    ];
    
    // Tipos de product_size
    const PRODUCT_SIZES = [
        'XS',
        'S',
        'M',
        'L',
        'XL',
        'XXL',
        'UNIQUE'
    ];
    
    // Tipos de product_status
    const PRODUCT_STATUS = [
        'ACTIVE',
        'INACTIVE',
        'DISCONTINUED',
        'OUT_OF_STOCK',
        'COMING_SOON',
        'ON_SALE'
    ];
    
    // Tipos de role
    const ROLES = [
        'ADMIN',
        'SELLER',
        'CUSTOMER'
    ];
    
    // Tipos de user_status
    const USER_STATUS = [
        'ACTIVE',
        'INACTIVE'
    ];
}

/**
 * Valida que un valor esté en una lista de valores permitidos para un ENUM
 *
 * @param string $value Valor a validar
 * @param array $enumValues Lista de valores permitidos
 * @param string $default Valor por defecto si no es válido
 * @return string Valor validado
 */
function validateEnumValue($value, $enumValues, $default) {
    return in_array($value, $enumValues) ? $value : $default;
}

/**
 * Valida un método de pago
 *
 * @param string $method Método de pago
 * @return string Método de pago validado
 */
function validatePaymentMethod($method) {
    return validateEnumValue($method, EnumTypes::PAYMENT_METHODS, 'CASH');
}

/**
 * Valida un estado de orden
 *
 * @param string $status Estado de orden
 * @return string Estado de orden validado
 */
function validateOrderStatus($status) {
    return validateEnumValue($status, EnumTypes::ORDER_STATUS, 'PENDING');
}

/**
 * Valida un estado de pago
 *
 * @param string $status Estado de pago
 * @return string Estado de pago validado
 */
function validatePaymentStatus($status) {
    return validateEnumValue($status, EnumTypes::PAYMENT_STATUS, 'PENDING');
}

/**
 * Formatea un número para guardarlo como decimal en la base de datos
 *
 * @param float $number Número a formatear
 * @param int $decimals Cantidad de decimales
 * @return float Número formateado
 */
function formatDecimalForDB($number, $decimals = 2) {
    return number_format((float)$number, $decimals, '.', '');
}
