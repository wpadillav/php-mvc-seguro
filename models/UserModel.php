<?php
/**
 * Clase UserModel
 *
 * Gestiona operaciones relacionadas con usuarios, incluyendo autenticación,
 * creación de cuentas y recuperación de datos, interactuando directamente con la base de datos.
 */
class UserModel {
    private $db;

    /**
     * Constructor
     * 
     * Obtiene la instancia compartida de la conexión a base de datos (PDO).
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Autentica a un usuario mediante verificación de hash y salt almacenados.
     * Utiliza PBKDF2 con SHA-256 y libsodium para una comparación segura.
     *
     * @param string $username Nombre de usuario
     * @param string $password Contraseña en texto plano
     * @return array|false Datos del usuario (sin hash ni salt) si es válido, o false si falla
     */
    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("SELECT id, username, password_hash, salt FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            error_log("Usuario no encontrado: $username");
            return false;
        }

        try {
            $storedHash = sodium_hex2bin($user['password_hash']);
            $salt = sodium_hex2bin($user['salt']);

            // Deriva un hash de la contraseña ingresada usando el mismo algoritmo y parámetros
            $inputHash = hash_pbkdf2(
                'sha256',
                $password,
                $salt,
                100000,
                32,
                false
            );
            $inputHashBinary = sodium_hex2bin($inputHash);

            // Comparación segura contra ataques de timing usando sodium_memcmp
            if ($storedHash && $inputHashBinary && 
                sodium_memcmp($storedHash, $inputHashBinary) === 0) {
                
                unset($user['password_hash'], $user['salt']); // Elimina datos sensibles antes de retornar
                return $user;
            }

            error_log("Hash no coincide para usuario: $username");
        } catch (Exception $e) {
            error_log("Error crítico en autenticación: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Obtiene información básica de un usuario por su nombre de usuario.
     *
     * @param string $username
     * @return array|null
     */
    public function getUserByUsername($username) {
        $stmt = $this->db->prepare("SELECT id, username FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo usuario almacenando la contraseña como hash seguro.
     * 
     * Utiliza PBKDF2 con SHA-256 y un salt generado aleatoriamente, almacenado junto al hash.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function createUser($username, $password) {
        $salt = random_bytes(32); // Salt aleatorio único
        $hash = hash_pbkdf2(
            'sha256',
            $password,
            $salt,
            100000,
            32,
            false
        );

        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash, salt) VALUES (:username, :hash, :salt)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hash', $hash);
        $stmt->bindParam(':salt', sodium_bin2hex($salt)); // Almacena el salt como hexadecimal

        return $stmt->execute();
    }
}
