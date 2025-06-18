<?php
/**
 * Carga automática de clases, incluyendo Dotenv para manejar variables de entorno.
 * Se asume que Composer ya ha instalado vlucas/phpdotenv.
 */
require_once __DIR__ . '/../vendor/autoload.php';

// Inicializa y carga las variables de entorno desde el archivo .env ubicado en el directorio raíz del proyecto.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

/**
 * Retorna un arreglo asociativo con los parámetros de conexión a la base de datos.
 * Estos valores son utilizados por el modelo de base de datos para establecer conexión mediante PDO o mysqli.
 * 
 * Ventajas del uso de variables de entorno:
 * - Seguridad: las credenciales no están expuestas en el código fuente.
 * - Portabilidad: diferentes entornos (desarrollo, staging, producción) pueden tener configuraciones distintas.
 */
return [
    'host'     => $_ENV['MYSQL_HOST'],      // Dirección del servidor de base de datos (e.g., localhost, 127.0.0.1, db)
    'username' => $_ENV['MYSQL_USER'],      // Nombre de usuario para conectar a la base de datos
    'password' => $_ENV['MYSQL_PASSWORD'],  // Contraseña de acceso
    'database' => $_ENV['MYSQL_DB'],        // Nombre de la base de datos a utilizar
    'charset'  => 'utf8mb4'                 // Codificación recomendada para soportar caracteres especiales y emojis
];
