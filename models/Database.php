<?php
/**
 * Clase Database
 * 
 * Implementa el patrón Singleton para gestionar una única instancia de conexión
 * a la base de datos mediante PDO. Esto permite reutilizar la misma conexión
 * durante el ciclo de vida de la aplicación, optimizando recursos.
 */
class Database {
    private static $instance = null;  // Instancia única del objeto Database (singleton)
    private $connection;              // Objeto PDO que representa la conexión a la base de datos

    /**
     * Constructor privado
     * 
     * Evita que la clase sea instanciada directamente desde fuera (clave del patrón Singleton).
     * Carga la configuración desde el archivo config/database.php y establece la conexión PDO.
     */
    private function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";

        try {
            $this->connection = new PDO($dsn, $config['username'], $config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Manejo de errores como excepciones
        } catch (PDOException $e) {
            // Manejo básico de errores: en producción, se recomienda registrar el error y no mostrar detalles al usuario
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Obtiene la instancia única del objeto Database.
     * Si no existe, la crea.
     *
     * @return Database
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self(); // Crea una nueva instancia si no existe
        }
        return self::$instance;
    }

    /**
     * Devuelve el objeto PDO activo.
     *
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
}
