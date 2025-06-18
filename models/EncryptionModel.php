<?php
/**
 * Clase EncryptionModel
 *
 * Proporciona métodos para cifrado y descifrado de texto usando libsodium (criptografía moderna y segura).
 * Utiliza el algoritmo `crypto_secretbox` (basado en XSalsa20-Poly1305), ideal para cifrado simétrico de datos.
 */
class EncryptionModel {
    private $secretKey;

    /**
     * Constructor
     * 
     * Convierte la clave secreta definida en el entorno (.env) desde formato hexadecimal
     * a binario, como requiere la función `sodium_crypto_secretbox()`.
     */
    public function __construct() {
        $this->secretKey = sodium_hex2bin($_ENV['APP_SECRET_KEY']);
    }

    /**
     * Cifra un texto plano.
     * 
     * Genera un nonce aleatorio y retorna el resultado como una cadena en formato hexadecimal:
     * "nonce:ciphertext"
     *
     * @param string $text Texto a cifrar
     * @return string Texto cifrado codificado en hexadecimal
     */
    public function encrypt(string $text): string {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES); // Nonce único por mensaje
        $ciphertext = sodium_crypto_secretbox($text, $nonce, $this->secretKey); // Cifrado simétrico
        return bin2hex($nonce) . ':' . bin2hex($ciphertext); // Se concatena el nonce con el cifrado
    }

    /**
     * Descifra un texto previamente cifrado con el método `encrypt`.
     * 
     * Extrae y convierte el nonce y el ciphertext desde hexadecimal, y realiza el descifrado.
     * Si los datos están corruptos o el descifrado falla, devuelve null.
     *
     * @param string $ciphertext Texto cifrado (nonce:ciphertext)
     * @return string|null Texto descifrado o null si falla
     */
    public function decrypt(string $ciphertext): ?string {
        $parts = explode(':', $ciphertext);
        if (count($parts) !== 2) return null; // Validación básica del formato

        $nonce = hex2bin($parts[0]);
        $ciphertext = hex2bin($parts[1]);

        // Intenta abrir el mensaje cifrado. Devuelve null si no se puede autenticar o descifrar correctamente.
        return sodium_crypto_secretbox_open($ciphertext, $nonce, $this->secretKey) ?: null;
    }
}
