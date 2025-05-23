<?php
// Carga los archivos de configuración de seguridad y base de datos
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/database.php';

/**
 * Autocargador de clases para controladores y modelos
 * Registra una función anónima que busca clases en los directorios "controllers" y "models"
 */
spl_autoload_register(function ($class) {
    $paths = [
        'controllers/' . $class . '.php',
        'models/' . $class . '.php'
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

/**
 * Punto de entrada principal del sistema.
 * Determina el controlador y la acción a ejecutar en función de los parámetros GET.
 * Si no hay parámetros, redirige según el estado de sesión del usuario.
 */
if (empty($_GET['controller']) && empty($_GET['action'])) {
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si el usuario ya tiene una sesión activa, lo redirige al dashboard
    if (isset($_SESSION['username'])) {
        header("Location: /?controller=Dashboard&action=index");
        exit();
    } else {
        // Si no hay sesión activa, redirige al formulario de login
        header("Location: /?controller=Auth&action=login");
        exit();
    }
}

// Obtiene el controlador y la acción desde los parámetros GET, con valores por defecto
$action = $_GET['action'] ?? 'login';
$controller = $_GET['controller'] ?? 'Auth';

// Forma el nombre de la clase del controlador siguiendo convención: NombreController
$controllerClass = ucfirst($controller) . 'Controller';

// Verifica si la clase del controlador existe
if (class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass();

    // Verifica si el método (acción) existe en la clase del controlador
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action(); // Ejecuta la acción
    } else {
        // Acción no encontrada en el controlador
        header("HTTP/1.0 404 Not Found");
        echo "Action not found";
    }
} else {
    // Controlador no encontrado
    header("HTTP/1.0 404 Not Found");
    echo "Controller not found";
}
