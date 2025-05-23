<?php
/**
 * Clase ToolsController
 * 
 * Controlador que gestiona herramientas de cifrado y descifrado de texto.
 * Aplica medidas de seguridad como protección CSRF y limitación de solicitudes (rate limiting).
 */
class ToolsController {
    private $encryptionModel;

    /**
     * Constructor
     * 
     * Inicializa el modelo de cifrado y asegura que la sesión esté iniciada.
     */
    public function __construct() {
        require_once __DIR__ . '/../models/EncryptionModel.php';
        $this->encryptionModel = new EncryptionModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Acción por defecto del controlador
     * 
     * - Verifica autenticación del usuario.
     * - Aplica control de tasa de solicitudes.
     * - Genera token CSRF si no existe.
     * - Procesa datos POST para cifrar o descifrar texto.
     * - Incluye la vista correspondiente.
     */
    public function index() {
        // Solo usuarios autenticados pueden acceder
        if (!isset($_SESSION['user'])) {
            header('Location: /?controller=Auth&action=login');
            exit;
        }

        $this->applyRateLimit();

        // Genera un token CSRF si no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $result = '';
        $ciphertext = '';

        // Manejo de petición POST: cifrado o descifrado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCsrfToken()) {
                die('Token CSRF inválido');
            }

            try {
                if (isset($_POST['text'])) {
                    // Cifrado
                    $text = $this->sanitizeInput($_POST['text']);
                    $ciphertext = $this->encryptionModel->encrypt($text);
                    $result = "Texto cifrado: " . htmlspecialchars($ciphertext);
                } elseif (isset($_POST['ciphertext'])) {
                    // Descifrado
                    $ciphertext = $this->sanitizeInput($_POST['ciphertext']);
                    $decrypted = $this->encryptionModel->decrypt($ciphertext);
                    $result = $decrypted === null 
                        ? "Error al descifrar el texto." 
                        : "Texto descifrado: " . htmlspecialchars($decrypted);
                }
            } catch (Exception $e) {
                error_log("Error en ToolsController: " . $e->getMessage());
                $result = "Ocurrió un error al procesar su solicitud.";
            }
        }

        // Renderiza la vista
        require_once __DIR__ . '/../views/tools/index.php';
    }

    /**
     * Valida que el token CSRF en la solicitud POST sea válido.
     */
    private function validateCsrfToken(): bool {
        return isset($_POST['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    /**
     * Limpia el texto recibido de caracteres potencialmente peligrosos.
     */
    private function sanitizeInput(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    /**
     * Aplica una política de rate limiting basada en la sesión:
     * máximo 10 solicitudes cada 60 segundos.
     */
    private function applyRateLimit() {
        $limit = 10;
        $window = 60;

        if (!isset($_SESSION['tools_requests'])) {
            $_SESSION['tools_requests'] = [
                'count' => 0,
                'start' => time()
            ];
        }

        $data = &$_SESSION['tools_requests'];

        // Reinicia la ventana si se agotó el tiempo
        if (time() - $data['start'] > $window) {
            $data['count'] = 0;
            $data['start'] = time();
        }

        // Bloquea si se excede el límite
        if (++$data['count'] > $limit) {
            die('Demasiadas solicitudes. Por favor, espere un minuto.');
        }
    }
}
