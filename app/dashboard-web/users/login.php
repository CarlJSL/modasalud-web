<?php
session_start();
require_once __DIR__ . '/../../conexion/db.php';
require_once __DIR__ . '/../model/userBaseModel.php';

use App\Model\BaseModel;

$model = new BaseModel($pdo, 'users');




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    try {
        $username = trim($_POST['txtUsua'] ?? '');
        $password = $_POST['txtContra'] ?? '';

        // Validation
        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son requeridos.']);
            exit;
        }

        // Additional validation for username/email format
        if (strlen($username) < 3) {
            echo json_encode(['success' => false, 'message' => 'El usuario debe tener al menos 3 caracteres.']);
            exit;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
            exit;
        }

        // Find user by username or email
        $user = $model->findByUsernameOrEmail($username);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
            exit;
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
            exit;
        }

        // Check if account is active
        if ($user['status'] !== 'ACTIVE') {
            echo json_encode(['success' => false, 'message' => 'Tu cuenta está inactiva. Contacta al administrador.']);
            exit;
        }

        // Set session variables
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['name'];
        $_SESSION['usuario_email'] = $user['email'];
        $_SESSION['usuario_username'] = $user['username'];
        $_SESSION['usuario_rol'] = $user['role'] ?? 'USER';
        $_SESSION['usuario_role_id'] = $user['role_id'] ?? null;

        echo json_encode([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno del servidor. Intenta nuevamente.']);
    }

    exit();
}
