<?php
/**
 * Clase AuthController
 *
 * Controlador encargado de manejar el flujo de autenticación: login, logout, protección CSRF
 * y gestión de intentos fallidos para prevenir fuerza bruta.
 */
class AuthController {
    private $userModel;
    private const MAX_LOGIN_ATTEMPTS = 5;    // Límite de intentos de login fallidos
    private const LOGIN_TIMEOUT = 300;       // Tiempo de bloqueo tras superar el límite (en segundos)

    public function __construct() {
        $this->userModel = new UserModel();
        $this->initializeSession(); // Asegura que la sesión esté iniciada correctamente
    }

    /**
     * Inicializa la sesión con parámetros de seguridad reforzados.
     */
    private function initializeSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 86400,
                'cookie_secure' => true,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict'
            ]);
        }
    }

    /**
     * Controlador de acción para el login.
     * Si es GET, muestra la vista; si es POST, procesa las credenciales.
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->generateCsrfToken();
            $this->showLoginView();
            return;
        }

        $this->validateRequest(); // Valida CSRF y límites de login
        $credentials = $this->getSanitizedCredentials();

        if (!$this->validateCredentials($credentials)) {
            $this->handleFailedLogin();
            return;
        }

        $this->handleSuccessfulLogin($credentials['username']);
    }

    /**
     * Genera un token CSRF único por sesión.
     */
    private function generateCsrfToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Incluye la vista de login con un posible mensaje de error.
     */
    private function showLoginView($error = null) {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Valida que la petición sea legítima:
     * - Verifica el token CSRF.
     * - Revisa si el usuario está temporalmente bloqueado.
     */
    private function validateRequest() {
        if (!$this->isValidCsrfToken()) {
            $this->logSecurityEvent('CSRF token inválido');
            die('Acceso no autorizado');
        }

        if ($this->isLoginBlocked()) {
            $remainingTime = $this->getRemainingBlockTime();
            die("Demasiados intentos. Espere $remainingTime segundos antes de intentar nuevamente.");
        }
    }

    /**
     * Verifica la validez del token CSRF.
     */
    private function isValidCsrfToken() {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    /**
     * Verifica si el usuario ha excedido el límite de intentos y aún está bloqueado.
     */
    private function isLoginBlocked() {
        if (($_SESSION['login_attempts'] ?? 0) >= self::MAX_LOGIN_ATTEMPTS) {
            $lastAttempt = $_SESSION['last_login_attempt'] ?? 0;
            return (time() - $lastAttempt) < self::LOGIN_TIMEOUT;
        }
        return false;
    }

    /**
     * Calcula el tiempo restante de bloqueo.
     */
    private function getRemainingBlockTime() {
        return self::LOGIN_TIMEOUT - (time() - ($_SESSION['last_login_attempt'] ?? 0));
    }

    /**
     * Filtra y normaliza las credenciales ingresadas.
     */
    private function getSanitizedCredentials() {
        return [
            'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING),
            'password' => $_POST['password'] ?? ''
        ];
    }

    /**
     * Verifica que las credenciales no estén vacías y las valida con UserModel.
     */
    private function validateCredentials($credentials) {
        if (empty($credentials['username']) || empty($credentials['password'])) {
            return false;
        }
        return $this->userModel->authenticate($credentials['username'], $credentials['password']);
    }

    /**
     * Manejo de intento fallido:
     * - Aumenta el contador.
     * - Registra evento.
     * - Muestra la vista con mensaje de error.
     */
    private function handleFailedLogin() {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_login_attempt'] = time();

        $this->logSecurityEvent('Intento de login fallido para: ' . ($_POST['username'] ?? ''));
        $this->showLoginView('Credenciales incorrectas');
    }

    /**
     * Manejo de login exitoso:
     * - Regenera el ID de sesión.
     * - Guarda datos del usuario en sesión.
     * - Limpia datos temporales de sesión.
     * - Redirige al panel principal.
     */
    private function handleSuccessfulLogin($username) {
        $user = $this->userModel->authenticate($username, $_POST['password']);

        if (!$user) {
            $this->handleFailedLogin();
            return;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'last_login' => time()
        ];

        unset($_SESSION['csrf_token'], $_SESSION['login_attempts'], $_SESSION['last_login_attempt']);

        $this->redirectAfterLogin();
    }

    /**
     * Redirige a un destino predeterminado tras autenticación.
     * Se podría mejorar validando `$redirect` si fuera dinámico.
     */
    private function redirectAfterLogin() {
        $redirect = filter_input(INPUT_POST, 'redirect', FILTER_SANITIZE_URL);
        $allowedRedirects = ['/dashboard', '/profile', '/tools']; // No se usa aún, puede añadirse seguridad extra

        header('Location: /?controller=Dashboard&action=index');
        exit;
    }

    /**
     * Registra eventos de seguridad en el log del sistema.
     */
    private function logSecurityEvent($message) {
        error_log('Security: ' . $message);
    }

    /**
     * Cierra la sesión y redirige al login.
     */
    public function logout() {
        session_unset();
        session_destroy();

        header('Location: /?controller=Auth&action=login');
        exit;
    }
}
