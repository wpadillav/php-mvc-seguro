<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/models/UserModel.php';

// Datos del nuevo usuario
$username = 'user';
$password = 'Lupa123!!'; // Cambia esto por una contraseña segura

// Crear el usuario
try {
    $db = Database::getInstance()->getConnection();
    $userModel = new UserModel();
    
    if ($userModel->createUser($username, $password)) {
        echo "✅ Usuario creado exitosamente:\n";
        echo "Username: " . $username . "\n";
        echo "Password: " . $password . "\n";
        
        // Opcional: Mostrar el hash y salt (para debug)
        $stmt = $db->prepare("SELECT password_hash, salt FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "Hash: " . $user['password_hash'] . "\n";
        echo "Salt: " . $user['salt'] . "\n";
    } else {
        echo "❌ Error al crear el usuario\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Error de base de datos: " . $e->getMessage() . "\n";
}